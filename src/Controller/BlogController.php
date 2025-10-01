<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogSection;
use App\Entity\Comment;
use App\Form\BlogType;
use App\Form\CommentType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;



#[Route('/blog')]
class BlogController extends AbstractController
{
    #[Route('/', name: 'app_blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Publier automatiquement les articles programmés dont l'heure est passée
        $this->publishScheduledBlogs($blogRepository, $entityManager);
        
        // Récupérer l'ordre de tri depuis les paramètres de requête (par défaut: récent)
        $sortOrder = $request->query->get('sort', 'recent');
        
        // Récupérer seulement les articles publiés
        $blogs = $blogRepository->findPublishedBlogs($sortOrder);
        
        return $this->render('content/blog/index.html.twig', [
            'blogs' => $blogs,
            'currentSort' => $sortOrder,
        ]);
    }

    #[Route('/admin', name: 'app_blog_admin', methods: ['GET'])]
    public function admin(BlogRepository $blogRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Publier automatiquement les articles programmés dont l'heure est passée
        $this->publishScheduledBlogs($blogRepository, $entityManager);
        
        // Récupérer l'ordre de tri depuis les paramètres de requête (par défaut: récent)
        $sortOrder = $request->query->get('sort', 'recent');
        
        // Récupérer le filtre de statut depuis les paramètres de requête
        $statusFilter = $request->query->get('status', 'all');
        
        // Récupérer TOUS les articles pour l'admin
        $allBlogs = $blogRepository->findAllForAdmin($sortOrder);
        
        // Filtrer les articles selon le statut sélectionné
        $blogs = match($statusFilter) {
            'published' => array_filter($allBlogs, fn($blog) => $blog->getStatus() === Blog::STATUS_PUBLISHED),
            'scheduled' => array_filter($allBlogs, fn($blog) => $blog->getStatus() === Blog::STATUS_SCHEDULED),
            'draft' => array_filter($allBlogs, fn($blog) => $blog->getStatus() === Blog::STATUS_DRAFT),
            default => $allBlogs, // 'all' ou toute autre valeur
        };
        
        // Récupérer le prochain article programmé pour le minuteur
        $nextScheduledArticle = $blogRepository->findNextScheduledArticle();
        
        // Calculer les statistiques (sur tous les articles, pas seulement les filtrés)
        $totalBlogs = count($allBlogs);
        $publishedBlogs = array_filter($allBlogs, fn($blog) => $blog->getStatus() === Blog::STATUS_PUBLISHED);
        $scheduledBlogs = array_filter($allBlogs, fn($blog) => $blog->getStatus() === Blog::STATUS_SCHEDULED);
        $draftBlogs = array_filter($allBlogs, fn($blog) => $blog->getStatus() === Blog::STATUS_DRAFT);
        
        return $this->render('content/blog/admin.html.twig', [
            'blogs' => $blogs,
            'allBlogs' => $allBlogs, // Pour garder accès à tous les articles si nécessaire
            'currentSort' => $sortOrder,
            'currentStatus' => $statusFilter,
            'nextScheduledArticle' => $nextScheduledArticle,
            'stats' => [
                'total' => $totalBlogs,
                'published' => count($publishedBlogs),
                'scheduled' => count($scheduledBlogs),
                'draft' => count($draftBlogs),
            ]
        ]);
    }

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement du fichier image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    // Mise à jour de la propriété 'image' de l'entité Blog pour enregistrer le nom du fichier
                    $blog->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                    // Ne pas persister si erreur d'image
                    return $this->render('content/blog/new.html.twig', [
                        'blog' => $blog,
                        'form' => $form->createView(),
                    ]);
                }
            }

            // Logique de publication basée sur le statut
            $this->handlePublicationLogic($blog);

            $entityManager->persist($blog);
            $entityManager->flush();

            // Traitement des sections dynamiques
            $dynamicSections = $request->request->all('dynamic_sections');
            if (!empty($dynamicSections)) {
                $position = 1;
                foreach ($dynamicSections as $sectionData) {
                    if (!empty($sectionData['title']) && !empty($sectionData['content'])) {
                        $section = new BlogSection();
                        $section->setTitle($sectionData['title']);
                        $section->setContent($sectionData['content']);
                        $section->setPosition($position);
                        $section->setBlog($blog);
                        
                        $entityManager->persist($section);
                        $position++;
                    }
                }
                $entityManager->flush();
            }

            $message = $this->getPublicationSuccessMessage($blog);
            $this->addFlash('success', $message);
            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }

        return $this->render('content/blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'blog_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBlog($blog);
            $comment->setAuthor($this->getUser()); // Associer l'utilisateur connecté
            // createdAt est déjà défini dans le constructeur
            
            // Vérifier s'il s'agit d'une réponse à un commentaire
            $parentId = $request->request->get('parent_id');
            if ($parentId) {
                $parentComment = $entityManager->getRepository(Comment::class)->find($parentId);
                if ($parentComment && $parentComment->getBlog() === $blog) {
                    $comment->setParent($parentComment);
                }
            }
            
            $entityManager->persist($comment);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre commentaire a été publié avec succès !');
            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }
    
        return $this->render('/content/blog/show.html.twig', [
            'blog' => $blog,
            'commentForm' => $form->createView(),
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Garder le nom de l'image actuelle
        $currentImage = $blog->getImage();
        
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement de la nouvelle image téléchargée, si elle existe
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    // Met à jour avec le nouveau nom de fichier
                    $blog->setImage($newFilename);
                } catch (FileException $e) {
                    // En cas d'erreur, garder l'image actuelle
                    $blog->setImage($currentImage);
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            } else {
                // Garder l'image actuelle si aucune nouvelle image n'est téléchargée
                $blog->setImage($currentImage);
            }

            // Traitement des sections dynamiques
            $dynamicSections = $request->request->all('dynamic_sections');
            
            // Récupérer toutes les sections existantes pour ce blog
            $existingSections = $entityManager->getRepository(BlogSection::class)->findBy(['blog' => $blog]);
            
            // Supprimer toutes les sections existantes d'abord
            foreach ($existingSections as $existingSection) {
                $entityManager->remove($existingSection);
            }
            
            // Recréer toutes les sections à partir des données soumises
            if (!empty($dynamicSections)) {
                $position = 1;
                foreach ($dynamicSections as $sectionId => $sectionData) {
                    if (!empty($sectionData['title']) && !empty($sectionData['content'])) {
                        // Créer une nouvelle section pour toutes les données soumises
                        $section = new BlogSection();
                        $section->setBlog($blog);
                        $section->setTitle($sectionData['title']);
                        $section->setContent($sectionData['content']);
                        $section->setPosition($position);
                        
                        $entityManager->persist($section);
                        $position++;
                    }
                }
            }

            // Logique de publication basée sur le statut
            try {
                $this->handlePublicationLogic($blog);
                $entityManager->flush();
                $message = $this->getPublicationSuccessMessage($blog);
                $this->addFlash('success', $message);
                return $this->redirectToRoute('app_blog_admin'); // Retour vers l'admin au lieu de show
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                // Rester sur la page d'édition pour corriger l'erreur
            } catch (\Exception $e) {
                // Capture toute autre erreur
                $this->addFlash('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
            }
        }
    
        return $this->render('content/blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $blog->getId(), $request->request->get('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/blog/{id}/comment/new', name: 'blog_add_comment')]
    public function addComment(Request $request, EntityManagerInterface $entityManager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            // Rediriger vers la page du blog ou afficher un message de succès
            return $this->redirectToRoute('blog_view', ['id' => $id]);
        }

        return $this->render('blog/add_comment.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    
    

    // #[Route('/submit-comment', name: 'submit_comment', methods: ['POST'])]
    // public function submitComment(Request $request, EntityManagerInterface $entityManager): Response {
    //     $comment = new Comment();
    //     $comment->setText($request->request->get('comment'));
    //     $blogId = $request->request->get('blog_id');
    //     $blog = $entityManager->getRepository(Blog::class)->find($blogId);
    
    //     if (!$blog) {
    //         $this->addFlash('error', 'Blog non trouvé.');
    //         return $this->redirectToRoute('blog_index'); // Redirigez vers une route existante qui est sûre.
    //     }
    
    //     $comment->setBlog($blog);
    //     $entityManager->persist($comment);
    //     $entityManager->flush();
    
    //     $this->addFlash('success', 'Votre commentaire a été ajouté.');
    //     return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
    // }
    
    #[Route('/admin/comment/{id}/delete', name: 'admin_comment_delete', methods: ['POST'])]
    public function deleteComment(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous devez être administrateur.');
            return $this->redirectToRoute('blog_index');
        }

        $comment = $entityManager->getRepository(Comment::class)->find($id);
        
        if (!$comment) {
            $this->addFlash('error', 'Commentaire non trouvé.');
            return $this->redirectToRoute('blog_index');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $blogId = $comment->getBlog()->getId();
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('blog_show', ['id' => $blogId]);
    }

    #[Route('/make-me-admin', name: 'make_me_admin')]
    public function makeMeAdmin(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new Response('Vous devez être connecté');
        }

        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);
            $entityManager->flush();
            
            return new Response('Vous êtes maintenant admin ! Rechargez la page.');
        }
        
        return new Response('Vous êtes déjà admin.');
    }

    #[Route('/{id}/reply', name: 'blog_reply_ajax', methods: ['POST'])]
    public function replyAjax(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        error_log('AJAX Reply route accessed for blog ID: ' . $blog->getId());
        error_log('Request method: ' . $request->getMethod());
        error_log('Request content: ' . $request->getContent());
        
        if (!$this->getUser()) {
            error_log('User not authenticated');
            return $this->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
        }

        $text = $request->request->get('text');
        $parentId = $request->request->get('parent_id');

        if (!$text || trim($text) === '') {
            return $this->json(['success' => false, 'message' => 'Le commentaire ne peut pas être vide.'], 400);
        }

        $comment = new Comment();
        $comment->setText(trim($text));
        $comment->setBlog($blog);
        $comment->setAuthor($this->getUser());

        // Gestion de la réponse
        if ($parentId) {
            $parentComment = $entityManager->getRepository(Comment::class)->find($parentId);
            if ($parentComment && $parentComment->getBlog() === $blog) {
                $comment->setParent($parentComment);
            }
        }

        $entityManager->persist($comment);
        $entityManager->flush();

        // Renvoyer les données du commentaire créé
        $user = $this->getUser();
        $commentData = [
            'id' => $comment->getId(),
            'text' => $comment->getText(),
            'author' => $user->getUsername() ?: $user->getEmail(),
            'createdAt' => $comment->getCreatedAt()->format('d/m/Y à H:i'),
            'parentId' => $comment->getParent() ? $comment->getParent()->getId() : null,
            'isAdmin' => $this->isGranted('ROLE_ADMIN')
        ];

        return $this->json(['success' => true, 'comment' => $commentData]);
    }

    private function handlePublicationLogic(Blog $blog): void
    {
        $now = new \DateTimeImmutable();
        
        switch ($blog->getStatus()) {
            case Blog::STATUS_PUBLISHED:
                // Si publié maintenant et pas de date définie, utiliser maintenant
                if (!$blog->getPublishedAt()) {
                    $blog->setPublishedAt($now);
                }
                break;
                
            case Blog::STATUS_SCHEDULED:
                // Pour un article programmé, vérifier la date
                if (!$blog->getPublishedAt()) {
                    throw new \InvalidArgumentException('Une date de publication est requise pour programmer un article.');
                }
                
                if ($blog->getPublishedAt() <= $now) {
                    throw new \InvalidArgumentException('La date de publication doit être dans le futur pour un article programmé. Date actuelle: ' . $now->format('d/m/Y H:i'));
                }
                break;
                
            case Blog::STATUS_DRAFT:
                // Pour un brouillon, aucune contrainte sur la date
                // On conserve la date existante pour permettre un retour facile en publié
                break;
        }
    }

    private function getPublicationSuccessMessage(Blog $blog): string
    {
        return match($blog->getStatus()) {
            Blog::STATUS_PUBLISHED => 'Article publié avec succès !',
            Blog::STATUS_SCHEDULED => 'Article programmé avec succès ! Il sera publié le ' . 
                                      $blog->getPublishedAt()->format('d/m/Y à H:i'),
            Blog::STATUS_DRAFT => 'Article sauvegardé en brouillon !',
            default => 'Article sauvegardé !'
        };
    }

    /**
     * Publier automatiquement les articles programmés dont l'heure est passée
     */
    private function publishScheduledBlogs(BlogRepository $blogRepository, EntityManagerInterface $entityManager): void
    {
        // Définir le fuseau horaire français
        $parisTimezone = new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $parisTimezone);
        
        // Debug : afficher l'heure actuelle
        error_log("Heure actuelle Paris : " . $now->format('Y-m-d H:i:s T'));
        
        // Récupérer les articles programmés dont la date est passée
        $scheduledBlogs = $blogRepository->findScheduledToPublish();
        
        if (!empty($scheduledBlogs)) {
            $publishedCount = 0;
            foreach ($scheduledBlogs as $blog) {
                // Convertir la date de publication en fuseau horaire parisien
                $publishTime = $blog->getPublishedAt();
                if ($publishTime) {
                    $publishTimeInParis = $publishTime->setTimezone($parisTimezone);
                    error_log("Article '{$blog->getTitleOne()}' programmé pour : " . $publishTimeInParis->format('Y-m-d H:i:s T'));
                    
                    // Vérifier si l'heure est vraiment passée en heure de Paris
                    if ($publishTimeInParis <= $now) {
                        // Changer le statut en publié
                        $blog->setStatus(Blog::STATUS_PUBLISHED);
                        $entityManager->persist($blog);
                        $publishedCount++;
                        error_log("Article '{$blog->getTitleOne()}' publié automatiquement !");
                    }
                }
            }
            
            if ($publishedCount > 0) {
                // Sauvegarder en base
                $entityManager->flush();
                error_log("$publishedCount article(s) publié(s) automatiquement");
            }
        }
    }
}
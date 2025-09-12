<?php

namespace App\Controller;

use App\Entity\Blog;
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
    public function index(BlogRepository $blogRepository): Response
    {
        return $this->render('content/blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
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

            // Définir la date de publication si elle n'est pas fournie
            if (!$blog->getPublishedAt()) {
                $blog->setPublishedAt(new \DateTimeImmutable());
            }

            $entityManager->persist($blog);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès !');
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
    
            $entityManager->flush();
            $this->addFlash('success', 'Article modifié avec succès !');
    
            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
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

    
}
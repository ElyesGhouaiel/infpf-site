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
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant le téléchargement du fichier
                }

                // Mise à jour de la propriété 'image' de l'entité Blog pour enregistrer le nom du fichier
                $blog->setImage($newFilename);
            }

            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('content/blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'blog_show')]
    public function show(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBlog($blog);
            $entityManager->persist($comment);
            $entityManager->flush();
    
            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }
    
        return $this->render('/content/blog/show.html.twig', [
            'blog' => $blog,
            'likes' => $blog->getLikes(), // Assurez-vous que cette méthode existe dans votre entité Blog
            'commentForm' => $form->createView(),
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Si l'entité blog avait déjà une image, convertissons-la en objet File pour que le formulaire la traite correctement
        if ($blog->getImage()) {
            $blog->setImage(
                new File($this->getParameter('images_directory').'/'.$blog->getImage())
            );
        }
    
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
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant le téléchargement du fichier
                }
    
                // Met à jour la propriété 'image' avec le nouveau nom de fichier
                $blog->setImage($newFilename);
            } else {
                // Remet l'image précédente si pas de nouvelle image téléchargée
                if ($form->get('image')->isEmpty()) {
                    $blog->setImage(null);
                }
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('content/blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
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
    

}
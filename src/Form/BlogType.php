<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Importer FileType
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\CollectionType; // Importer pour gérer des collections de formes
use App\Form\CommentType;


class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title_one', TextType::class, [
            'label' => 'Main Title',
            'attr' => [
                'placeholder' => 'Enter the main title here...',
                'class' => 'custom-class-for-title',
            ]
        ])
        ->add('publishedAt', DateTimeType::class, [
            'widget' => 'single_text',
            'label' => 'Publication Date',
            // Configurez les options selon vos besoins
        ])
        ->add('author', TextType::class, [
            'label' => 'Author',
            'attr' => [
                'placeholder' => 'Author name',
            ]
        ])
        ->add('content_one', TextareaType::class, [
            'label' => 'Main Content',
            'attr' => [
                'placeholder' => 'Write the main content here...',
                'rows' => 5,
            ]
        ])
        ->add('title_two', TextType::class, [
            'label' => 'Secondary Title',
            'attr' => ['placeholder' => 'Enter the secondary title here...']
        ])
        ->add('content_two', TextareaType::class, [
            'label' => 'Secondary Content',
            'attr' => [
                'placeholder' => 'Write the secondary content here...',
                'rows' => 5,
            ]
        ])
        ->add('image', FileType::class, [
            'label' => 'Blog Image',
            'mapped' => false, // Indique que ce champ n'est pas directement lié à une propriété de l'entité
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '5M',  // Augmente la taille maximale des fichiers à 5MB
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'image/gif',
                        'image/svg+xml'  // Ajout du support pour les images SVG
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger un fichier image valide.',
                ])
            ],
        ])

        ->add('title_tree', TextType::class, [
            'label' => 'Tertiary Title',
            'attr' => [
                'placeholder' => 'Enter the tertiary title here...',
                'class' => 'custom-class-for-title',
            ]
        ])
        ->add('content_tree', TextareaType::class, [
            'label' => 'Tertiary Content',
            'attr' => [
                'placeholder' => 'Write the tertiary content here...',
                'rows' => 5,
            ]
        ])
        ->add('sous_title_tree', TextType::class, [
            'label' => 'Sub-title for Tertiary Content',
            'attr' => ['placeholder' => 'Enter the sub-title for tertiary content here...']
        ])
        ->add('sous_content_tree', TextareaType::class, [
            'label' => 'Sub-content for Tertiary Content',
            'attr' => [
                'placeholder' => 'Write the sub-content for tertiary content here...',
                'rows' => 5,
            ]
        ])
        ->add('title_for', TextType::class, [
            'label' => 'Quaternary Title',
            'attr' => ['placeholder' => 'Enter the quaternary title here...']
        ])
        ->add('content_for', TextareaType::class, [
            'label' => 'Quaternary Content',
            'attr' => [
                'placeholder' => 'Write the quaternary content here...',
                'rows' => 5,
            ]
        ])
        
        ->add('likes', NumberType::class, [
            'label' => 'Number of Likes',
            'required' => false, // Rendre le champ facultatif
        ])
        ->add('comments', CollectionType::class, [
            'entry_type' => CommentType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
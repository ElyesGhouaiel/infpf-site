<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;


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
        ->add('status', ChoiceType::class, [
            'label' => 'Statut de publication',
            'choices' => [
                'Brouillon (non publié)' => Blog::STATUS_DRAFT,
                'Publier maintenant' => Blog::STATUS_PUBLISHED,
                'Programmer la publication' => Blog::STATUS_SCHEDULED,
            ],
            // Pas de 'data' par défaut pour permettre au formulaire d'utiliser la valeur de l'entité
            'attr' => [
                'class' => 'publication-status-select',
                'onchange' => 'togglePublicationDate(this.value)'
            ]
        ])
        ->add('publishedAt', DateTimeType::class, [
            'widget' => 'single_text',
            'label' => 'Date et heure de publication',
            'required' => false,
            'help' => 'Laissez vide pour publier immédiatement, ou choisissez une date future pour programmer la publication',
            'attr' => [
                'class' => 'publication-date-field',
                'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                'step' => '60'
            ]
        ])
        ->add('author', TextType::class, [
            'label' => 'Author',
            'required' => false,
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
            'required' => false,
            'attr' => ['placeholder' => 'Enter the secondary title here...']
        ])
        ->add('content_two', TextareaType::class, [
            'label' => 'Secondary Content',
            'required' => false,
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
            'label' => 'Third Section Title',
            'required' => false,
            'attr' => [
                'placeholder' => 'Enter the third section title here...',
                'class' => 'custom-class-for-title',
            ]
        ])
        ->add('content_tree', TextareaType::class, [
            'label' => 'Third Section Content',
            'required' => false,
            'attr' => [
                'placeholder' => 'Write the third section content here...',
                'rows' => 5,
            ]
        ])
        ->add('title_for', TextType::class, [
            'label' => 'Fourth Section Title',
            'required' => false,
            'attr' => ['placeholder' => 'Enter the fourth section title here...']
        ])
        ->add('content_for', TextareaType::class, [
            'label' => 'Fourth Section Content',
            'required' => false,
            'attr' => [
                'placeholder' => 'Write the fourth section content here...',
                'rows' => 5,
            ]
        ])
        ->add('title_five', TextType::class, [
            'label' => 'Fifth Section Title',
            'required' => false,
            'attr' => ['placeholder' => 'Enter the fifth section title here...']
        ])
        ->add('content_five', TextareaType::class, [
            'label' => 'Fifth Section Content',
            'required' => false,
            'attr' => [
                'placeholder' => 'Write the fifth section content here...',
                'rows' => 5,
            ]
        ])
        ->add('shortDesc', TextareaType::class, [
            'label' => 'Description courte',
            'required' => false,
            'attr' => [
                'placeholder' => 'Rédigez une description courte pour l\'aperçu...',
                'rows' => 3,
            ]
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
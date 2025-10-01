<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Formation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType; // Ajouté pour le prix
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameFormation', TextType::class, [
                'label' => 'Nom de la formation',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            
            ->add('descriptionFormation', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('dureeFormation', TextType::class, [
                'label' => 'Durée (en heures)',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('priceFormation', NumberType::class, [
                'label' => 'Prix',
                'attr' => ['class' => 'form-control mb-3'],
            ])            
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-select mb-3'],
            ])
            ->add('phraseOne', TextareaType::class, [
                'label' => 'Phrase One',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('presentation', TextareaType::class, [
                'label' => 'Présentation',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('prerequis', TextareaType::class, [
                'label' => 'Prérequis',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'rows' => 6,
                    'placeholder' => 'Séparez chaque prérequis par une ligne. Utilisez • ou - pour les listes.'
                ],
            ])
            ->add('atouts', TextareaType::class, [
                'label' => 'Atouts',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'rows' => 8,
                    'placeholder' => 'Séparez chaque atout par une ligne. Utilisez • ou - pour les listes.'
                ],
            ])
            ->add('programme', TextareaType::class, [
                'label' => 'Programme',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'rows' => 15,
                    'placeholder' => 'Décrivez le programme détaillé. Séparez les modules/parties par des lignes vides.'
                ],
            ])
            ->add('modalites_pedagogique', TextareaType::class, [
                'label' => 'Modalités Pédagogiques',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('evaluation', TextareaType::class, [
                'label' => 'Évaluation',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('niveau', TextType::class, [
                'label' => 'Niveau',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('langue', TextType::class, [
                'label' => 'Langue',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('rncp', TextType::class, [
                'label' => 'Code RNCP',
                'required' => false, // En fonction de si le champ est obligatoire ou non
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu de formation',
                'required' => false,
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('bloc', TextType::class, [
                'label' => 'Bloc de compétences',
                'required' => false,
                'attr' => ['class' => 'form-control mb-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
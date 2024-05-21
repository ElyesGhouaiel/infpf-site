<?php

// src/Form/FilterFormType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('niveau', ChoiceType::class, [
                'choices' => $options['niveaux'],
                'placeholder' => 'Tous les niveaux',
                'required' => false,
            ])
            ->add('langue', ChoiceType::class, [
                'choices' => $options['langues'],
                'placeholder' => 'Toutes les langues',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Filtrer']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'niveaux' => [],
            'langues' => [],
        ]);
    }
}
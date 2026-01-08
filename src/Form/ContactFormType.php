<?php
// src/Form/ContactFormType.php
namespace App\Form;

use App\Entity\Message;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(["message" => "Le nom ne peut pas être vide."])
                ],
                'label' => 'Nom complet *',
                'attr' => ['class' => 'custom-input', 'placeholder' => 'Prénom et nom']
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(["message" => "L'email ne peut pas être vide."]),
                    new Email(["message" => "L'email n'est pas valide."])
                ],
                'label' => 'Email *',
                'attr' => ['class' => 'custom-input', 'placeholder' => 'votre.email@exemple.com']
            ])
            ->add('numero', TextType::class, [
                'constraints' => [
                    new NotBlank(["message" => "Le téléphone ne peut pas être vide."])
                ],
                'label' => 'Téléphone *',
                'attr' => ['class' => 'custom-input', 'placeholder' => '06 12 34 56 78']
            ])
            ->add('formationId', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'nameFormation',
                'placeholder' => 'Sélectionnez une formation',
                'required' => false,
                'label' => 'Formation concernée',
                'attr' => ['class' => 'custom-select'],
                'mapped' => false, // On va gérer manuellement
            ])
            ->add('requestType', ChoiceType::class, [
                'choices' => [
                    'Je souhaite des renseignements' => 'renseignement',
                    'Je souhaite un devis pour cette formation' => 'devis',
                ],
                'label' => 'Objet de votre demande *',
                'expanded' => true, // Radio buttons
                'attr' => ['class' => 'custom-radio'],
                'constraints' => [
                    new NotBlank(["message" => "Veuillez sélectionner l'objet de votre demande."])
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Détails supplémentaires',
                'required' => false,
                'attr' => [
                    'class' => 'custom-textarea', 
                    'placeholder' => 'Parlez-nous de votre projet professionnel, vos objectifs, vos questions...',
                    'rows' => 5
                ]
            ]);
            // CAPTCHA gere manuellement en JavaScript (lazy load)
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'allow_extra_fields' => true, // Permet les anciens champs (cache navigateur)
        ]);
    }
}
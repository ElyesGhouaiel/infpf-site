<?php
// src/Form/ContactFormType.php
namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(["message" => "Le nom ne peut pas être vide."])
                ],
                'label' => 'Votre nom*:',
                'attr' => ['class' => 'custom-input', 'placeholder' => 'Nom complet']
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(["message" => "L'email ne peut pas être vide."]),
                    new Email(["message" => "L'email n'est pas valide."])
                ],
                'label' => 'Votre email*:',
                'attr' => ['class' => 'custom-input', 'placeholder' => 'exemple@domaine.com']
            ])
            ->add('numero', TextType::class, [
                'label' => 'Votre téléphone:',
                'attr' => ['class' => 'custom-input', 'placeholder' => 'Numéro de téléphone']
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new NotBlank(["message" => "Le message ne peut pas être vide."])
                ],
                'label' => 'Votre message*:',
                'attr' => ['class' => 'custom-textarea', 'placeholder' => 'Écrivez votre message ici...']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
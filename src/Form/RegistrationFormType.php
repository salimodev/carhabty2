<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; 
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, array('label'=>false))
            ->add('prenom', null, array('label'=>false))
           ->add('adresse', ChoiceType::class, [
        'choices' => [
            'Grand Tunis'          => 'Grand Tunis',
            'Sousse'         => 'Sousse',
            'Monastir'       => 'Monastir',
            'Mahdia'         => 'Mahdia',
            'Sfax'           => 'Sfax',
            'Kairouan'       => 'Kairouan',
            'Kasserine'      => 'Kasserine',
            'Sidi Bouzid'    => 'Sidi Bouzid',
            'Gabès'          => 'Gabès',
            'Médenine'       => 'Médenine',
            'Tataouine'      => 'Tataouine',
            'Gafsa'          => 'Gafsa',
            'Tozeur'         => 'Tozeur',
            'Kébili'         => 'Kébili',
            'Bizerte'        => 'Bizerte',
            'Beja'           => 'Beja',
            'Jendouba'       => 'Jendouba',
            'Le Kef'         => 'Le Kef',
            'Nabeul'         => 'Nabeul',
            'Zaghouan'       => 'Zaghouan',
            'Siliana'        => 'Siliana',
        ],
        'placeholder' => 'Sélectionnez votre zone de couverture',
        'label' => false,
        'expanded' => false,  // liste déroulante
        'multiple' => false,  // un seul choix
    ])
           ->add('roles', ChoiceType::class, [
    'choices'  => [
        'Propriétaire'    => 'ROLE_PROPRIETAIRE',
        'Vendeur des piéces neuves' => 'ROLE_VENDEUR_NEUF',
        'Mécanicien' => 'ROLE_MECANICIEN',
        'Vendeur des piéces occasion' => 'ROLE_VENDEUR_OCCASION',
        'Particulier' => 'ROLE_PARTICULIER'
    ],
     'placeholder' => 'Choisissez votre rôle',
    'multiple' => false,
    'expanded' => false,
    'label'    => false,
    'mapped'   => false, 
])
            ->add('tel1', null, [
    'label' => false,
    'constraints' => [
        new Assert\Length([
            'min' => 8,
            'max' => 8,
            'exactMessage' => 'Le numéro de téléphone doit contenir exactement {{ limit }} chiffres.',
        ]),
        new Assert\Regex([
            'pattern' => '/^[0-9]+$/',
            'message' => 'Le numéro de téléphone doit contenir uniquement des chiffres.',
        ]),
    ],
])
->add('tel2', null, [
    'label' => false,
    'required' => false,
    'constraints' => [
        new Assert\Length([
            'min' => 8,
            'max' => 8,
            'exactMessage' => 'Le numéro de téléphone doit contenir exactement {{ limit }} chiffres.',
        ]),
        new Assert\Regex([
            'pattern' => '/^[0-9]+$/',
            'message' => 'Le numéro de téléphone doit contenir uniquement des chiffres.',
        ]),
    ],
])
           ->add('email', EmailType::class, [
        'label' => false,
        'constraints' => [
            new NotBlank(['message' => 'Veuillez entrer une adresse e-mail.']),
            new Email(['message' => 'Veuillez entrer une adresse e-mail valide.'])
        ]
    ])
             ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'attr' => [
        'class' => 'form-control rounded-pill password-field',
        'placeholder' => 'Mot de passe',
    ],
                'label'=>false,
                'mapped' => false,
               // 'options' => ['attr' =>['class'=>'form-control','placeholder'=>'Mot de passe']],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entre votre mot de passe ',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'minimum 6 characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'first_options' => ['label' => false,'attr' =>['class'=>'form-control rounded-pill','placeholder'=>'Mot de passe', 'style'=>'background-color: #dfdfdf;margin-bottom: 16px;']],
                'second_options' => ['label' => false,'attr' =>['class'=>'form-control rounded-pill','placeholder'=>'Confirmer votre mot de passe', 'style'=>'background-color: #dfdfdf;']],
                'invalid_message' => 'votre mot de passe de confirmation incorrect'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}

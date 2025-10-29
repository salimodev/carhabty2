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

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, array('label'=>false))
            ->add('prenom', null, array('label'=>false))
           ->add('adresse', ChoiceType::class, [
        'choices' => [
            'Tunis'          => 'Tunis',
            'Ariana'         => 'Ariana',
            'Ben Arous'      => 'Ben Arous',
            'Manouba'        => 'Manouba',
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
        'placeholder' => 'Sélectionnez votre ville',
        'label' => false,
        'expanded' => false,  // liste déroulante
        'multiple' => false,  // un seul choix
    ])
           ->add('roles', ChoiceType::class, [
    'choices'  => [
        'Proprietaire'    => 'ROLE_PROPRIETAIRE',
        'Mecanicien' => 'ROLE_MECANICIEN',
        'Vendeur Neuf' => 'ROLE_VENDEUR_NEUF',
        'Vendeur Occasion' => 'ROLE_VENDEUR_OCCASION',
        'Particulier' => 'ROLE_PARTICULIER'
    ],
    'multiple' => false,
    'expanded' => false,
    'label'    => false,
    'mapped'   => false, 
])
            ->add('tel1', null, array('label'=>false))
            ->add('tel2', null, array('label'=>false))
            ->add('email',null, array('label'=>false))
             ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
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
                'first_options' => ['label' => false,'attr' =>['class'=>'form-control','placeholder'=>'Mot de passe', 'style'=>'margin-bottom: 16px;']],
                'second_options' => ['label' => false,'attr' =>['class'=>'form-control','placeholder'=>'Confirmer votre mot de passe']],
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

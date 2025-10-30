<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', RepeatedType::class, [
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
            'first_options' => ['label' => false,'attr' =>['class'=>'form-control','placeholder'=>'Nouveau mot de passe', 'style'=>'background-color: #dfdfdf;margin-bottom: 16px;']],
            'second_options' => ['label' => false,'attr' =>['class'=>'form-control','placeholder'=>'Confirmer votre mot de passe','style'=>'background-color: #dfdfdf;']],
            'invalid_message' => 'votre mot de passe de confirmation incorrect'
        ])
        ->add('envoyer', SubmitType::class, [
            'attr'=>['class'=>'btn w-100 rounded-pill fw-bold py-2','style'=>'background-color: #2ec4b6; color: #fff; border: none;']
        ])
        ;
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

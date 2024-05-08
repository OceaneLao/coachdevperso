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

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'label' => false,
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'border border-brown rounded-lg w-full ps-3 py-2 my-3',
                        'placeholder' => 'Entrez votre nouveau mot de passe',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez votre nouveau mot de passe',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => "Votre mot de passe doit au moins comporter {{ limit }} caractères.",
                            'max' => 4096,
                        ]),
                        new PasswordStrength([
                            'message' => "Veuillez entrez un mot de passe plus robuste.",
                            'groups' => 'ResetPassword',
                        ]),
                        new NotCompromisedPassword([
                            'message' => "Ce mot de passe a été compromis, veuillez en choisir un autre.",
                            'groups' => 'ResetPassword',
                        ]),
                    ],
                    
                ],
                'second_options' => [ 
                        'attr' => [ 
                            'class' => 'border border-brown rounded-lg w-full ps-3 py-2 my-3',
                            'placeholder' => 'Confirmez votre nouveau mot de passe',
                            ]
                        ],
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

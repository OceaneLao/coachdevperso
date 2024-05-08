<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Choissisez une image de profil ',
                'attr' => [
                    'class' => 'mb-5',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => "1024k",
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => "Entrez un format d'image valide : JPG ou PNG",
                    ])
                    ],
             ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [ 
                    'class' => 'border border-brown rounded-lg w-9/12 ps-3 pt-2 pb-36',
                    'placeholder' => 'Ajoutez votre description'],  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}

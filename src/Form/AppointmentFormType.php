<?php

namespace App\Form;

use App\Entity\Appointment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('createdAt', DateTimeType::class, [
            'label' => false,
            'attr' => [
                'style' => 'display:none'
            ],
        ])
        
        ->add('startedAt', DateTimeType::class, [
            'widget' => 'single_text',
            'format' => 'dd-MM-yyyy HH:mm',
            'html5' => false,
        ])

        ->add('endedAt', DateTimeType::class, [
            'widget' => 'single_text',
            'format' => 'dd-MM-yyyy HH:mm',
            'html5' => false,
        ])

        ->add('status', null, [
            'label' => false,
            'attr' => [
                'style' => 'display:none'
            ],
        ])

        ->add('isAvailable', null, [
            'label' => false,
            'attr' => [
                'style' => 'display:none'
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}

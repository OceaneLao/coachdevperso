<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt', HiddenType::class, [
                'required' => false,
            ])
            ->add('startedAt', null, [
                'widget' => 'choice',
                'required' => false,
            ])
            ->add('endedAt', HiddenType::class, [
                'required' => false,
            ])
            ->add('status', HiddenType::class, [
                'required' => false,
            ])
            ->add('isAvailable', null, [
                'required' => false,
            ])
            ->add('user', HiddenType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}

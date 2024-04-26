<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('startedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('endedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('status')
            ->add('isAvailable')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email', // Remplacez 'username' par la méthode appropriée pour afficher le nom de l'utilisateur
            'label' => 'User',
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

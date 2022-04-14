<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Saisissez votre ancien mot de passe'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les deux mots de passe doivent être identique.',
                'required' => true,
                'first_options' => ['label' => 'Nouveau mot de passe',
                    'attr' => [
                    'placeholder' => 'Saisissez votre nouveau mot de passe'
                ]],
                'second_options' => ['label' => 'Confirmer votre nouveau mot de passe',
                    'attr' => [
                    'placeholder' => 'Saisissez votre nouveau mot de passe'
                ]]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

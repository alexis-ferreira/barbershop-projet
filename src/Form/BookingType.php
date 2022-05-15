<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', ChoiceType::class, [
                'label' => 'Prestation',
                'choices' => [
                    'Coupe (15€)' => 'Coupe',
                    'Coupe + Taille de la barbe (18€)' => 'Coupe + Taille Barbe',
                    "Coupe + Rasage intégral à l'ancienne de la barbe (30€)" => 'Coupe + Taille Barbe',
                    "Rasage barbe intégral à l'ancienne (15€)" => 'Rasage barbe intégral',
                ],
                'required' => true,
            ])
            ->add('start', DateTimeType::class, [
                'label' => 'Date du rendez-vous',
                'date_widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d H:i:s')
                ],
                'hours' => [10,11,12,13,14,15,16,17,18,19],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}

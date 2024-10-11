<?php

namespace App\Form;

use App\Entity\Resena;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResenaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('puntuacion', ChoiceType::class, [
                'choices' => [
                    '1 Estrella' => 1,
                    '2 Estrellas' => 2,
                    '3 Estrellas' => 3,
                    '4 Estrellas' => 4,
                    '5 Estrellas' => 5,
                ],
                'label' => 'Puntuación',
                'expanded' => true,  // Esto hace que se muestre como botones de radio
            ])
            ->add('comentario', TextareaType::class, [
                'label' => 'Comentario',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resena::class,
        ]);
    }
}

<?php
// src/Form/ContactoType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('apellidos', TextType::class, ['label' => 'Apellidos'])
            ->add('email', EmailType::class, ['label' => 'Correo Electrónico'])
            ->add('telefono', TelType::class, ['label' => 'Teléfono'])
            ->add('mensaje', TextareaType::class, ['label' => 'Mensaje', 'attr' => ['rows' => 5]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

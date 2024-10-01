<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa tu nombre',
                    ]),
                ],
            ])
            ->add('apellido1', TextType::class, [
                'label' => 'Apellido 1',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa tu primer apellido',
                    ]),
                ],
            ])
            ->add('apellido2', TextType::class, [
                'label' => 'Apellido 2',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa tu segundo apellido',
                    ]),
                ],
            ])
            ->add('telefono', TelType::class, [
                'label' => 'Teléfono',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa tu número de teléfono',
                    ]),
                    new Length([
                        'min' => 9,
                        'minMessage' => 'Tu número de teléfono debe tener al menos {{ limit }} caracteres',
                        'max' => 15,
                    ]),
                ],
            ])
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una contraseña',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Tu contraseña debe tener al menos {{ limit }} caracteres',
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

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
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Proszę wpisać hasło',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Hasło musi mieć co najmniej {{ limit }} znaków',
                            'max' => 4096,
                        ]),
                        // new PasswordStrength([
                        //     'minScore' => PasswordStrength::STRENGTH_WEAK,
                        //     'message' => 'Hasło jest zbyt słabe. Dodaj cyfry lub znaki specjalne.',
                        // ]),
                    ],
                    'attr'=> [
                        'class' => 'w-full mb-2 p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500',
                        'placeholder' => 'Wpisz nowe hasło'
                    ],
                    'label' => 'Nowe Hasło',
                    'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mb-1']
                ],
                'second_options' => [
                    'label' => 'Powtórz hasło',
                    'attr'=> [
                        'class' => 'w-full mb-2 p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500',
                        'placeholder' => 'Powtórz hasło'
                    ],
                    'label_attr' => ['class' => 'block text-sm font-medium text-gray-700 mb-1']
                ],
                'invalid_message' => 'Hasła nie są takie same.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

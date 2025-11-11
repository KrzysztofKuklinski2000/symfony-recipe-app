<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nazwa użytkownika',
                'required' => false,
                'help' => 'Opcjonalna, publiczna nazwa, która będzie widoczna dla innych.',
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Nazwa musi mieć co najmniej {{ limit }} znaki.',
                        'max' => 255,
                        'maxMessage' => 'Nazwa może mieć maksymalnie {{ limit }} znaków'
                    ]),
                ]
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Zdjęcie profilowe (JPG lub PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Proszę przesłać poprawny plik obrazu (JPG lub PNG)'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\RecipeIngredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class RecipeIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa składnika',
                'attr' => [
                    'placeholder' => 'np. Mąka'
                ]
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Ilość',
                'html5' => true,
                'required' => false,
                'attr' => [
                    'step' => 'any'
                ]
            ])
            ->add('unit', TextType::class, [
                'label' => 'Jednostka (np. kg, ml, łyżki)',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredient::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\RecipeIngredient;
use App\Enum\Unit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
            ->add('unit', EnumType::class, [
                'label' => 'Jednostka (np. kg, ml, łyżki)',
                'class' => Unit::class,
                'placeholder' => '--- Wybierz jednostkę (opcjonalnie) ---',
                'choice_label' => fn(Unit $unit) => $unit->getLabel(),
                'required' => false,
            ])
            ->add('nutritionFactor', ChoiceType::class, [
                'label' => 'Zużycie (Kalorie)',
                'choices' => [
                    'Całość (100%)' => 1.0,
                    'Marynata (zjadamy połowę) (~50%)' => 0.5,
                    'Płytkie smażenie (~20%)' => 0.2,
                    'Głębokie smażenie (frytki) (~10%)' => 0.1,
                    'Tylko do smaku/wywar (np. liść laurowy) (~0%)' => 0.0,
                ],
                'expanded' => false,
                'multiple' => false,
                'help' => 'Wybierz mniej, jeśli składnik nie jest zjadany w całości (np. olej do smażenia).',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredient::class,
        ]);
    }
}

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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredient::class,
        ]);
    }
}

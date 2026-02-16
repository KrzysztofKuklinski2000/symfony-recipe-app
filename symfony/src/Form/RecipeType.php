<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Enum\DietaryTag;
use App\Enum\Difficulty;
use App\Form\RecipeIngredientType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\DataTransformer\CategoriesToCollectionTransformer;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Tytuł przepisu'
            ])
            ->add('instructions', TextareaType::class, [
                'label' => 'Instrukcje przygotowania',
                'attr' => ['rows' => 10]
            ])
            ->add('preparationTime', IntegerType::class, [
                'label' => 'Czas przygotowania'
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Zdjęcie do przepisu (JPG lub PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Proszę przesłać poprawny plik obrazu (JPG lub PNG)'
                    ])
                ],
            ])
            ->add('recipeIngredients', CollectionType::class, [
                'label' => 'Składniki: ',
                'entry_type' => RecipeIngredientType::class,
                'prototype'  => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('categories', CategoryTagsType::class, [
                'label' => 'Kategorie (oddziel przecinkami):',
                'attr' => [
                    'placeholder' => 'np. Śniadanie, Zdrowe, Włoskie',
                ],
                'required' => false,
                'invalid_message' => 'Błąd w formacie kategorii.'
            ])
            ->add('difficulty', EnumType::class, [
                'label' => 'Wybierz poziom trudności',
                'class' => Difficulty::class,
                'choice_label' => fn($difficulty) => $difficulty->getLabel(),
                'placeholder' => '--- Wybierz (opcjonalnie) ---',
                'required' => false,
            ])
            ->add('dietaryTags', EnumType::class, [
                'label' => 'Wybierz preferencje diety',
                'class' => DietaryTag::class,
                'choice_label' => fn($difficulty) => $difficulty->getLabel(),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('servings', IntegerType::class, [
                'label' => 'Liczba porcji',
                'required' => false,
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('calculateNutrition', CheckboxType::class, [
            'label' => 'Obliczaj kalorie automatycznie',
            'help' => 'Odznacz, jeśli chcesz usunąć kalorie lub wpisać je ręcznie (w przyszłości).',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}

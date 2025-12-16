<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\Type\CategoryTagsType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Tytuł');
        yield TextEditorField::new('instructions', 'Instrukcje przygotowania');

        yield TextField::new('image_filename', 'Nazwa pliku zdjęcia');

        yield IntegerField::new('preparation_time', 'Czas przygotowania (h)');

        yield AssociationField::new('categories', 'Kategorie')
            ->onlyOnIndex();

        yield Field::new('tagsInput', 'Kategorie')
            ->setFormType(CategoryTagsType::class)
            ->setFormTypeOptions(['property_path' => 'categories'])
            ->onlyOnForms();

        yield CollectionField::new('recipeIngredients', 'Składniki')
            ->setEntryIsComplex(true)
            ->useEntryCrudForm(RecipeIngredientCrudController::class);

    }
}

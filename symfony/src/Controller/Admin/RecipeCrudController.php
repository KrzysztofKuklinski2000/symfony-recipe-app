<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Tytuł'),
            TextEditorField::new('description', 'Opis'),

            TextField::new('image', 'Nazwa pliku zdjęcia'),

            IntegerField::new('cookingTime', 'Czas gotowania (min)'),
            IntegerField::new('servings', 'Porcje'),

            AssociationField::new('categories', 'Kategorie')
                ->autocomplete(),

            CollectionField::new('recipeIngredients', 'Składniki')
                ->useEntryCrudForm(RecipeIngredientCrudController::class)
        ];
    }
}

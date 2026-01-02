<?php

namespace App\Controller\Admin;

use App\Entity\RecipeIngredient;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class RecipeIngredientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RecipeIngredient::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nazwa składnika');
        yield NumberField::new('quantity', 'Ilość');
        yield TextField::new('unit', 'Jednostka');
        yield AssociationField::new('recipe')->hideOnForm();
    }
}

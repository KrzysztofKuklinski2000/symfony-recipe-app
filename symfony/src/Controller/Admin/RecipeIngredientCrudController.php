<?php

namespace App\Controller\Admin;

use App\Entity\RecipeIngredient;
use App\Enum\Unit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class RecipeIngredientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RecipeIngredient::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $units = [];
        foreach (Unit::cases() as $unit) {
            $units[$unit->getLabel()] = $unit;
        }

        yield TextField::new('name', 'Nazwa składnika');
        yield NumberField::new('quantity', 'Ilość');
        yield ChoiceField::new('unit', 'Jdnostka')
            ->setChoices($units)
            ->renderAsBadges();
        yield AssociationField::new('recipe')->hideOnForm();
    }
}

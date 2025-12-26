<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\Type\CategoryTagsType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

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
        yield AssociationField::new('author')->hideOnForm();
        yield TextEditorField::new('instructions', 'Instrukcje przygotowania')
            ->hideOnIndex();

        yield ImageField::new('image_filename', 'Nazwa pliku zdjęcia')
            ->setBasePath('/uploads/recipes/')
            ->setUploadDir('public/uploads/recipes/')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        yield IntegerField::new('preparation_time', 'Czas przygotowania (h)')
            ->hideOnIndex();

        yield ArrayField::new('categories', 'Kategorie')
            ->onlyOnIndex();

        yield Field::new('tagsInput', 'Kategorie')
            ->setFormType(CategoryTagsType::class)
            ->setFormTypeOptions(['property_path' => 'categories'])
            ->onlyOnForms();

        yield CollectionField::new('recipeIngredients', 'Składniki')
            ->setEntryIsComplex(true)
            ->useEntryCrudForm(RecipeIngredientCrudController::class)
            ->onlyOnForms();

        yield CollectionField::new('recipeIngredients', 'Lista Składników')
            ->onlyOnDetail()
            ->formatValue(function ($value, $entity){
                if(count($value) === 0 ) return 'Brak składników';

                $html = '<ul style="padding-left: 20px; margin: 0;">';

                foreach($value as $ingredient){
                    $html .= '<li>' . (string) $ingredient . '</li>';
                }
                $html .= '</ul>';

                return $html;
            });
    }

    public function configureCrud(Crud $crud): Crud {
        return $crud
            ->setEntityLabelInSingular('Przepis')
            ->setPageTitle('index', 'Zarządzanie Przepisami');
    }

    public function configureActions(Actions $actions): Actions {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}

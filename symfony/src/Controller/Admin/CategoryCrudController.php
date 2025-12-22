<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureActions(Actions $actions): Actions {
        return $actions->disable(Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nazwa');
        yield TextField::new('slug', 'Slug')->onlyOnIndex();

    }

    public function configureCrud(Crud $crud): Crud {
        return $crud
            ->setPageTitle('index', 'Zarządzanie Kategoriami')
            ->setPageTitle('edit', 'Edycja Kategorii')
            ->setHelp('index', 'Tutaj możesz poprawiać nazwy kategorii lub usuwać nieużywane.');
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Controller\Admin\RecipeCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureActions(Actions $actions): Actions {
        return $actions->disable(Action::NEW)->add(Crud::PAGE_INDEX, Action::DETAIL);;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nazwa');
        yield TextField::new('slug', 'Slug')->onlyOnIndex();
        yield AssociationField::new('recipes', 'Przepisy')
        ->hideOnForm()
        ->onlyOnIndex();

        yield CollectionField::new('recipes', 'Lista Przepisów')
            ->onlyOnDetail()
            ->formatValue(function ($collection){
                $count = count($collection);

                if($count === 0) return 'Brak przepisów';

                $html = '<ul style="padding-left: 20px; margin: 0;">';
                $limit = 1;
                $i = 0;


                foreach($collection as $recipe){
                    $i++;
                    if($i > $limit) {
                        $remaining = $count - $limit;
                        $html .= '<li style="color: grey; font-style: italic;">... i ' . $remaining . ' innych.</li>';
                        break;
                    }
                    $html .= '<li>' . (string) $recipe . '</li>';
                }
                $html .= '</ul>';

                return $html;
            });
    }


    public function configureCrud(Crud $crud): Crud {
        return $crud
            ->setPageTitle('index', 'Zarządzanie Kategoriami')
            ->setPageTitle('edit', 'Edycja Kategorii')
            ->setHelp('index', 'Tutaj możesz poprawiać nazwy kategorii lub usuwać nieużywane.');
    }
}

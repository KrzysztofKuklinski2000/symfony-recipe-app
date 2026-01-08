<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureActions(Actions $actions): Actions {
        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureCrud(Crud $crud): Crud {
        return $crud
            ->setEntityLabelInSingular('Komentarz')
            ->setEntityLabelInPlural('Komentarze')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle('index', 'Zarządzanie komentarzami');
    }


    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('author', 'Autor');
        yield AssociationField::new('recipe', 'Przepis');

        yield TextareaField::new('content', 'Treść')
            ->stripTags()
            ->renderAsHtml(false);

        yield DateTimeField::new('createdAt', 'Data dodania')
            ->setFormat('yyyy-MM-dd HH:mm');
    }

}

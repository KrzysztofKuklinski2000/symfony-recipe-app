<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield ImageField::new('imageFilename', 'Nazwa pliku zdjęcia')
            ->setBasePath('/uploads/users/')
            ->setUploadDir('public/uploads/users/')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);
        yield EmailField::new('email');
        yield TextField::new('plainPassword')
            ->setFormType(PasswordType::class)
            ->setLabel('Hasło')
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->setHelp('Zostaw puste, jeśli nie chcesz zmieniać hasła.')
            ->onlyOnForms();
        yield ChoiceField::new('roles')
            ->setChoices([
                "Użytkownik" => "ROLE_USER",
                "Administrator" => "ROLE_ADMIN",
            ])
            ->allowMultipleChoices()
            ->renderExpanded()
            ->setLabel('Uprawnienia');
        yield BooleanField::new('isVerified')->setLabel('Zweryfikowany');
    }
}

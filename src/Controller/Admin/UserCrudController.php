<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('lastname', 'Nom de famille')->onlyOnForms()->setColumns(6),
            TextField::new('firstname', 'Prénom')->onlyOnForms()->setColumns(6),
            TextField::new('fullName', 'Nom complet')->hideOnForm(),
            TextField::new('email', 'Adresse e-mail')->setSortable(false),
            ArrayField::new('roles', 'Rôles')->setSortable(false),
            BooleanField::new('isActive', 'Est actif / active'),
            BooleanField::new('isVerified', 'Est vérifié(e)'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des membres')
            ->setEntityLabelInSingular('membre')
            ->setPageTitle('new', 'Créer nouveau membre')
            ->setDefaultSort(['lastname' => 'ASC'])
        ;
            // ->setEntityPermission('ROLE_EDITOR');
    }
}

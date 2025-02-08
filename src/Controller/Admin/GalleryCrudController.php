<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GalleryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Gallery::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des galeries')
            ->setEntityLabelInSingular('galerie')
            ->setPageTitle('edit', fn (Gallery $gallery) => sprintf('Modifier <b>galerie "%s"</b>', $gallery->getName()))
            ->setPageTitle('new', "Créer galerie")
            ->showEntityActionsInlined()
            ->setSearchFields(null)
            ->addFormTheme('back/collection-row-participant-contest.html.twig')
        ;
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GalleryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Gallery::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            DateTimeField::new('date', 'Date'),
            BooleanField::new('isActive', "Activer la galerie"),
            BooleanField::new('isPrivate', "Rendre privée la galerie")
                ->setColumns(3)
                ->setFormTypeOptions([
                    "attr" => [
                        "data-is-private-switch" => null,
                    ],
                ]),
            TextField::new('password', 'Mot de passe d\'accès à la galerie')
                ->setFormTypeOptions([
                    "attr" => [
                        "data-password-input" => null,
                        "disabled" => "",
                    ],
                ])
                ->setColumns(3),
            TextField::new('uuid')->hideOnForm(),
            TextEditorField::new('slug')->hideOnForm(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des galeries')
            ->setEntityLabelInSingular('galerie')
            ->setPageTitle('edit', fn (Gallery $gallery) => sprintf('Modifier <b>galerie "%s"</b>', $gallery->getName()))
            ->setPageTitle('new', 'Créer galerie')
            ->showEntityActionsInlined()
            ->setSearchFields(null)
            // ->addFormTheme('back/collection-row-participant-contest.html.twig')
        ;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addAssetMapperEntry(Asset::new('backend_app'))
            // ->addJsFile('backend/admin.js')
        ;
    }
}

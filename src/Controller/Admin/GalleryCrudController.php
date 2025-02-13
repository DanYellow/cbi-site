<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GalleryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Gallery::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $gallery = $this->getContext()->getEntity()->getInstance();
        $isPrivate = false;
        if ($gallery) {
            $isPrivate = $gallery->isPrivate();
        }

        $passwordInputAttributes = array(
            "data-password-input" => null,
            "autocomplete" => "on",
        );

        if (!$isPrivate) {
            $passwordInputAttributes['disabled'] = '';
        }

        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('name', 'Nom'),
            DateField::new('createdAt', 'Crée le')->setFormTypeOptions([
                "attr" => []
            ]),
            DateField::new('updatedAt', 'Mis à jour le'),
            BooleanField::new('isActive', "Activer la galerie"),
            BooleanField::new('isPrivate', "Rendre privée la galerie")
                ->setColumns(3)
                ->setFormTypeOptions([
                    "attr" => [
                        "data-is-private-switch" => null,
                    ],
                ])->onlyOnForms(),
            BooleanField::new('isPrivate', "Accessible via mot de passe")
                ->renderAsSwitch(false)
                ->onlyOnIndex(),
            TextField::new('password', 'Mot de passe d\'accès')
                ->setFormTypeOptions([
                    "attr" => $passwordInputAttributes,
                    'help' => "Seuls les internautes ayant le mot de passe pourront accéder à la galerie",
                ])
                ->setColumns(3)
                ->setRequired($isPrivate)
                ->hideOnIndex(),
            TextField::new('uuid')->hideOnForm()->hideOnIndex(),
            TextField::new('user.fullname', 'Auteur(e)')->onlyOnIndex()->setPermission("ROLE_ADMIN"),
            TextField::new('slug')->hideOnForm()->hideOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des galeries')
            ->setEntityLabelInSingular('galerie')
            ->setPageTitle('edit', fn(Gallery $gallery) => sprintf('Modifier galerie <b>"%s"</b>', $gallery->getName()))
            ->setPageTitle('new', 'Créer nouvelle galerie')
            ->showEntityActionsInlined()
            ->setSearchFields(null)
            ->setDefaultSort(['updatedAt' => 'DESC'])
            // ->addFormTheme('back/collection-row-participant-contest.html.twig')
        ;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addAssetMapperEntry(Asset::new('backend_app'));
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel("Créer nouvelle galerie");
        });

        return parent::configureActions($actions);
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Gallery) return;
        $entityInstance->setUser($this->getUser());

        parent::persistEntity($em, $entityInstance);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $qb->where('entity.user = :user_id');
            $qb->setParameter('user_id', $this->getUser());
        }

        return $qb;
    }
}

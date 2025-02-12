<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    protected UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
    }

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
            TextField::new('password', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->setHelp($pageName === Crud::PAGE_NEW ? "" : "Laisser vide si le mot de passe n'est pas modifié")
                ->setColumns(6),
            TextField::new('fullName', 'Nom complet')->hideOnForm(),
            TextField::new('email', 'Adresse e-mail')->setSortable(false)->setColumns(6),
            ArrayField::new('roles', 'Rôles')->setSortable(false)->setColumns(6)->setPermission('ROLE_ADMIN'),
            BooleanField::new('isActive', 'Est actif / active')->setPermission('ROLE_ADMIN'),
            BooleanField::new('isVerified', 'Est vérifié(e)')->hideWhenCreating(true)->setPermission('ROLE_ADMIN'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $actions
                ->disable(Action::NEW, Action::DELETE, Crud::PAGE_INDEX);
                // ->add(Crud::PAGE_INDEX, Action::DETAIL);
        }

        return parent::configureActions($actions);
    }

    private function pageTitle(User $user) {
        if ($user == $this->getUser()) {
            return "Éditer mon profil";
        }
        return sprintf('Éditer le profil de "%s"', $user->getFullName());
    }

    public function configureCrud(Crud $crud): Crud
    {
        function foo()
        {
            return "rrrre";
        }

        // function pageTitle(User $user) {
        //     if ($user == $this->getUser()) {
        //         return "fefe";
        //     }
        //     return sprintf('Modifier "%s"', $user->getFullName());
        // }

        return $crud
            ->setPageTitle('index', foo())
            // ->setPageTitle('index', 'Liste des membres')
            ->setEntityLabelInSingular('membre')
            ->setPageTitle('new', 'Créer nouveau membre')
            ->setDefaultSort(['lastname' => 'ASC'])
            ->setPageTitle('edit', fn(User $user) => $this->pageTitle($user))
        ;
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof User) return;

        $originalData = $em->getUnitOfWork()->getEntityChangeSet($entityInstance);
        if (array_key_exists('password', $originalData)) {
            $entityInstance->setPassword($this->userPasswordHasher->hashPassword($entityInstance, $entityInstance->getPassword()));
        }

        parent::updateEntity($em, $entityInstance);
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof User) return;

        $entityInstance->setPassword($this->userPasswordHasher->hashPassword($entityInstance, $entityInstance->getPassword()));

        parent::persistEntity($em, $entityInstance);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $qb->where('entity.id = :user_id');
            $qb->setParameter('user_id', $this->getUser()->getId());
        }

        return $qb;
    }
}

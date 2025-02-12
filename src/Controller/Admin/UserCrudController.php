<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_ADMIN')]
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
                ->setColumns(6),
            TextField::new('fullName', 'Nom complet')->hideOnForm(),
            TextField::new('email', 'Adresse e-mail')->setSortable(false)->setColumns(6),
            ArrayField::new('roles', 'Rôles')->setSortable(false)->setColumns(6),
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
}

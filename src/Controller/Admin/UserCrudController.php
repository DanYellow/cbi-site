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
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        yield IdField::new('id')->hideOnForm()->hideOnIndex();
        yield TextField::new('lastname', 'Nom de famille')->onlyOnForms()->setColumns(6);
        yield TextField::new('firstname', 'Prénom')->onlyOnForms()->setColumns(6);
        yield TextField::new('password', 'Mot de passe')
            ->setFormType(PasswordType::class)
            ->onlyOnForms()
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->setHtmlAttributes([
                'autocomplete' => 'false',
            ])
            ->setHelp($pageName === Crud::PAGE_NEW ? "" : "Laisser vide si le mot de passe n'est pas modifié")
            ->setColumns(6);
        yield TextField::new('fullName', 'Nom complet')->hideOnForm();
        yield TextField::new('email', 'Adresse e-mail')->setSortable(false)->setColumns(6);
        yield ArrayField::new('roles', 'Rôles')->setSortable(false)->setColumns(6)->setPermission('ROLE_ADMIN');

        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            yield BooleanField::new('isActive', 'Est actif / active');
            yield BooleanField::new('isVerified', 'Est vérifié(e)')->hideWhenCreating(true);
        } else {
            yield BooleanField::new('isActive', 'Est actif / active')->hideOnForm();
            yield BooleanField::new('isVerified', 'Est vérifié(e)')->hideOnForm();
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $actions
                ->disable(
                    Action::NEW,
                    Action::DELETE,
                    Crud::PAGE_INDEX,
                    Action::BATCH_DELETE,
                    Action::SAVE_AND_RETURN,
                );
            // ->add(Crud::PAGE_INDEX, Action::DETAIL);
        }

        return parent::configureActions($actions);
    }

    private function pageTitle(User $user)
    {
        if ($user == $this->getUser()) {
            return "Éditer mon profil";
        }
        return sprintf('Éditer le profil de "%s"', $user->getFullName());
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des membres')
            ->setEntityLabelInSingular('membre')
            ->setPageTitle('new', 'Créer nouveau membre')
            ->setDefaultSort(['lastname' => 'ASC'])
            ->setPageTitle('edit', fn(User $user) => $this->pageTitle($user))
            ->setPageTitle('detail', function (User $user) {
                if ($user == $this->getUser()) {
                    return "Mon profil";
                }

                return sprintf('Profil de "%s"', $user->getFullName());
            })
        ;
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof User) return;

        $entityInstance->setPassword($this->userPasswordHasher->hashPassword($entityInstance, $entityInstance->getPassword()));

        parent::persistEntity($em, $entityInstance);
    }

    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $user = $context->getEntity()->getInstance();
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        // if ($tag->isPublic() && $tag->isEditable()) {
        $url = $adminUrlGenerator
            ->setAction(Action::DETAIL)
            ->setEntityId($user->getId())
            ->generateUrl();

        return $this->redirect($url);
        // }

        return parent::getRedirectResponseAfterSave($context, $action);
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

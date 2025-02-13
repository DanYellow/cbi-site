<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\User;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/cbi-admin', routeName: 'admin')]
#[IsGranted("ROLE_USER")]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private UserRepository $userRepository) {}

    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(AlbumCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getFullName())
        ;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setFaviconPath('assets/images/logo-cbi.jpg')
            ->setTitle('<img src="/assets/images/logo-cbi.jpg" alt="Logo Club de Belles Images" width="200">Administration <br>Club des belles images');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Mes albums', 'fa-solid fa-images', Album::class);
        yield MenuItem::linkToCrud('Mon profil', 'fa-solid fa-user', User::class)
            ->setAction('detail')
            ->setEntityId($this->getUser()->getId());
        yield MenuItem::section("Administration")->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Membres', 'fa-solid fa-users', User::class)
            ->setPermission('ROLE_ADMIN')->setBadge($this->userRepository->getNumberNonVerifiedUsers(), 'warning');
        yield MenuItem::section();
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class UserController extends AbstractController
{
    #[Route(['/profil/{username}'], name: 'get_profile')]
    public function profile(
        #[MapEntity(
            expr: 'repository.findOneBy({ "username": username }, {})',
            message: 'The user does not exist'
        )]
        ?User $user
    ): Response {
        dd($user);
        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '
            );
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route(['/profil/{username}/albums'], name: 'get_member_albums')]
    public function memberAlbums(
        #[MapEntity(
            expr: 'repository.findOneBy({ "username": username }, {})',
            message: 'The user does not exist'
        )]
        ?User $user
    ): Response {
        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '
            );
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/me', name: 'get_my_profile')]
    public function my_profile(
        #[CurrentUser] ?User $user
    ): Response {

        if (!$user) {
            throw $this->createNotFoundException();
        }
        // $user = $this->getUser();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}

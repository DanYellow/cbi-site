<?php

namespace App\Controller;

use App\Entity\Album;
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
            class: Album::class,
            expr: 'repository.findBy({ "user": username }, {})',
            message: 'The album does not exist'
        )]
        iterable $listAlbums,
        #[MapEntity(
            expr: 'repository.findOneBy({ "username": username }, {})',
            message: 'The user does not exist'
        )]
        ?User $user
    ): Response {
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '
            );
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route(['/profil/{username}/albums/{album_name}'], name: 'get_album')]
    public function getAlbum(
        #[MapEntity(
            class: Album::class,
            expr: 'repository.findOneBy({ "slug": album_name }, {})',
            message: 'The album does not exist'
        )]
        ?Album $album
    ): Response {
        if (!$album) {
            throw $this->createNotFoundException(
                'No album found for id '
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

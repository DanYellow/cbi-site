<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route(['/profil/{username}', "/me"], name: 'get_profile')]
    public function index(
        // #[MapEntity(
        //     expr: 'repository.findOneBy({"username": username}, {})',
        //     message: 'The product does not exist'
        // )]
        string $username,
        EntityManagerInterface $entityManager
    ): Response {

        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($username === "me") {
            $user = $this->getUser();
        }

        // dd($user);

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}

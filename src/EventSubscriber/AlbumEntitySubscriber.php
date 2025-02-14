<?php

namespace App\EventSubscriber;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Album::class)]
class AlbumEntitySubscriber
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private RequestStack $requestStack,
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function preUpdate(Album $album, PreUpdateEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        $originalData = $entityManager->getUnitOfWork()->getEntityChangeSet($album);

        if (!empty($album->getPassword()) && array_key_exists('password', $originalData)) {
            $album->setPassword($this->userPasswordHasher->hashPassword($album, $album->getPassword()));
        } else if (array_key_exists('password', $originalData)) {
            $album->setPassword($originalData["password"][0]);
        }
    }
}

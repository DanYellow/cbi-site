<?php

# src/EventSubscriber/EasyAdminSubscriber.php
namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct() {}

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setUserSlug'],
        ];
    }

    public function setUserSlug(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        // if (!($entity instanceof User)) {
        //     return;
        // }

        $fs = new Filesystem();
        $fs->appendToFile('logs.tmp.txt', $entity->getPassword());
    }
}

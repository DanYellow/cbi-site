<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mime\Address;


class EasyAdminSubscriber implements EventSubscriberInterface
{
    protected MailerInterface $mailer;
    private bool $previousVerifiedState;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            AfterEntityUpdatedEvent::class => ['sendActivationEmail'],
            BeforeEntityUpdatedEvent::class => ['getPreviousStatus'],
        ];
    }

    public function test(BeforeCrudActionEvent $event) {

    }

    public function getPreviousStatus(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        // die();
    }

    public function sendActivationEmail(AfterEntityUpdatedEvent $event)
    {
        // $entity = $event->getEntityInstance();

        // if (!($entity instanceof User)) {
        //     return;
        // }

        // if (false && $entity->isVerified()) {
        //     try {
        //         $email = (new TemplatedEmail())
        //             ->to(new Address($entity->getEmail(), "{$entity->getFirstname()} {$entity->getLastname()}"))
        //             ->subject('Votre compte sur le site du Club des Belles Images a été activé')
        //             ->htmlTemplate('emails/account_activation.html.twig')
        //             ->context([
        //                 'user' => $entity,
        //             ]);

        //         $this->mailer->send($email);
        //     } catch (TransportExceptionInterface $e) {
        //         // some error prevented the email sending; display an
        //         // error message or try to resend the message
        //     }
        // }
    }
}

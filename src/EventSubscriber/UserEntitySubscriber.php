<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mime\Address;

use Doctrine\ORM\Events;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManager;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
class UserEntitySubscriber
{
    protected MailerInterface $mailer;
    private bool $previousVerifiedState;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function postUpdate(User $user, PostUpdateEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        $originalData = $entityManager->getUnitOfWork()->getEntityChangeSet($user);

        if (array_key_exists('isVerified', $originalData) && $user->isVerified()) {
            try {
                $email = (new TemplatedEmail())
                    ->to(new Address($user->getEmail(), "{$user->getFirstname()} {$user->getLastname()}"))
                    ->subject('Votre compte sur le site du Club des Belles Images a été activé')
                    ->htmlTemplate('emails/account_activation.html.twig')
                    ->context([
                        'user' => $user,
                    ]);

                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
            }
        }
    }
}

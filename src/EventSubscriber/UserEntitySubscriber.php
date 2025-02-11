<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;


#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
class UserEntitySubscriber
{
    protected MailerInterface $mailer;

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

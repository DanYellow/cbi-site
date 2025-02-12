<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
class UserEntitySubscriber
{
    protected MailerInterface $mailer;
    protected ParameterBagInterface $params;
    protected UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        MailerInterface $mailer,
        ParameterBagInterface $params,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        $this->mailer = $mailer;
        $this->params = $params;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function postUpdate(User $user, PostUpdateEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        $originalData = $entityManager->getUnitOfWork()->getEntityChangeSet($user);

        if (array_key_exists('isVerified', $originalData) && $user->isVerified()) {
            try {
                $email = (new TemplatedEmail())
                    ->to(new Address($user->getEmail(), "{$user->getFirstname()} {$user->getLastname()}"))
                    ->subject('CBI - Votre compte sur le site du Club des Belles Images a été activé')
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

    // public function preUpdate(User $user, PreUpdateEventArgs $args): void
    // {
    //     $entityManager = $args->getObjectManager();

    //     $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
    //     $entityManager->persist($user);
    //     $entityManager->flush();
    // }

    public function postPersist(User $user): void
    {
        // container->getParameter("app.auto_email")
        try {
            $email = (new TemplatedEmail())
                ->to(new Address($this->params->get("app.auto_email"), "Club des Belles Images"))
                ->subject("CBI - {$user->getFullName()} vient de créer un compte")
                ->htmlTemplate('emails/account_creation.html.twig')
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

<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Symfony\Component\Mime\Address;


class EasyAdminSubscriber implements EventSubscriberInterface
{
    protected $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            AfterEntityUpdatedEvent::class => ['setBlogPostSlug'],
        ];
    }

    public function setBlogPostSlug(AfterEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        if ($entity->isVerified()) {
            try {
                $email = (new TemplatedEmail())
                    // ->from(new Address('hello@example.com', 'Club des Belles Images'))
                    ->to($entity->getEmail())
                    ->subject('Votre compte a été activé')
                    ->htmlTemplate('emails/account_activation.html.twig')
                    ->context([
                        'user' => $entity,
                    ])
                ;

                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
            }
        }
    }
}

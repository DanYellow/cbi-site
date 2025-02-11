<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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

        $email = (new Email())
            ->from('hello@example.com')
            ->to($entity->getEmail())
            ->subject('Votre compte a été activé')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $this->mailer->send($email);
        // dd($entity->isVerified());
        // $slug = $this->slugger->slugify($entity->getTitle());
        // $entity->setIsVerified(false);
    }
}

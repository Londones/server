<?php
// api/src/EventSubscriber/EtablissementMailSubscriber.php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Etablissement;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

final class EtablissementMailSubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['sendMail', EventPriorities::POST_WRITE],
        ];
    }

    public function sendMail(ViewEvent $event): void
    {
        $etablissement = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$etablissement instanceof Etablissement || Request::METHOD_POST !== $method) {
            return;
        }

        $message = (new Email())
            ->from('no-reply@pickme.fr')
            ->to('vietanhc12@gmail.com')
            ->subject('A new etablissement has been added')
            ->text(sprintf('The etablissement #%d has been added.', $etablissement->getId()));

        $this->mailer->send($message);
    }
}
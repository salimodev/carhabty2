<?php
// src/EventListener/ForceIndexationListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ForceIndexationListener implements EventSubscriberInterface
{
    public function onResponse(ResponseEvent $event): void
    {
        // Forcer l'indexation seulement en prod
        if ($_ENV['APP_ENV'] === 'prod') {
            $event->getResponse()->headers->set('X-Robots-Tag', 'index, follow');
        }
    }

    public static function getSubscribedEvents(): array
    {
        // Priorité plus haute pour écraser le listener Symfony
        return [
            KernelEvents::RESPONSE => ['onResponse', 255],
        ];
    }
}

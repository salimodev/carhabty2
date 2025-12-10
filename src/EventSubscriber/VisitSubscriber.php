<?php
// src/EventSubscriber/VisitSubscriber.php
namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Entity\Visit;

class VisitSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession(); // <-- récupère la session ici

        if ($session->get('visitor_counted')) {
            return;
        }

        $visit = new Visit();
        $visit->setIp($request->getClientIp());
        $visit->setVisitedAt(new \DateTimeImmutable());

        $this->em->persist($visit);
        $this->em->flush();

        $session->set('visitor_counted', true);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}

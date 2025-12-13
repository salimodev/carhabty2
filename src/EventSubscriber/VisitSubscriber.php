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
    $ip = $request->getClientIp();

    if (!$ip) {
        return; // pas d'IP, ne rien faire
    }

    $today = new \DateTimeImmutable();
    $today = $today->setTime(0, 0, 0);

    // Vérifier si cette IP a déjà visité aujourd'hui
    $existingVisit = $this->em->getRepository(Visit::class)
        ->createQueryBuilder('v')
        ->select('v.id')
        ->where('v.ip = :ip')
        ->andWhere('v.visitedAt >= :today')
        ->setParameter('ip', $ip)
        ->setParameter('today', $today)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();

    if ($existingVisit) {
        return; // déjà compté pour aujourd'hui
    }

    // Ajouter la nouvelle visite
    $visit = new Visit();
    $visit->setIp($ip);
    $visit->setVisitedAt(new \DateTimeImmutable());

    $this->em->persist($visit);
    $this->em->flush();
}



    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}

<?php
// src/EventSubscriber/FooterSubscriber.php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Repository\FooterRepository;
use Twig\Environment;

class FooterSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    private FooterRepository $footerRepository;

    public function __construct(Environment $twig, FooterRepository $footerRepository)
    {
        $this->twig = $twig;
        $this->footerRepository = $footerRepository;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $footer = $this->footerRepository->findOneBy([], ['id' => 'DESC']);
        $this->twig->addGlobal('footer', $footer);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}

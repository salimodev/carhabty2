<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ProprietaireController extends AbstractController
{
  #[Route('/proprietaire', name: 'app_proprietaire')]
    public function index(Request $request): Response
    {
       $session =$request->getSession();
       $session->set('PageMenu', 'proprietaire');
        return $this->render('proprietaire/proprietaire.html.twig');
    }

   

    

  

}
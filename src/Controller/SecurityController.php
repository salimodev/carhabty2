<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
   #[Route(path: '/login', name: 'app_login')]
public function login(Request $request,AuthenticationUtils $authenticationUtils, EntityManagerInterface $em): Response
{
     $session = $request->getSession();
    $session->set('PageMenu', 'app_login');
    // récupérer l'erreur de login si elle existe
    $error = $authenticationUtils->getLastAuthenticationError();
    // récupérer le dernier username entré
    $lastUsername = $authenticationUtils->getLastUsername();

    // Vérifier si l'utilisateur existe et est bloqué uniquement si pas d'erreur déjà
    if ($lastUsername && !$error) {
        $user = $em->getRepository(Users::class)
                   ->findOneBy(['email' => $lastUsername]);

        if ($user && $user->isBlocked()) {
            // empêche la connexion et remplace l'erreur
            $error = "Votre compte est bloqué par l'administrateur.";
        }
    }

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error
    ]);
}

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

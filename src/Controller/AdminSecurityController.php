<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminSecurityController extends AbstractController
{
    #[Route(path: '/auth/admin/login', name: 'app_admin_login')]
    public function login_admin(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     dd($this->getUser());
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/adminLogin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

   #[Route('/logout/admin', name: 'app_logout_admin')]
public function logout(): void
{
    // Cette méthode sera **interceptée par Symfony**, donc jamais appelée.
    throw new \LogicException('Cette méthode est interceptée par Symfony.');
}
}

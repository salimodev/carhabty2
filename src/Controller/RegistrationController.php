<?php

namespace App\Controller;

use App\Entity\Users;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\UsersAuthenticator;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,UserAuthenticatorInterface $userAuthenticator,SendMailService $mail, UsersAuthenticator $authenticator,  UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
 
              // Gestion du rôle choisi dans la liste déroulante
            $selectedRole = $form->get('roles')->getData(); // récupère la chaîne choisie
            $user->setRoles([$selectedRole]);               // convertit en tableau pour la base

            $entityManager->persist($user);
            $entityManager->flush();
            $context = compact('user');
              // Envoi du mail
            $mail->sendBienvenue(
                'salimabbessi.dev@gmail.com',
                $user->getEmail(),
                'Bienvenue',
                'bienvenue',
                $context
            );
             // 🔽 Ajout de la redirection selon le rôle
    if ($selectedRole === 'ROLE_VENDEUR_NEUF') {
        return $this->redirectToRoute('dashboard_vendeurNeuf');
    } elseif ($selectedRole === 'ROLE_PROPRIETAIRE') {
        return $this->redirectToRoute('app_proprietaire');
    }

          // --- Authentification automatique ---
        return $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request,
            
        );
    }

    return $this->render('registration/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
    }
}

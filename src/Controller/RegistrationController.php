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
public function register(
    Request $request,
    UserAuthenticatorInterface $userAuthenticator,
    SendMailService $mail,
    UsersAuthenticator $authenticator,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager
): Response {
    $user = new Users();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var string $plainPassword */
        $plainPassword = $form->get('plainPassword')->getData();

        // 🔒 Hasher le mot de passe
        $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

        // 🎭 Gestion du rôle choisi
        $selectedRole = $form->get('roles')->getData();
        $user->setRoles([$selectedRole]);

        $entityManager->persist($user);
        $entityManager->flush();

        // 📧 Envoi du mail de bienvenue
        $context = compact('user');
        $mail->sendBienvenue(
            'salimabbessi.dev@gmail.com',
            $user->getEmail(),
            'Bienvenue',
            'bienvenue',
            $context
        );

        // ✅ Authentifier l’utilisateur AVANT redirection
        $response = $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );

        // 🚀 Redirection selon le rôle
        if ($selectedRole === 'ROLE_VENDEUR_NEUF') {
            return $this->redirectToRoute('dashboard_vendeurNeuf');
        } elseif ($selectedRole === 'ROLE_PROPRIETAIRE') {
            return $this->redirectToRoute('app_proprietaire');
          }  elseif ($selectedRole === 'ROLE_PARTICULIER') {
            return $this->redirectToRoute('app_particulier');
        }elseif ($selectedRole === 'ROLE_MECANICIEN') {
    return $this->redirectToRoute('app_mecancien');
}

        return $response;
    }

    return $this->render('registration/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}

}

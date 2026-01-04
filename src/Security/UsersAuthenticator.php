<?php

namespace App\Security;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UsersAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private EntityManagerInterface $em;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private RouterInterface $router,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    public function authenticate(Request $request): Passport
    {
        $login = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token');

        // garde le login saisi en session
        $request->getSession()->set('_security.last_username', $login);

        return new Passport(
            new UserBadge($login, function ($login) {
                // chercher par email
                $user = $this->em->getRepository(Users::class)->findOneBy(['email' => $login]);

                // si non trouvé et que c’est un numéro
                if (!$user && preg_match('/^\d+$/', $login)) {
                    $user = $this->em->getRepository(Users::class)->findOneBy(['tel1' => $login]);
                }

                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('Identifiants invalides.');
                }

                if ($user->isBlocked()) {
                    throw new CustomUserMessageAuthenticationException('Votre compte est bloqué par l’administrateur.');
                }

                return $user;
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        $user = $token->getUser();

        // redirection vers la page précédente si existante
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // redirection selon le rôle
        $roles = $user->getRoles();

        if (in_array('ROLE_VENDEUR_NEUF', $roles, true)) {
            return new RedirectResponse($this->router->generate('dashboard_vendeurNeuf'));
        }

        if (in_array('ROLE_PROPRIETAIRE', $roles, true)) {
            return new RedirectResponse($this->router->generate('app_proprietaire'));
        }

        if (in_array('ROLE_MECANICIEN', $roles, true)) {
            return new RedirectResponse($this->router->generate('app_mecancien'));
        }

        if (in_array('ROLE_VENDEUR_OCCASION', $roles, true)) {
            return new RedirectResponse($this->router->generate('dashboard_vendeurOccasion'));
        }

        if (in_array('ROLE_PARTICULIER', $roles, true)) {
            return new RedirectResponse($this->router->generate('app_particulier'));
        }

        // redirection par défaut
        return new RedirectResponse($this->router->generate('Accueil'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

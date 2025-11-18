<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\RouterInterface;

class UsersAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator,private RouterInterface $router)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set('_security.last_username', $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }





public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    $user = $token->getUser();

    // Sécurité : si pas d’utilisateur, retour à la page login
    if (!$user) {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    // Vérifie le rôle de l’utilisateur et redirige selon son type
    if (in_array('ROLE_VENDEUR_NEUF', $user->getRoles(), true)) {
        return new RedirectResponse($this->router->generate('dashboard_vendeurNeuf'));
    }

    if (in_array('ROLE_PROPRIETAIRE', $user->getRoles(), true)) {
        return new RedirectResponse($this->router->generate('app_proprietaire'));
    }

     if (in_array('ROLE_MECANICIEN', $user->getRoles(), true)) {
        return new RedirectResponse($this->router->generate('app_mecancien'));
    }

     if (in_array('ROLE_VENDEUR_OCCASION', $user->getRoles(), true)) {
        return new RedirectResponse($this->router->generate('dashboard_vendeurOccasion'));
    }

    // Sinon, redirige par défaut (page d’accueil)
    return new RedirectResponse($this->router->generate('Accueil'));
}

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

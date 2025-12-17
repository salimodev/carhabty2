<?php
// src/Security/UserChecker.php
namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use App\Entity\Users;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        // Vérifie que l'objet est bien un Users
        if ($user instanceof Users && $user->isBlocked()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte est bloqué par l’administrateur.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // rien à faire ici
    }
}
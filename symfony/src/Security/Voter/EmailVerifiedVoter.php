<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class EmailVerifiedVoter extends Voter
{
    public const IS_EMAIL_VERIFIED = 'IS_EMAIL_VERIFIED';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::IS_EMAIL_VERIFIED;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::IS_EMAIL_VERIFIED:
                    return $user->isVerified();
                break;
        }

        return false;
    }
}

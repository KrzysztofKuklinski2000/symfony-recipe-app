<?php

namespace App\Security\Voter;

use App\Entity\User;
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

        if (!$user instanceof User) {
            return false;
        }

        if($attribute === self::IS_EMAIL_VERIFIED) {
            return $user->isVerified();
        }

        return false;
    }
}

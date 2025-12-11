<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\ShoppingListItem;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class ShoppingListVoter extends Voter
{
    public const DELETE = 'SHOPPING_DELETE';
    public const TOGGLE = 'SHOPPING_TOGGLE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE, SELF::TOGGLE])
            && $subject instanceof ShoppingListItem;
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

        switch ($attribute) {
            case self::DELETE:
            case self::TOGGLE:
                    return $user->getId() === $subject->getUser()->getId();
                break;
        }

        return false;
    }
}

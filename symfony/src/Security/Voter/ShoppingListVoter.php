<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\ShoppingListItem;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class ShoppingListVoter extends Voter
{
    public const DELETE = 'SHOPPING_DELETE';
    public const TOGGLE = 'SHOPPING_TOGGLE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::DELETE, SELF::TOGGLE])
            && $subject instanceof ShoppingListItem;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE:
            case self::TOGGLE:
                return $user->getId() === $subject->getUser()->getId();
        }

        return false;
    }
}

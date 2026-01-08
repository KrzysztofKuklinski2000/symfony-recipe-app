<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Recipe;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class RecipeVoter extends Voter
{
    public const EDIT = 'RECIPE_EDIT';
    public const DELETE = 'RECIPE_DELETE';
    public const CREATE = 'RECIPE_CREATE';
    public const FAVORITE = 'RECIPE_FAVORITE';


    protected function supports(string $attribute, mixed $subject): bool
    {

        if($attribute === self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::DELETE, self::FAVORITE])
            && $subject instanceof Recipe;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if($attribute === self::CREATE) {
            return true;
        }

        if($subject->getAuthor() === null) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                return $subject->getAuthor()->getId() === $user->getId();
            case self::FAVORITE:
                return $subject->getAuthor()->getId() !== $user->getId();
        }

        return false;
    }
}

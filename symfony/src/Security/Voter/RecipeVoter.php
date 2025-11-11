<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Recipe;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class RecipeVoter extends Voter
{
    public const EDIT = 'RECIPE_EDIT';
    public const DELETE = 'RECIPE_DELETE';
    public const CREATE = 'RECIPE_CREATE';
    public const VIEW = 'RECIPE_VIEW';
    public const FAVORITE = 'RECIPE_FAVORITE';


    protected function supports(string $attribute, mixed $subject): bool
    {

        if($attribute === self::CREATE) {return true;}
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::FAVORITE])
            && $subject instanceof Recipe;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof User) {
            return false;
        }

        if($attribute === self::CREATE) {
            return true;
        }

        //$subject to object Recipe
        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
            case self::VIEW:
                if(null === $subject->getAuthor()) return false;
                return $subject->getAuthor()->getId() === $user->getId();
                break;
            case self::FAVORITE:
                if (null === $subject->getAuthor()) return false;
                return $subject->getAuthor()->getId() !== $user->getId();
                break;
        }

        return false;
    }
}

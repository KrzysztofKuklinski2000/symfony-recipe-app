<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class CommentVoter extends Voter
{
    public const EDIT = 'COMMENT_EDIT';
    public const VIEW = 'COMMENT_VIEW';
    public const DELETE = 'COMMENT_DELETE';
    public const VOTE = 'COMMENT_VOTE';


    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::VOTE])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user*/
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
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                break;

            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case self::DELETE:
                if (null === $subject->getAuthor()) return false;

                $isCommentAuthor = $subject->getAuthor()->getId() === $user->getId();

                //sprawdzamy czy zalogowany użytkownik jest autorem przepisu
                $isRecipeAuthor = $subject->getRecipe()->getAuthor()->getId() === $user->getId();

                return $isCommentAuthor || $isRecipeAuthor;
                break;
            case self::VOTE:
                return $subject->getAuthor()->getId() !== $user->getId();
            break;
        }

        return false;
    }
}

<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class CommentVoter extends Voter {
    public const DELETE = 'COMMENT_DELETE';
    public const VOTE = 'COMMENT_VOTE';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::VOTE])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user*/
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if($subject->getAuthor() === null) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE:
                $isCommentAuthor = $subject->getAuthor()->getId() === $user->getId();

                //sprawdzamy czy zalogowany użytkownik jest autorem przepisu
                $isRecipeAuthor = $subject->getRecipe()->getAuthor()->getId() === $user->getId();

                return $isCommentAuthor || $isRecipeAuthor;
            case self::VOTE:
                return $subject->getAuthor()->getId() !== $user->getId();
        }

        return false;
    }
}

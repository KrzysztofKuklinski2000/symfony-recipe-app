<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\RecipeRating;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RecipeRatingService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function rateRecipe(Recipe $recipe, User $user, int $score): void
    {
        $ratingRepository = $this->em->getRepository(RecipeRating::class);
        $existingRating = $ratingRepository->findOneBy([
            'recipe' => $recipe,
            'author' => $user
        ]);

        if ($existingRating) {
            $existingRating->setScore($score);
        } else {
            $newRating = new RecipeRating();
            $newRating->setRecipe($recipe);
            $newRating->setAuthor($user);
            $newRating->setScore($score);
            $this->em->persist($newRating);
        }

        $this->em->flush();
    }

    public function cancelRecipeRating(Recipe $recipe, User $user): void
    {
        $ratingRepository = $this->em->getRepository(RecipeRating::class);
        $existingRating = $ratingRepository->findOneBy([
            'recipe' => $recipe,
            'author' => $user
        ]);

        if ($existingRating) {
            $this->em->remove($existingRating);
            $this->em->flush();
        }
    }
}

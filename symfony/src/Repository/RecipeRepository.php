<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Recipe;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findRecipesFromFollowing(User $user): array
    {
        $following = $user->getFollowing();

        if($following->isEmpty()) {
            return [];
        }

        return $this->createQueryBuilder('r')
            ->andWhere('r.author IN (:followingUsers)')
            ->setParameter('followingUsers', $following)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

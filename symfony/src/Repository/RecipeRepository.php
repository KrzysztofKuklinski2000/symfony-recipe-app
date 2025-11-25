<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Category;
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

    public function findPublicRecipesExcludingUser(?User $user, ?Category $category): array{
        $query =  $this->createQueryBuilder('r')
                    ->leftJoin('r.favoritedBy', 'f')
                    ->addSelect('COUNT(f) AS HIDDEN totalFavorites')
                    ->groupBy('r.id')
                    ->orderBy('totalFavorites', 'DESC')
                    ;

        if($user) {
            $query->andWhere('r.author != :user');
            $query->setParameter('user', $user);
        }

        if($category) {
            $query->innerJoin('r.categories', 'c')
                ->andWhere('c = :category')
                ->setParameter('category', $category);
        }

        return $query->getQuery()->getResult();
    }
}

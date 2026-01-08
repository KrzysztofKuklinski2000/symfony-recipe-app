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

    public function findPublicRecipesExcludingUser(
        ?User $user,
        ?Category $category,
        ?string $phrase,
        ?string $difficultyValue = null,
        array $tagValues = [],
        int $page = 1,
        int $limit = 12,
        ): array{
        $query =  $this->createQueryBuilder('r')
            ->leftJoin('r.favoritedBy', 'f')
            ->addSelect('COUNT(f) AS HIDDEN totalFavorites')
            ->groupBy('r.id')
            ->orderBy('totalFavorites', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit + 1);

        if($user) {
            $query->andWhere('r.author != :user');
            $query->setParameter('user', $user);
        }

        if($category) {
            $query->innerJoin('r.categories', 'c')
                ->andWhere('c = :category')
                ->setParameter('category', $category);
        }

        if($phrase) {
            $query->andWhere('r.title LIKE :phrase ')
                ->setParameter('phrase', "%".$phrase."%");
        }

        if($difficultyValue) {
            $query->andWhere('r.difficulty = :difficulty')
                ->setParameter('difficulty', $difficultyValue);
        }

        if(!empty($tagValues)) {
            $orX = $query->expr()->orX();

            foreach ($tagValues as $key => $value) {
                $searchTerm = '%'.$value.'%';

                $orX->add($query->expr()->like('LOWER(r.dietaryTags)', ":tag_$key"));
                $query->setParameter("tag_$key", strtolower($searchTerm));
            }

            $query->andWhere($orX);
        }
        return $query->getQuery()->getResult();
    }
}

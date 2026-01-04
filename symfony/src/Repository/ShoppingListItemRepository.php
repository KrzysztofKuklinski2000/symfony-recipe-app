<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\ShoppingListItem;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ShoppingListItem>
 */
class ShoppingListItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingListItem::class);
    }

    public function deleteByRecipe(User $user, ?int $recipeId): void {
        $qb = $this->createQueryBuilder('s')
            ->delete()
            ->andWhere('s.user = :user')
            ->setParameter('user', $user);

        if($recipeId) {
            $qb->andWhere('s.recipe = :recipeId')
                ->setParameter('recipeId', $recipeId);
        }else {
            $qb->andWhere('s.recipe IS NULL');
        }

        $qb->getQuery()->execute();

    }
}

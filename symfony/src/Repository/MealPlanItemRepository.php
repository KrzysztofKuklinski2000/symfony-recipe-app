<?php

namespace App\Repository;

use App\Entity\MealPlanItem;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MealPlanItem>
 */
class MealPlanItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MealPlanItem::class);
    }

    public function findForUserBetweenDates(User $user, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array    {

        return $this->createQueryBuilder('mealPlanItem')
            ->andWhere('mealPlanItem.user = :user')
            ->andWhere('mealPlanItem.plannedFor BETWEEN :startDate AND :endDate')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('mealPlanItem.plannedFor', 'ASC')
            ->addOrderBy('mealPlanItem.mealType', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

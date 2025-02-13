<?php

namespace App\Repository;

use App\Entity\Habit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<habit$habit>
 *
 * @method Habit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Habit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Habit[]    findAll()
 * @method Habit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HabitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Habit::class);
    }

    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('execution');
    }


    public function save(Habit $habit): void
    {
        $this->getEntityManager()->persist($habit);
        $this->getEntityManager()->flush();
    }

    public function delete(Habit $habit): void
    {
        $this->getEntityManager()->remove($habit);
        $this->getEntityManager()->flush();
    }

    public function queryAll(): array
    {
        $query = $this->createQueryBuilder('habits')
            ->orderBy('habits.id', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}

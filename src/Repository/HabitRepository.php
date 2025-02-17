<?php
namespace App\Repository;

use App\Entity\Habit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder; 
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<Habit>
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
        return $queryBuilder ?? $this->createQueryBuilder('habit');
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

    public function getTodayHabits(): array
{
    $today = (new \DateTime())->format('N');
    $weekDays = [
        1 => "mon", 2 => "tue", 3 => "wed",
        4 => "thu", 5 => "fri", 6 => "sat", 7 => "sun"
    ];
    $currentWeekDay = $weekDays[$today];

    $entityManager = $this->getEntityManager();
    $conn = $entityManager->getConnection();

    $sql = '
        SELECT h.*, 
        CASE
            WHEN h.time IS NULL THEN \'unscheduled\'
            WHEN TIME(h.time) >= \'05:00:00\' AND TIME(h.time) < \'12:00:00\' THEN \'morning\'
            WHEN TIME(h.time) >= \'12:00:00\' AND TIME(h.time) < \'18:00:00\' THEN \'afternoon\'
            WHEN TIME(h.time) >= \'18:00:00\' AND TIME(h.time) <= \'23:59:59\' THEN \'evening\'
            ELSE \'night\'
        END AS time_category
        FROM habit h 
        WHERE (h.frequency = :daily
        OR (h.frequency = :weekdays AND :today BETWEEN 1 AND 5)
        OR (h.frequency = :weekends AND :today IN (6,7))
        OR (h.frequency = :days AND JSON_CONTAINS(h.week_days, CONCAT(\'"\',:current_weekday,\'"\'), \'$\')))
        ORDER BY h.time ASC
    ';

    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery([
        'daily' => 'daily',
        'weekdays' => 'weekdays',
        'weekends' => 'weekends',
        'days' => 'days',
        'today' => $today,
        'current_weekday' => $currentWeekDay
    ]);

    $habits = $result->fetchAllAssociative();

    $categorizedHabits = [
        'morning' => [],
        'afternoon' => [],
        'evening' => [],
        'night' => [],
        'unscheduled' => []
    ];

    foreach ($habits as $habit) {
        $categorizedHabits[$habit['time_category']][] = $habit;
    }

    return $categorizedHabits;
}
   
}

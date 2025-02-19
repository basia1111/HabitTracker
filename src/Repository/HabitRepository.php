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

    public function queryAll($user): array
{
    return $this->createQueryBuilder('habits')
        ->where('habits.user = :user')
        ->setParameter('user', $user)
        ->orderBy('habits.id', 'DESC')
        ->getQuery()
        ->getResult();
}

public function getAllHabits(): array
    {
        return $this->createQueryBuilder('habits')
            ->orderBy('habits.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    
public function getTodayHabits($user): array
{
    $today = (new \DateTime())->format('N');
    $todayDate = (new \DateTime())->format('Y-m-d'); 


    $currentWeekDay = strtolower((new \DateTime())->format('D'));

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
        END AS time_category,
        CASE 
            WHEN JSON_SEARCH(h.completions, \'one\', :today_date) IS NOT NULL THEN true 
            ELSE false 
        END AS is_completed
        FROM habit h 
        WHERE h.user_id = :user_id
        AND (h.frequency = :daily
        OR (h.frequency = :weekdays AND :today BETWEEN 1 AND 5)
        OR (h.frequency = :weekends AND :today IN (6,7))
        OR (h.frequency = :days AND JSON_CONTAINS(h.week_days, CONCAT(\'"\',:current_weekday,\'"\'), \'$\')))
        ORDER BY h.time ASC
    ';

    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery([
        'user_id' => $user->getId(),
        'daily' => 'daily',
        'weekdays' => 'weekdays',
        'weekends' => 'weekends',
        'days' => 'days',
        'today' => $today,
        'current_weekday' => $currentWeekDay,
        'today_date' => $todayDate 
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

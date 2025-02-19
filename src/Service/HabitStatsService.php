<?php
namespace App\Service;

use App\Interface\HabitServiceInterface;
use App\Interface\HabitStatsServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;

class HabitStatsService implements HabitStatsServiceInterface
{
    private HabitServiceInterface $habitService;
    private Security $security;

    public function __construct(HabitServiceInterface $habitService, Security $security)
    {
        $this->habitService = $habitService;
        $this->security = $security;
    }

    public function getUserHabitStats(): array
    {
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        $habits = $this->habitService->findAll($user);
        $todayHabits = $this->habitService->getTodayHabits($user);

        $totalHabits = count($habits);
        $totalTodayHabits = 0;

        $longestStreak = 0;
        $completedHabits = 0;

        foreach ($habits as $habit){
            if($habit->getStreak() > $longestStreak){
                $longestStreak = $habit->getStreak();
            }
        }

        foreach($todayHabits as $todayHabitCategory){
            foreach($todayHabitCategory as $habit){
                $totalTodayHabits++;
                
                if($habit['is_completed']){
                    $completedHabits++;
                }
            }
        }

        return [
            'totalHabits' => $totalHabits,
            'totalTodayHabits' => $totalTodayHabits,
            'longestStreak' => $longestStreak,
            'completedHabits' => $completedHabits, 
        ];
    }
}

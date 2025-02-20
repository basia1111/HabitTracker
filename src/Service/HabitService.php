<?php

namespace App\Service;

use App\Entity\Habit;
use App\Repository\HabitRepository;
use App\Interface\HabitServiceInterface;

class HabitService implements HabitServiceInterface
{
    private HabitRepository $habitRepository;

    public function __construct(HabitRepository $habitRepository)
    {
        $this->habitRepository = $habitRepository;
    }

    public function save(Habit $habit): void
    {
        $this->habitRepository->save($habit);
    }

    public function delete(Habit $habit): void
    {
        $this->habitRepository->delete($habit);
    }

    public function findAll($user): array
    {
        return $this->habitRepository->queryAll($user);
    }

    public function getAllHabits():array
    {
        return $this->habitRepository->getAllHabits();
    }

    public function getTodayHabits($user):array
    {
        return $this->habitRepository->getTodayHabits($user);
    }

    public function createGoogleRecurrenceRule(Habit $habit): string
    {
        $baseRule = 'RRULE:';
        
        switch ($habit->getFrequency()) {
            case 'daily':
                return $baseRule . 'FREQ=DAILY';
                
            case 'weekdays':
                return $baseRule . 'FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR';
                
            case 'weekends':
                return $baseRule . 'FREQ=WEEKLY;BYDAY=SA,SU';
                
            case 'days':
                $days = array_map(function($day) {
                    return match(strtolower($day)) {
                        'mon' => 'MO',
                        'tue' => 'TU',
                        'wed' => 'WE',
                        'thu' => 'TH',
                        'fri' => 'FR',
                        'sat' => 'SA',
                        'sun' => 'SU',
                    };
                }, $habit->getWeekDays());
                
                return $baseRule . 'FREQ=WEEKLY;BYDAY=' . implode(',', $days);
                
            default:
                return $baseRule . 'FREQ=DAILY';
        }
    }
}

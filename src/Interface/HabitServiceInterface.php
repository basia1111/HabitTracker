<?php

namespace App\Interface;

use App\Entity\Habit;

interface HabitServiceInterface
{
    public function save(Habit $habit): void;

    public function delete(Habit $habit): void;
    
    public function findAll($user): array;

    public function getTodayHabits($user): array;

    public function getAllHabits(): array;
}

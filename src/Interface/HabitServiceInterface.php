<?php

namespace App\Interface;

use App\Entity\Habit;
use App\Entity\User;

interface HabitServiceInterface
{
    public function save(Habit $habit): void;

    public function delete(Habit $habit): void;
    
    public function findAll(User $user): array;

    public function getTodayHabits(User $user): array;

    public function getAllHabits(): array;

    public function createGoogleRecurrenceRule(Habit $habit): string;
}

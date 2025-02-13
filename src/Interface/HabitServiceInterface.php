<?php

namespace App\Interface;

use App\Entity\Habit;

interface HabitServiceInterface
{
    public function save(Habit $habit): void;

    public function delete(Habit $habit): void;
    
    public function findAll(): array;
}

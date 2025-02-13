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

    public function findAll(): array
    {
        return $this->habitRepository->queryAll();
    }
}

<?php

namespace App\Interface;

use App\Entity\User;

interface UserServiceInterface
{
    public function save(User $user): void;

    public function delete(User $user): void;

    public function findAll(): array;
}

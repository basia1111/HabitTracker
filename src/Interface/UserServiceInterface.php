<?php

namespace App\Interface;

interface UserServiceInterface
{
    public function save($user): void;

    public function delete($user): void;

    public function findAll(): array;
}

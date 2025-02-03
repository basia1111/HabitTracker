<?php

namespace App\Service;

use App\Entity\User;
use App\Interface\UserServiceInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;

set_time_limit(0);

class UserService implements UserServiceInterface
{

    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function save($user): void
    {
        $this->userRepository->save($user);
    }
    
    public function delete($user): void
    {
        $this->userRepository->delete($user);
    
    }

    public function findAll(): array
    {
        return $this->userRepository-> queryAll();

    }
}

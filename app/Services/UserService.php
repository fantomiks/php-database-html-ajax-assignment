<?php

namespace App\Services;

use App\Mappers\UserMapper;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;

class UserService
{
    private UserRepositoryInterface $userRepository;
    private UserMapper $userMapper;

    public function __construct(UserRepositoryInterface $userRepository, UserMapper $userMapper)
    {
        $this->userRepository = $userRepository;
        $this->userMapper = $userMapper;
    }

    /**
     * @return array|User[]
     */
    public function getUsers(): array
    {
        return array_map(fn($row) => $this->userMapper->toUser($row), $this->userRepository->findAll());
    }

    public function bumpAccessCount(int $userId): User
    {
        $userData = [];
        $this->userRepository->transaction(function () use ($userId, &$userData) {
            $userData = $this->userRepository->selectForUpdate($userId);

            $userData['access_count']++;
            $userData['modify_dt'] = (new \DateTime())->format('Y-m-d H:i:s');

            $this->userRepository->updateById($userId, [
                'access_count' => $userData['access_count'],
                'modify_dt' => $userData['modify_dt'],
            ]);
        });

        return $this->userMapper->toUser($userData);
    }
}

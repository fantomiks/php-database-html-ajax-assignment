<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): array;

    public function selectForUpdate(int $id): array;

    public function updateById($id, array $data = []): void;

    public function transaction(callable $func): void;
}

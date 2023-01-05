<?php

namespace App\Repositories;

use App\Core\Database;

class UserRepository implements UserRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->database
            ->table('user')
            ->fetch(Database::PDO_FETCH_MULTI)
            ->runSelectQuery();
    }

    public function findById(int $id): array
    {
        return $this->database
            ->table('user')
            ->fetch(Database::PDO_FETCH_SINGLE)
            ->where('user_id', $id)
            ->runSelectQuery();
    }

    public function updateById($id, array $data = []): void
    {
        $this->database
            ->table('user')
            ->where('user_id', $id)
            ->runUpdateQuery($data);
    }

    public function selectForUpdate(int $id): array
    {
        return $this->database
            ->table('user')
            ->fetch(Database::PDO_FETCH_SINGLE)
            ->where('user_id', $id)
            ->runSelectForUpdateQuery();
    }

    /**
     * @throws \Throwable
     */
    public function transaction(callable $func): void
    {
        $this->database->beginTransaction();
        try {
            $func();
            $this->database->commit();
        } catch (\Throwable $e) {
            $this->database->rollback();
            throw $e;
        }
    }
}

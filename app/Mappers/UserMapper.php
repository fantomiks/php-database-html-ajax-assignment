<?php

namespace App\Mappers;

use App\Models\User;

class UserMapper
{
    public function toUser(array $row): User
    {
        return new User($row['user_id'] ?? null, $row['name'], $row['access_count'], $row['modify_dt']);
    }

    public function toArray(User $user): array
    {
        return [
            'user_id' => $user->getUserId(),
            'name' => $user->getName(),
            'access_count' => $user->getAccessCount(),
            'modify_dt' => $user->getModifyDt(),
        ];
    }

    public function toJson(User $user): string
    {
        return json_encode($this->toArray($user));
    }
}

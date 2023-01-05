<?php

namespace App\Models;

use DateTime;
use DateTimeInterface;

class User
{
    private ?int $userId;
    private string $name;
    private int $accessCount;
    private DateTimeInterface $modifyDt;

    public function __construct(?int $userId, string $name, int $accessCount, string $modifyDt)
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->accessCount = $accessCount;
        $this->modifyDt = DateTime::createFromFormat('Y-m-d H:i:s', $modifyDt);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAccessCount(): int
    {
        return $this->accessCount;
    }

    public function getModifyDt(): string
    {
        return $this->modifyDt->format('m/d/Y \a\t h:ia');
    }
}

<?php

namespace App\Core;

use stdClass;

/**
 * @property string $host
 * @property string $username
 * @property string $password
 * @property string $dbname
 */
class Config extends Singleton
{
    public stdClass $config;

    public function setConfig(string $json): void
    {
        $this->config = json_decode($json);
    }

    public function __get(string $name): mixed
    {
        return $this->config->$name ?? null;
    }
}

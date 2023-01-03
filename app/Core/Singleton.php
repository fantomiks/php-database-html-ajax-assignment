<?php

namespace App\Core;

use Exception;

/** @phpstan-consistent-constructor */
abstract class Singleton
{
    /**
     * @var Singleton[] The reference to *Singleton* instances of any child class.
     */
    private static array $instances = [];

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return static The *Singleton* instance.
     */
    public static function getInstance(): static
    {
        if (!isset(self::$instances[static::class])) {
            self::$instances[static::class] = new static();
        }

        return self::$instances[static::class];
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     * @throws Exception
     */
    public function __wakeup(): void
    {
        throw new Exception("Cannot unserialize a singleton.");
    }
}

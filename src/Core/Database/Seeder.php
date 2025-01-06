<?php

namespace HumoGen\Core\Database;

abstract class Seeder
{
    /**
     * Run the seeder.
     */
    abstract public function run(): void;

    /**
     * Call other seeders.
     */
    protected function call(string $class): void
    {
        $seeder = new $class;
        $seeder->run();
    }

    /**
     * Get the database connection.
     */
    protected function db()
    {
        return app()->getService('db');
    }
} 
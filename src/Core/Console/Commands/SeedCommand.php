<?php

namespace HumoGen\Core\Console\Commands;

use Database\Seeders\DatabaseSeeder;

class SeedCommand
{
    /**
     * Run database seeds.
     */
    public function handle(array $args = []): int
    {
        $class = $args[1] ?? DatabaseSeeder::class;

        if (!class_exists($class)) {
            echo "Seeder class {$class} not found.\n";
            return 1;
        }

        $seeder = new $class;
        $seeder->run();

        echo "Database seeding completed successfully.\n";
        return 0;
    }
} 
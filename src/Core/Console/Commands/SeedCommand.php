<?php

namespace HumoGen\Core\Console\Commands;

use HumoGen\Core\Console\Command;
use HumoGen\Core\Database\Seeder;

class SeedCommand extends Command
{
    protected string $signature = 'db:seed';
    protected string $description = 'Seed the database with records';

    public function handle(array $args = []): int
    {
        $class = $args[0] ?? 'Database\\Seeders\\DatabaseSeeder';
        
        if (!class_exists($class)) {
            $this->error("Seeder class not found: $class");
            return 1;
        }

        $seeder = new $class;
        
        if (!$seeder instanceof Seeder) {
            $this->error("Class must extend HumoGen\\Core\\Database\\Seeder");
            return 1;
        }

        $this->info("Running seeder: $class");
        
        try {
            $seeder->run();
            $this->info('Database seeding completed successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Database seeding failed: ' . $e->getMessage());
            return 1;
        }
    }
} 
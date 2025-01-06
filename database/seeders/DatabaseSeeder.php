<?php

namespace Database\Seeders;

use HumoGen\Core\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * The seeders to run.
     */
    protected array $seeders = [
        // Add your seeders here
        // PersonSeeder::class,
        // UserSeeder::class,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->seeders as $seeder) {
            $this->call($seeder);
        }
    }
} 
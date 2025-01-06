<?php

namespace Database\Seeders;

use HumoGen\Core\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Add your seeders here
        $this->call(PersonSeeder::class);
    }
} 
<?php

namespace Database\Seeders;

use HumoGen\Core\Database\Seeder;
use Database\Factories\PersonFactory;

class PersonSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        // Create 100 sample persons
        PersonFactory::new()
            ->count(100)
            ->create();
    }
} 
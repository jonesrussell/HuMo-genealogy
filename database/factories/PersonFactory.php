<?php

namespace Database\Factories;

use HumoGen\Core\Database\Factory;

class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'pers_firstname' => $this->faker()->firstName(),
            'pers_lastname' => $this->faker()->lastName(),
            'pers_birth_date' => $this->faker()->date(),
            'pers_birth_place' => $this->faker()->city(),
            'pers_death_date' => $this->faker()->optional(0.3)->date(),
            'pers_death_place' => $this->faker()->optional(0.3)->city(),
            'pers_gender' => $this->faker()->randomElement(['M', 'F']),
            'pers_tree_id' => 1, // Default tree
        ];
    }

    /**
     * Store the person in the database.
     */
    protected function store(array $attributes): array
    {
        $db = app()->getService('db');
        
        $columns = implode(', ', array_keys($attributes));
        $values = implode(', ', array_fill(0, count($attributes), '?'));
        
        $sql = "INSERT INTO humo_persons ({$columns}) VALUES ({$values})";
        $db->query($sql, array_values($attributes));
        
        $id = $db->lastInsertId();
        return array_merge(['id' => $id], $attributes);
    }

    /**
     * Get a Faker instance.
     */
    protected function faker()
    {
        static $faker;
        
        if (!$faker) {
            $faker = \Faker\Factory::create();
        }
        
        return $faker;
    }
} 
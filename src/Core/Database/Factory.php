<?php

namespace HumoGen\Core\Database;

abstract class Factory
{
    protected array $attributes = [];
    protected int $count = 1;

    /**
     * Define the model's default state.
     */
    abstract public function definition(): array;

    /**
     * Create new factory instance.
     */
    public static function new(): static
    {
        return new static();
    }

    /**
     * Set the number of models to create.
     */
    public function count(int $count): self
    {
        $this->count = $count;
        return $this;
    }

    /**
     * Override default attributes.
     */
    public function state(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Create the models.
     */
    public function create(array $extra = []): array
    {
        $results = [];

        for ($i = 0; $i < $this->count; $i++) {
            $attributes = array_merge(
                $this->definition(),
                $this->attributes,
                $extra
            );

            $results[] = $this->store($attributes);
        }

        return $results;
    }

    /**
     * Store the model in the database.
     */
    abstract protected function store(array $attributes): array;
} 
<?php

namespace HumoGen\Core\Contracts;

/**
 * Repository Interface
 * 
 * Defines the contract for repositories that handle data access.
 */
interface RepositoryInterface
{
    /**
     * Find a model by its primary key.
     *
     * @param int $id The primary key
     * @return object|null The found model or null
     */
    public function find(int $id): ?object;

    /**
     * Get all models.
     *
     * @return array<object> Array of models
     */
    public function all(): array;

    /**
     * Create a new model.
     *
     * @param array<string, mixed> $data The model data
     * @return object The created model
     */
    public function create(array $data): object;

    /**
     * Update an existing model.
     *
     * @param int $id The model ID
     * @param array<string, mixed> $data The update data
     * @return object|null The updated model or null if not found
     */
    public function update(int $id, array $data): ?object;

    /**
     * Delete a model.
     *
     * @param int $id The model ID
     * @return bool True if deleted, false if not found
     */
    public function delete(int $id): bool;

    /**
     * Find models by a field value.
     *
     * @param string $field The field to search
     * @param mixed $value The value to search for
     * @return array<object> Array of matching models
     */
    public function findBy(string $field, mixed $value): array;

    /**
     * Find the first model matching the criteria.
     *
     * @param array<string, mixed> $criteria The search criteria
     * @return object|null The found model or null
     */
    public function findOneBy(array $criteria): ?object;

    /**
     * Get the model class name.
     *
     * @return string The model class
     */
    public function getModelClass(): string;
} 
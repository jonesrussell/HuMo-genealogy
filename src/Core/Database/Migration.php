<?php

namespace HumoGen\Core\Database;

abstract class Migration
{
    /**
     * Run the migration.
     */
    abstract public function up(): void;

    /**
     * Reverse the migration.
     */
    abstract public function down(): void;

    /**
     * Get migration name.
     */
    public function getName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * Get migration timestamp.
     */
    public function getTimestamp(): string
    {
        preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}/', $this->getName(), $matches);
        return $matches[0] ?? '';
    }

    /**
     * Check if migration can be rolled back safely.
     */
    public function canRollback(): bool
    {
        return true;
    }

    /**
     * Run before migration.
     */
    protected function beforeUp(): void
    {
        // Add any pre-migration tasks
    }

    /**
     * Run after migration.
     */
    protected function afterUp(): void
    {
        // Add any post-migration tasks
    }

    /**
     * Run before rollback.
     */
    protected function beforeDown(): void
    {
        // Add any pre-rollback tasks
    }

    /**
     * Run after rollback.
     */
    protected function afterDown(): void
    {
        // Add any post-rollback tasks
    }
} 
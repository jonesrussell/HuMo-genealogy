<?php

namespace HumoGen\Core\Database;

class Schema
{
    /**
     * Create a new table.
     */
    public static function create(string $table, callable $callback): void
    {
        $blueprint = new Table($table);
        $callback($blueprint);
        
        static::build($blueprint);
    }

    /**
     * Modify an existing table.
     */
    public static function table(string $table, callable $callback): void
    {
        $blueprint = new Table($table, true);
        $callback($blueprint);
        
        static::build($blueprint);
    }

    /**
     * Drop a table.
     */
    public static function drop(string $table): void
    {
        $blueprint = new Table($table);
        $blueprint->drop();
        
        static::build($blueprint);
    }

    /**
     * Build the schema changes.
     */
    protected static function build(Table $blueprint): void
    {
        $blueprint->build();
    }

    /**
     * Check if a table exists.
     */
    public static function hasTable(string $table): bool
    {
        $db = app()->getService('db');
        $sql = "SHOW TABLES LIKE ?";
        $stmt = $db->query($sql, [$table]);
        return (bool) $stmt->fetch();
    }

    /**
     * Check if a column exists.
     */
    public static function hasColumn(string $table, string $column): bool
    {
        $db = app()->getService('db');
        $sql = "SHOW COLUMNS FROM {$table} LIKE ?";
        $stmt = $db->query($sql, [$column]);
        return (bool) $stmt->fetch();
    }

    /**
     * Get column listing.
     */
    public static function getColumnListing(string $table): array
    {
        $db = app()->getService('db');
        $sql = "SHOW COLUMNS FROM {$table}";
        $stmt = $db->query($sql);
        return array_column($stmt->fetchAll(), 'Field');
    }
} 
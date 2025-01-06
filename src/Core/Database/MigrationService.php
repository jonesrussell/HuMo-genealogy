<?php

namespace HumoGen\Core\Database;

class MigrationService
{
    protected $db;
    protected string $table = 'migrations';
    protected string $path;

    public function __construct()
    {
        $this->db = app()->getService('db');
        $this->path = dirname(__DIR__, 3) . '/database/migrations';
        $this->ensureMigrationTableExists();
    }

    /**
     * Run all pending migrations.
     */
    public function migrate(): array
    {
        $ran = [];
        $files = $this->getPendingMigrations();

        foreach ($files as $file) {
            $migration = $this->getMigrationInstance($file);
            
            $migration->beforeUp();
            $migration->up();
            $migration->afterUp();

            $this->log($migration);
            $ran[] = $migration->getName();
        }

        return $ran;
    }

    /**
     * Rollback the last batch of migrations.
     */
    public function rollback(): array
    {
        $rolled = [];
        $migrations = $this->getLastBatch();

        foreach ($migrations as $migration) {
            $instance = $this->getMigrationInstance($migration['migration']);
            
            if ($instance->canRollback()) {
                $instance->beforeDown();
                $instance->down();
                $instance->afterDown();

                $this->remove($migration['id']);
                $rolled[] = $instance->getName();
            }
        }

        return $rolled;
    }

    /**
     * Get pending migrations.
     */
    protected function getPendingMigrations(): array
    {
        $files = glob($this->path . '/*.php');
        $ran = $this->getRanMigrations();

        return array_filter($files, function ($file) use ($ran) {
            return !in_array(basename($file, '.php'), $ran);
        });
    }

    /**
     * Get migrations that have already run.
     */
    protected function getRanMigrations(): array
    {
        $sql = "SELECT migration FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return array_column($stmt->fetchAll(), 'migration');
    }

    /**
     * Get the last batch of migrations.
     */
    protected function getLastBatch(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE batch = (SELECT MAX(batch) FROM {$this->table})";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Log that a migration was run.
     */
    protected function log(Migration $migration): void
    {
        $batch = $this->getNextBatchNumber();
        
        $sql = "INSERT INTO {$this->table} (migration, batch) VALUES (?, ?)";
        $this->db->query($sql, [$migration->getName(), $batch]);
    }

    /**
     * Remove a migration from the log.
     */
    protected function remove(int $id): void
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->db->query($sql, [$id]);
    }

    /**
     * Get the next batch number.
     */
    protected function getNextBatchNumber(): int
    {
        $sql = "SELECT MAX(batch) FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn() + 1;
    }

    /**
     * Create a new migration instance.
     */
    protected function getMigrationInstance(string $file): Migration
    {
        require_once $file;
        $class = basename($file, '.php');
        return new $class;
    }

    /**
     * Ensure the migration table exists.
     */
    protected function ensureMigrationTableExists(): void
    {
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function (Table $table) {
                $sql = "CREATE TABLE {$this->table} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL,
                    batch INT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                app()->getService('db')->query($sql);
            });
        }
    }
} 
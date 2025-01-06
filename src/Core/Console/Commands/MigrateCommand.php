<?php

namespace HumoGen\Core\Console\Commands;

use HumoGen\Core\Database\MigrationService;

class MigrateCommand
{
    protected MigrationService $migrator;

    public function __construct()
    {
        $this->migrator = new MigrationService();
    }

    /**
     * Run migrations.
     */
    public function handle(array $args = []): int
    {
        if (isset($args[1]) && $args[1] === 'rollback') {
            return $this->rollback();
        }

        return $this->migrate();
    }

    /**
     * Run pending migrations.
     */
    protected function migrate(): int
    {
        $ran = $this->migrator->migrate();

        if (empty($ran)) {
            echo "Nothing to migrate.\n";
            return 0;
        }

        foreach ($ran as $migration) {
            echo "Migrated: {$migration}\n";
        }

        return 0;
    }

    /**
     * Rollback migrations.
     */
    protected function rollback(): int
    {
        $rolled = $this->migrator->rollback();

        if (empty($rolled)) {
            echo "Nothing to rollback.\n";
            return 0;
        }

        foreach ($rolled as $migration) {
            echo "Rolled back: {$migration}\n";
        }

        return 0;
    }
} 
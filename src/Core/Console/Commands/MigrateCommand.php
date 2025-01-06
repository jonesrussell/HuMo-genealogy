<?php

namespace HumoGen\Core\Console\Commands;

use HumoGen\Core\Console\Command;
use HumoGen\Core\Database\MigrationService;

class MigrateCommand extends Command
{
    protected string $signature = 'migrate';
    protected string $description = 'Run database migrations';

    protected MigrationService $migrator;

    public function __construct()
    {
        $this->migrator = new MigrationService();
    }

    public function handle(array $args = []): int
    {
        if (!empty($args[0])) {
            switch ($args[0]) {
                case 'rollback':
                    return $this->rollback();
                case 'fresh':
                    return $this->fresh();
                default:
                    $this->error("Unknown option: {$args[0]}");
                    $this->showHelp();
                    return 1;
            }
        }

        return $this->migrate();
    }

    protected function migrate(): int
    {
        $this->info('Running migrations...');
        
        $ran = $this->migrator->migrate();

        if (empty($ran)) {
            $this->info('Nothing to migrate.');
            return 0;
        }

        foreach ($ran as $migration) {
            $this->info("Migrated: $migration");
        }

        return 0;
    }

    protected function rollback(): int
    {
        $this->info('Rolling back migrations...');
        
        $rolled = $this->migrator->rollback();

        if (empty($rolled)) {
            $this->info('Nothing to rollback.');
            return 0;
        }

        foreach ($rolled as $migration) {
            $this->info("Rolled back: $migration");
        }

        return 0;
    }

    protected function fresh(): int
    {
        if (!$this->confirm('This will drop all tables and re-run migrations. Continue?', false)) {
            return 0;
        }

        $this->info('Dropping all tables...');
        // TODO: Implement drop all tables
        
        return $this->migrate();
    }

    protected function showHelp(): void
    {
        echo "\nUsage: humo migrate [option]\n\n";
        echo "Options:\n";
        echo "  <none>     Run pending migrations\n";
        echo "  rollback   Rollback the last batch of migrations\n";
        echo "  fresh      Drop all tables and re-run migrations\n";
        echo "\n";
    }
} 
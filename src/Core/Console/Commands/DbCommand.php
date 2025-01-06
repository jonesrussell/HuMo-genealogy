<?php

namespace HumoGen\Core\Console\Commands;

use HumoGen\Core\Console\Command;

class DbCommand extends Command
{
    protected string $signature = 'db';
    protected string $description = 'Database management commands';

    protected string $dumpsDir = 'database/dumps';
    protected string $dbContainer = 'mysql';

    public function handle(array $args = []): int
    {
        if (empty($args[0])) {
            $this->error("Subcommand is required.");
            $this->showHelp();
            return 1;
        }

        switch ($args[0]) {
            case 'export':
                return $this->export($args[1] ?? $this->getCurrentBranch());
            case 'import':
                return $this->import($args[1] ?? $this->getCurrentBranch());
            case 'list':
                return $this->listDumps();
            case 'switch':
                return $this->switchDb();
            default:
                $this->error("Unknown subcommand: {$args[0]}");
                $this->showHelp();
                return 1;
        }
    }

    protected function export(string $name): int
    {
        $this->ensureDumpsDir();
        
        $dumpFile = "{$this->dumpsDir}/$name.sql";
        $this->info("Exporting database to $dumpFile...");

        $command = sprintf(
            'docker compose exec -T %s mysqldump -u"%s" -p"%s" "%s" > %s',
            $this->dbContainer,
            $_ENV['MYSQL_USER'] ?? 'root',
            $_ENV['MYSQL_PASSWORD'] ?? '',
            $_ENV['MYSQL_DATABASE'] ?? 'humogen',
            $dumpFile
        );

        exec($command, $output, $result);

        if ($result !== 0) {
            $this->error("Failed to export database");
            return 1;
        }

        $this->info("Database exported successfully!");
        return 0;
    }

    protected function import(string $name): int
    {
        $dumpFile = "{$this->dumpsDir}/$name.sql";
        
        if (!file_exists($dumpFile)) {
            $this->error("Dump file not found: $dumpFile");
            return 1;
        }

        $this->info("Importing database from $dumpFile...");

        $command = sprintf(
            'docker compose exec -T %s mysql -u"%s" -p"%s" "%s" < %s',
            $this->dbContainer,
            $_ENV['MYSQL_USER'] ?? 'root',
            $_ENV['MYSQL_PASSWORD'] ?? '',
            $_ENV['MYSQL_DATABASE'] ?? 'humogen',
            $dumpFile
        );

        exec($command, $output, $result);

        if ($result !== 0) {
            $this->error("Failed to import database");
            return 1;
        }

        $this->info("Database imported successfully!");
        return 0;
    }

    protected function listDumps(): int
    {
        $this->ensureDumpsDir();
        
        $this->info("Available database dumps:");
        $dumps = glob("{$this->dumpsDir}/*.sql");
        
        if (empty($dumps)) {
            echo "No dumps found.\n";
            return 0;
        }

        foreach ($dumps as $dump) {
            $name = basename($dump);
            $size = $this->formatSize(filesize($dump));
            $modified = date("Y-m-d H:i:s", filemtime($dump));
            printf("%-30s %10s  %s\n", $name, $size, $modified);
        }

        return 0;
    }

    protected function switchDb(): int
    {
        $currentBranch = $this->getCurrentBranch();
        $this->info("Current branch: $currentBranch");

        // Export current database state
        if ($this->export($currentBranch) !== 0) {
            return 1;
        }

        // Get target branch
        $targetBranch = $this->ask("Enter target branch name to import", true);

        // Import target database
        return $this->import($targetBranch);
    }

    protected function getCurrentBranch(): string
    {
        return trim(shell_exec('git branch --show-current'));
    }

    protected function ensureDumpsDir(): void
    {
        if (!is_dir($this->dumpsDir)) {
            mkdir($this->dumpsDir, 0755, true);
        }
    }

    protected function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $bytes;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return sprintf("%.2f %s", $size, $units[$unit]);
    }

    protected function showHelp(): void
    {
        echo "\nUsage: humo db SUBCOMMAND [options]\n\n";
        echo "Available subcommands:\n";
        echo "  export [name]    Export database to a dump file (default: branch name)\n";
        echo "  import [name]    Import database from a dump file (default: branch name)\n";
        echo "  list            List available database dumps\n";
        echo "  switch          Export current branch DB and import target branch DB\n";
        echo "\n";
    }
} 
<?php

namespace HumoGen\Core\Console\Commands;

use HumoGen\Core\Console\Command;

class DebugCommand extends Command
{
    protected string $signature = 'debug';
    protected string $description = 'Show debug information about the application';

    public function handle(array $args = []): int
    {
        $type = $args[0] ?? 'all';

        switch ($type) {
            case 'env':
                return $this->debugEnv();
            case 'docker':
                return $this->debugDocker();
            case 'db':
                return $this->debugDatabase();
            case 'all':
                return $this->debugAll();
            default:
                $this->error("Unknown debug type: $type");
                return 1;
        }
    }

    protected function debugEnv(): int
    {
        $this->info("Environment Information:");
        $this->info("----------------------");
        
        echo "Current Directory: " . getcwd() . "\n";
        echo "Current Branch: " . trim(shell_exec('git branch --show-current')) . "\n\n";
        
        echo "Environment Variables:\n";
        if (file_exists('.env')) {
            echo "Found .env file:\n";
            $env = file_get_contents('.env');
            $lines = array_filter(
                explode("\n", $env),
                fn($line) => !empty($line) && $line[0] !== '#'
            );
            sort($lines);
            echo implode("\n", $lines) . "\n";
        } else {
            echo "No .env file found\n";
        }

        return 0;
    }

    protected function debugDocker(): int
    {
        $this->info("Docker Status:");
        $this->info("-------------");

        // Check Docker version
        $dockerVersion = shell_exec('docker version --format "{{.Server.Version}}"');
        echo "Docker Version: $dockerVersion";

        // Check Docker Compose version
        $composeVersion = shell_exec('docker compose version');
        echo "Docker Compose Version: $composeVersion";

        // Check container status
        echo "\nContainer Status:\n";
        echo shell_exec('docker compose ps');

        // Show container logs
        echo "\nContainer Logs (last 10 lines):\n";
        echo "PHP Container:\n";
        echo shell_exec('docker compose logs --tail=10 php');
        echo "\nMySQL Container:\n";
        echo shell_exec('docker compose logs --tail=10 mysql');

        return 0;
    }

    protected function debugDatabase(): int
    {
        $this->info("Database Connection Info:");
        $this->info("-----------------------");

        $db = app()->getService('db');
        
        echo "Database Container: mysql\n";
        echo "Database Name: " . ($_ENV['MYSQL_DATABASE'] ?? 'Not set') . "\n";
        echo "Database User: " . ($_ENV['MYSQL_USER'] ?? 'Not set') . "\n";
        echo "Database Host: " . ($_ENV['MYSQL_HOST'] ?? 'Not set') . "\n";
        echo "Database Port: " . ($_ENV['MYSQL_PORT'] ?? 'Not set') . "\n\n";

        try {
            $db->query("SELECT 1");
            $this->info("✓ Database connection successful");

            // Get database size
            $sql = "SELECT table_schema AS 'Database',
                   ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
                   FROM information_schema.tables
                   WHERE table_schema = ?
                   GROUP BY table_schema";
            
            $stmt = $db->query($sql, [$_ENV['MYSQL_DATABASE']]);
            $result = $stmt->fetch();
            
            if ($result) {
                echo "\nDatabase Size: {$result['Size (MB)']} MB\n";
            }
        } catch (\Exception $e) {
            $this->error("✗ Database connection failed");
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function debugAll(): int
    {
        $this->info("=== HuMo-genealogy Debug Information ===");
        $this->info("=======================================");
        echo "\n";

        $result = $this->debugEnv();
        if ($result !== 0) return $result;

        echo "\n";
        $result = $this->debugDocker();
        if ($result !== 0) return $result;

        echo "\n";
        $result = $this->debugDatabase();
        if ($result !== 0) return $result;

        return 0;
    }
} 
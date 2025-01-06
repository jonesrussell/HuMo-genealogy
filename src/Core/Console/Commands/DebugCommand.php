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
        foreach ($_ENV as $key => $value) {
            if (!str_starts_with($key, 'MYSQL_PASSWORD')) {
                echo "$key=$value\n";
            }
        }

        return 0;
    }

    protected function debugDatabase(): int
    {
        $this->info("Database Connection Info:");
        $this->info("-----------------------");

        $db = app()->getService('db');
        
        echo "Database Host: " . ($_ENV['MYSQL_HOST'] ?? 'mysql') . "\n";
        echo "Database Name: " . ($_ENV['MYSQL_DATABASE'] ?? 'humogen') . "\n";
        echo "Database User: " . ($_ENV['MYSQL_USER'] ?? 'root') . "\n";
        echo "Database Port: " . ($_ENV['MYSQL_PORT'] ?? '3306') . "\n\n";

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

            // Show tables
            $sql = "SHOW TABLES";
            $stmt = $db->query($sql);
            $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($tables)) {
                echo "\nTables:\n";
                foreach ($tables as $table) {
                    echo "- $table\n";
                }
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
        $result = $this->debugDatabase();
        if ($result !== 0) return $result;

        return 0;
    }
} 
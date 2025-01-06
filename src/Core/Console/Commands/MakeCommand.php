<?php

namespace HumoGen\Core\Console\Commands;

use HumoGen\Core\Console\Command;

class MakeCommand extends Command
{
    protected string $signature = 'make';
    protected string $description = 'Create a new class file';

    protected array $types = [
        'migration' => [
            'path' => 'database/migrations',
            'stub' => 'database/migrations/migration.stub',
            'prefix' => true
        ],
        'seeder' => [
            'path' => 'database/seeders',
            'stub' => 'database/seeders/seeder.stub',
            'suffix' => 'Seeder'
        ],
        'factory' => [
            'path' => 'database/factories',
            'stub' => 'database/factories/factory.stub',
            'suffix' => 'Factory'
        ]
    ];

    public function handle(array $args = []): int
    {
        if (empty($args[0])) {
            $this->error("Type argument is required.");
            $this->showHelp();
            return 1;
        }

        if (empty($args[1])) {
            $this->error("Name argument is required.");
            $this->showHelp();
            return 1;
        }

        $type = strtolower($args[0]);
        $name = $args[1];

        if (!isset($this->types[$type])) {
            $this->error("Unknown type: $type");
            $this->showHelp();
            return 1;
        }

        return $this->makeFile($type, $name);
    }

    protected function makeFile(string $type, string $name): int
    {
        $config = $this->types[$type];
        
        // Create the target filename
        $filename = $name;
        if (!empty($config['prefix'])) {
            $filename = $this->getTimestamp() . '_' . $filename;
        }
        if (!empty($config['suffix'])) {
            $filename .= $config['suffix'];
        }
        $filename .= '.php';

        // Ensure directory exists
        if (!is_dir($config['path'])) {
            mkdir($config['path'], 0755, true);
        }

        // Check if file already exists
        $filepath = $config['path'] . '/' . $filename;
        if (file_exists($filepath)) {
            $this->error("File already exists: $filepath");
            return 1;
        }

        // Read stub file
        if (!file_exists($config['stub'])) {
            $this->error("Stub file not found: {$config['stub']}");
            return 1;
        }

        $content = file_get_contents($config['stub']);
        $content = str_replace('DummyClass', $name, $content);

        // Write the new file
        if (file_put_contents($filepath, $content) === false) {
            $this->error("Failed to create file: $filepath");
            return 1;
        }

        $this->info("Created $type: $filepath");
        return 0;
    }

    protected function getTimestamp(): string
    {
        return date('Y_m_d_His');
    }

    protected function showHelp(): void
    {
        echo "\nUsage: humo make TYPE NAME\n\n";
        echo "Available types:\n";
        foreach ($this->types as $type => $config) {
            echo "  $type\n";
        }
        echo "\n";
    }
} 
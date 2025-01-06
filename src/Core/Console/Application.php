<?php

namespace HumoGen\Core\Console;

class Application
{
    protected array $commands = [];
    protected array $defaultCommands = [
        Commands\MigrateCommand::class,
        Commands\SeedCommand::class,
        Commands\DebugCommand::class,
        Commands\MakeCommand::class,
        Commands\DbCommand::class,
        Commands\ComposerCommand::class,
    ];

    public function __construct()
    {
        $this->registerDefaultCommands();
    }

    /**
     * Register the default commands.
     */
    protected function registerDefaultCommands(): void
    {
        foreach ($this->defaultCommands as $command) {
            $this->add(new $command);
        }
    }

    /**
     * Add a command to the application.
     */
    public function add(Command $command): void
    {
        $this->commands[$command->getSignature()] = $command;
    }

    /**
     * Run the console application.
     */
    public function run(array $args = []): int
    {
        $command = $args[1] ?? '--help';

        if ($command === '--help' || $command === '-h') {
            return $this->showHelp();
        }

        if (!isset($this->commands[$command])) {
            $this->showError("Command '$command' not found.");
            return 1;
        }

        try {
            return $this->commands[$command]->handle(array_slice($args, 2));
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
            return 1;
        }
    }

    /**
     * Show the help screen.
     */
    protected function showHelp(): int
    {
        echo "HuMo-genealogy CLI Tool\n\n";
        echo "Usage: humo COMMAND [OPTIONS]\n\n";
        echo "Available commands:\n";

        $commands = $this->commands;
        ksort($commands);

        foreach ($commands as $name => $command) {
            printf("  %-20s %s\n", $name, $command->getDescription());
        }

        echo "\n";
        return 0;
    }

    /**
     * Show an error message.
     */
    protected function showError(string $message): void
    {
        echo "\033[31mError: $message\033[0m\n";
    }
} 
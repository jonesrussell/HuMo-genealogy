<?php

namespace HumoGen\Core\Console\Commands;

use HumoGen\Core\Console\Command;

class ComposerCommand extends Command
{
    protected string $signature = 'composer';
    protected string $description = 'Run composer commands';

    public function handle(array $args = []): int
    {
        if (empty($args[0])) {
            $this->error('Command argument is required.');
            $this->showHelp();
            return 1;
        }

        $command = $args[0];
        $extraArgs = array_slice($args, 1);

        switch ($command) {
            case 'dump':
            case 'dump-autoload':
                return $this->runComposer('dump-autoload', $extraArgs);
            case 'install':
                return $this->runComposer('install', $extraArgs);
            case 'update':
                return $this->runComposer('update', $extraArgs);
            default:
                $this->error("Unknown composer command: $command");
                $this->showHelp();
                return 1;
        }
    }

    protected function runComposer(string $command, array $args = []): int
    {
        $composerBin = $this->findComposer();
        $argString = implode(' ', array_map('escapeshellarg', $args));
        $fullCommand = "$composerBin $command $argString";

        passthru($fullCommand, $result);
        return $result;
    }

    protected function findComposer(): string
    {
        if (file_exists(getcwd() . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }

        return 'composer';
    }

    protected function showHelp(): void
    {
        echo "\nUsage: humo composer COMMAND [options]\n\n";
        echo "Available commands:\n";
        echo "  dump           Regenerate autoload files\n";
        echo "  dump-autoload  Regenerate autoload files\n";
        echo "  install        Install dependencies\n";
        echo "  update        Update dependencies\n";
        echo "\n";
    }
} 
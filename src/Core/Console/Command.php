<?php

namespace HumoGen\Core\Console;

abstract class Command
{
    /**
     * The name and signature of the console command.
     */
    protected string $signature;

    /**
     * The console command description.
     */
    protected string $description = '';

    /**
     * Execute the console command.
     */
    abstract public function handle(array $args = []): int;

    /**
     * Get the command signature.
     */
    public function getSignature(): string
    {
        return $this->signature;
    }

    /**
     * Get the command description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Write a message to the console.
     */
    protected function info(string $message): void
    {
        echo "\033[32m$message\033[0m\n";
    }

    /**
     * Write an error message to the console.
     */
    protected function error(string $message): void
    {
        echo "\033[31m$message\033[0m\n";
    }

    /**
     * Write a warning message to the console.
     */
    protected function warn(string $message): void
    {
        echo "\033[33m$message\033[0m\n";
    }

    /**
     * Ask the user for input.
     */
    protected function ask(string $question, bool $required = false): string
    {
        echo "$question: ";
        $handle = fopen("php://stdin", "r");
        $answer = trim(fgets($handle));
        fclose($handle);

        if ($required && empty($answer)) {
            $this->error("A value is required.");
            return $this->ask($question, true);
        }

        return $answer;
    }

    /**
     * Ask the user for confirmation.
     */
    protected function confirm(string $question, bool $default = false): bool
    {
        $defaultText = $default ? 'Y/n' : 'y/N';
        $answer = $this->ask("$question [$defaultText]");

        if (empty($answer)) {
            return $default;
        }

        return strtolower($answer[0]) === 'y';
    }
} 
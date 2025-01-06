<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use HumoGen\Core\Console\Commands\MigrateCommand;

class MigrateCommandTest extends TestCase
{
    protected MigrateCommand $command;
    protected $stdin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new MigrateCommand();
    }

    public function testMigrateReturnsSuccessOnEmptyMigrations(): void
    {
        $result = $this->command->handle([]);
        $this->assertEquals(0, $result);
    }

    public function testMigrateShowsHelpOnInvalidOption(): void
    {
        $result = $this->command->handle(['invalid']);
        $this->assertEquals(1, $result);
    }

    public function testMigrateFreshRequiresConfirmation(): void
    {
        // Mock user input to return 'no'
        $this->mockUserInput("n\n");
        
        $result = $this->command->handle(['fresh']);
        $this->assertEquals(0, $result);
    }

    /**
     * Mock user input for testing.
     */
    protected function mockUserInput(string $input): void
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $input);
        rewind($stream);
        
        // Backup and replace STDIN
        $this->stdin = STDIN;
        define('STDIN', $stream);
    }

    protected function tearDown(): void
    {
        if (isset($this->stdin)) {
            define('STDIN', $this->stdin);
        }
        parent::tearDown();
    }
} 
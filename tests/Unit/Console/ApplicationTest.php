<?php

namespace Tests\Unit\Console;

use Tests\TestCase;
use HumoGen\Core\Console\Application;
use HumoGen\Core\Console\Command;

class ApplicationTest extends TestCase
{
    protected Application $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = new Application();
    }

    public function testShowsHelpWithNoArguments(): void
    {
        ob_start();
        $result = $this->app->run(['humo']);
        $output = ob_get_clean();

        $this->assertEquals(0, $result);
        $this->assertStringContainsString('HuMo-genealogy CLI Tool', $output);
        $this->assertStringContainsString('Available commands:', $output);
    }

    public function testShowsErrorForUnknownCommand(): void
    {
        ob_start();
        $result = $this->app->run(['humo', 'unknown']);
        $output = ob_get_clean();

        $this->assertEquals(1, $result);
        $this->assertStringContainsString('Error: Command', $output);
        $this->assertStringContainsString('not found', $output);
    }

    public function testCanRegisterAndRunCommand(): void
    {
        // Create a test command
        $command = new class extends Command {
            protected string $signature = 'test';
            protected string $description = 'Test command';
            
            public function handle(array $args = []): int
            {
                echo "Test command executed";
                return 0;
            }
        };

        // Register and run the command
        $this->app->add($command);

        ob_start();
        $result = $this->app->run(['humo', 'test']);
        $output = ob_get_clean();

        $this->assertEquals(0, $result);
        $this->assertStringContainsString('Test command executed', $output);
    }
} 
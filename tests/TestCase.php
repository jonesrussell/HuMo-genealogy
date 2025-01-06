<?php

namespace Tests;

use HumoGen\Core\Database\Connection;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected ?Connection $db = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up SQLite in-memory database for testing
        $this->db = new Connection(
            'sqlite',
            ':memory:',
            '',
            '',
            '0',
            'utf8mb4'
        );

        // Register database service
        app()->bind('db', fn() => $this->db);
    }

    protected function tearDown(): void
    {
        $this->db = null;
        parent::tearDown();
    }

    /**
     * Create a temporary file for testing.
     */
    protected function createTempFile(string $content = ''): string
    {
        $file = tempnam(sys_get_temp_dir(), 'humo_test_');
        file_put_contents($file, $content);
        return $file;
    }

    /**
     * Remove a temporary file.
     */
    protected function removeTempFile(string $file): void
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }
} 
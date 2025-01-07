<?php

namespace Tests\Unit\Core;

use HumoGen\Core\Application;
use HumoGen\Core\Contracts\ContainerInterface;
use HumoGen\Core\Contracts\DatabaseInterface;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    private Application $app;

    protected function setUp(): void
    {
        $this->app = Application::getInstance();

        // Mock the database connection for testing
        $mockDb = $this->createMock(DatabaseInterface::class);
        $this->app->getContainer()->instance(DatabaseInterface::class, $mockDb);
        $this->app->getContainer()->instance('db', $mockDb);
    }

    public function testApplicationIsASingleton(): void
    {
        $app1 = Application::getInstance();
        $app2 = Application::getInstance();
        
        $this->assertSame($app1, $app2);
    }

    public function testContainerIsAvailable(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->app->getContainer());
    }

    public function testDatabaseServiceIsRegistered(): void
    {
        $db = $this->app->getService(DatabaseInterface::class);
        $this->assertInstanceOf(DatabaseInterface::class, $db);
    }

    public function testLegacyDatabaseServiceIsRegistered(): void
    {
        $db = $this->app->getService('db');
        $this->assertInstanceOf(DatabaseInterface::class, $db);
    }

    public function testBindingService(): void
    {
        $this->app->bind('test', fn() => new \stdClass());
        $service = $this->app->getService('test');
        
        $this->assertInstanceOf(\stdClass::class, $service);
    }

    public function testSingletonService(): void
    {
        $this->app->singleton('singleton', fn() => new \stdClass());
        
        $instance1 = $this->app->getService('singleton');
        $instance2 = $this->app->getService('singleton');
        
        $this->assertSame($instance1, $instance2);
    }
} 
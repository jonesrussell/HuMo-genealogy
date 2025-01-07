<?php

namespace Tests\Unit\Core\Providers;

use HumoGen\Core\Application;
use HumoGen\Core\Contracts\DatabaseInterface;
use HumoGen\Core\Providers\DatabaseServiceProvider;
use PHPUnit\Framework\TestCase;

class DatabaseServiceProviderTest extends TestCase
{
    private Application $app;
    private DatabaseServiceProvider $provider;

    protected function setUp(): void
    {
        $this->app = Application::getInstance();
        $this->provider = new DatabaseServiceProvider();
    }

    public function testProviderRegistersServices(): void
    {
        $this->provider->register($this->app);

        $this->assertTrue($this->app->getContainer()->has(DatabaseInterface::class));
        $this->assertTrue($this->app->getContainer()->has('db'));
    }

    public function testProviderReturnsCorrectServices(): void
    {
        $services = $this->provider->provides();

        $this->assertContains(DatabaseInterface::class, $services);
        $this->assertContains('db', $services);
    }

    public function testProviderIsNotDeferred(): void
    {
        $this->assertFalse($this->provider->isDeferred());
    }

    public function testDatabaseServiceIsSingleton(): void
    {
        $this->provider->register($this->app);

        $db1 = $this->app->getService(DatabaseInterface::class);
        $db2 = $this->app->getService(DatabaseInterface::class);

        $this->assertSame($db1, $db2);
    }

    public function testLegacyDbAliasReturnsSameInstance(): void
    {
        $this->provider->register($this->app);

        $db1 = $this->app->getService(DatabaseInterface::class);
        $db2 = $this->app->getService('db');

        $this->assertSame($db1, $db2);
    }
} 
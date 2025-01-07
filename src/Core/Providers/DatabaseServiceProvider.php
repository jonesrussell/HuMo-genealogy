<?php

namespace HumoGen\Core\Providers;

use HumoGen\Core\Application;
use HumoGen\Core\Contracts\DatabaseInterface;
use HumoGen\Core\Contracts\ServiceProviderInterface;
use HumoGen\Core\Database\Connection;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    /**
     * Register the database service.
     */
    public function register(Application $app): void
    {
        $app->singleton(DatabaseInterface::class, function () {
            // We're in Docker if we're running the command through docker compose exec
            $inDocker = getenv('DOCKER_CONTAINER') === 'true';
            
            // Use internal Docker port if running in container, otherwise use host port
            $port = $inDocker ? '3306' : ($_ENV['DB_PORT'] ?? '3306');

            return new Connection(
                $_ENV['DB_HOST'],
                $_ENV['DB_DATABASE'],
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD'],
                $port
            );
        });

        // For backward compatibility, also register as 'db'
        $app->singleton('db', function ($app) {
            return $app->getService(DatabaseInterface::class);
        });
    }

    /**
     * Bootstrap any database services.
     */
    public function boot(Application $app): void
    {
        // No bootstrapping needed for database service
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            DatabaseInterface::class,
            'db'
        ];
    }

    /**
     * Determine if the provider is deferred.
     */
    public function isDeferred(): bool
    {
        return false;
    }
} 
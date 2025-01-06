<?php

namespace HumoGen\Core;

use Dotenv\Dotenv;

class Application
{
    protected static ?self $instance = null;
    protected array $services = [];

    protected function __construct()
    {
        $this->bootstrap();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function bootstrap(): void
    {
        $this->loadEnvironment();
        $this->registerServices();
    }

    protected function loadEnvironment(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();

        // Set defaults if not present
        $_ENV['APP_NAME'] = $_ENV['APP_NAME'] ?? 'HuMo-genealogy';
        $_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'local';
        $_ENV['DB_HOST'] = $_ENV['DB_HOST'] ?? 'mariadb';
        $_ENV['DB_DATABASE'] = $_ENV['DB_DATABASE'] ?? 'humogen';
        $_ENV['DB_USERNAME'] = $_ENV['DB_USERNAME'] ?? 'root';
        $_ENV['DB_PASSWORD'] = $_ENV['DB_PASSWORD'] ?? '';
        $_ENV['DB_PORT'] = $_ENV['DB_PORT'] ?? '3306';

        // Map legacy variables if they exist
        if (isset($_ENV['MYSQL_HOST'])) {
            $_ENV['DB_HOST'] = $_ENV['MYSQL_HOST'];
        }
        if (isset($_ENV['MYSQL_DATABASE'])) {
            $_ENV['DB_DATABASE'] = $_ENV['MYSQL_DATABASE'];
        }
        if (isset($_ENV['MYSQL_USER'])) {
            $_ENV['DB_USERNAME'] = $_ENV['MYSQL_USER'];
        }
        if (isset($_ENV['MYSQL_PASSWORD'])) {
            $_ENV['DB_PASSWORD'] = $_ENV['MYSQL_PASSWORD'];
        }
        if (isset($_ENV['MYSQL_PORT'])) {
            $_ENV['DB_PORT'] = $_ENV['MYSQL_PORT'];
        }

        // Validate that we have the minimum required variables
        $dotenv->required([
            'APP_NAME',
            'APP_ENV',
            'DB_HOST',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD'
        ]);
    }

    protected function registerServices(): void
    {
        // We're in Docker if we're running the command through docker compose exec
        $inDocker = getenv('DOCKER_CONTAINER') === 'true';
        
        // Use internal Docker port if running in container, otherwise use host port
        $port = $inDocker ? '3306' : ($_ENV['DB_PORT'] ?? '3306');

        // Register database service
        $this->services['db'] = new Database\Connection(
            $_ENV['DB_HOST'],
            $_ENV['DB_DATABASE'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            $port
        );
    }

    public function getService(string $name)
    {
        if (!isset($this->services[$name])) {
            throw new \RuntimeException("Service '$name' not found.");
        }

        return $this->services[$name];
    }
} 
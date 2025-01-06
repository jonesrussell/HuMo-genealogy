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

        // Only validate essential variables with fallbacks
        $dotenv->required('APP_NAME')->default('HuMo-genealogy');
        $dotenv->required('APP_ENV')->default('local');
        $dotenv->required('DB_HOST')->default('mariadb');
        $dotenv->required('DB_DATABASE')->default('humogen');
        $dotenv->required('DB_USERNAME')->default('root');
        $dotenv->required('DB_PASSWORD')->default('');
    }

    protected function registerServices(): void
    {
        // Register database service
        $this->services['db'] = new Database\Connection(
            $_ENV['DB_HOST'],
            $_ENV['DB_DATABASE'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_PORT'] ?? '3306'
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
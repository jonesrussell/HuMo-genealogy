<?php

namespace HumoGen\Core;

use Dotenv\Dotenv;

class Application
{
    private static ?self $instance = null;
    private array $container = [];

    private function __construct()
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

    private function bootstrap(): void
    {
        $this->loadEnvironment();
        $this->registerErrorHandling();
        $this->initializeSession();
        $this->registerServices();
    }

    private function loadEnvironment(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
        $dotenv->required([
            'APP_NAME',
            'APP_ENV',
            'DB_HOST',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
        ]);
    }

    private function registerErrorHandling(): void
    {
        error_reporting(E_ALL);
        $debug = $_ENV['APP_DEBUG'] ?? false;
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('display_startup_errors', $debug ? '1' : '0');

        if (!$debug) {
            ini_set('log_errors', '1');
            ini_set('error_log', dirname(__DIR__, 2) . '/storage/logs/php-error.log');
        }
    }

    private function initializeSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Regenerate session ID periodically for security
        if (!isset($_SESSION['_last_regeneration']) || 
            (time() - $_SESSION['_last_regeneration']) > 3600
        ) {
            session_regenerate_id(true);
            $_SESSION['_last_regeneration'] = time();
        }
    }

    private function registerServices(): void
    {
        // Register core services
        $this->container['config'] = new Config();
        $this->container['db'] = new Database();
    }

    public function getService(string $name): mixed
    {
        return $this->container[$name] ?? null;
    }
} 
<?php

namespace HumoGen\Core;

class Config
{
    private array $config = [];

    public function __construct()
    {
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        $this->config = [
            'app' => [
                'name' => $_ENV['APP_NAME'],
                'env' => $_ENV['APP_ENV'],
                'debug' => (bool) ($_ENV['APP_DEBUG'] ?? false),
                'url' => $_ENV['APP_URL'],
            ],
            'database' => [
                'driver' => $_ENV['DB_CONNECTION'],
                'host' => $_ENV['DB_HOST'],
                'port' => $_ENV['DB_PORT'],
                'database' => $_ENV['DB_DATABASE'],
                'username' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD'],
                'prefix' => $_ENV['DB_PREFIX'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'mail' => [
                'driver' => $_ENV['MAIL_MAILER'],
                'host' => $_ENV['MAIL_HOST'],
                'port' => $_ENV['MAIL_PORT'],
                'username' => $_ENV['MAIL_USERNAME'],
                'password' => $_ENV['MAIL_PASSWORD'],
                'encryption' => $_ENV['MAIL_ENCRYPTION'],
                'from' => [
                    'address' => $_ENV['MAIL_FROM_ADDRESS'],
                    'name' => $_ENV['MAIL_FROM_NAME'],
                ],
            ],
        ];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $config = $this->config;

        foreach ($keys as $segment) {
            if (!isset($config[$segment])) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    public function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $config[$segment] = $value;
                break;
            }

            if (!isset($config[$segment])) {
                $config[$segment] = [];
            }

            $config = &$config[$segment];
        }
    }
} 
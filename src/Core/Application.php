<?php

namespace HumoGen\Core;

use Dotenv\Dotenv;
use HumoGen\Core\Container\Container;
use HumoGen\Core\Contracts\ContainerInterface;
use HumoGen\Core\Contracts\ServiceProviderInterface;
use HumoGen\Core\Providers\DatabaseServiceProvider;

class Application
{
    protected static ?self $instance = null;
    protected ContainerInterface $container;
    protected array $serviceProviders = [];
    protected array $loadedProviders = [];
    protected array $deferredProviders = [];

    protected function __construct()
    {
        $this->container = new Container();
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
        $this->registerBaseServiceProviders();
        $this->bootServiceProviders();
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

    protected function registerBaseServiceProviders(): void
    {
        $this->register(new DatabaseServiceProvider());
    }

    /**
     * Register a service provider.
     */
    public function register(ServiceProviderInterface $provider): void
    {
        $providerClass = get_class($provider);

        if (isset($this->loadedProviders[$providerClass])) {
            return;
        }

        if ($provider->isDeferred()) {
            foreach ($provider->provides() as $service) {
                $this->deferredProviders[$service] = $provider;
            }
            return;
        }

        $provider->register($this);
        $this->serviceProviders[] = $provider;
        $this->loadedProviders[$providerClass] = true;
    }

    /**
     * Boot all registered service providers.
     */
    protected function bootServiceProviders(): void
    {
        foreach ($this->serviceProviders as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot($this);
            }
        }
    }

    /**
     * Load a deferred provider if the service is not loaded.
     */
    protected function loadDeferredProvider(string $service): void
    {
        if (!isset($this->deferredProviders[$service])) {
            return;
        }

        $provider = $this->deferredProviders[$service];
        $providerClass = get_class($provider);

        if (!isset($this->loadedProviders[$providerClass])) {
            $provider->register($this);
            $this->loadedProviders[$providerClass] = true;
        }
    }

    /**
     * Get a service from the container.
     *
     * @throws \HumoGen\Core\Container\BindingResolutionException
     */
    public function getService(string $name): mixed
    {
        $this->loadDeferredProvider($name);
        return $this->container->get($name);
    }

    /**
     * Get the service container instance.
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Register a binding with the container.
     */
    public function bind(string $abstract, mixed $concrete = null): void
    {
        $this->container->bind($abstract, $concrete);
    }

    /**
     * Register a shared binding with the container.
     */
    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $this->container->singleton($abstract, $concrete);
    }
} 
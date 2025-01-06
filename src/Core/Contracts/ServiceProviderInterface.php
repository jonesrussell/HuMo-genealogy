<?php

namespace HumoGen\Core\Contracts;

use HumoGen\Core\Application;

/**
 * Service Provider Interface
 * 
 * Defines the contract for service providers that register and bootstrap services.
 */
interface ServiceProviderInterface
{
    /**
     * Register any application services.
     * 
     * This method is called before all service providers are booted.
     * Use this method to bind things in the container.
     *
     * @param Application $app The application instance
     */
    public function register(Application $app): void;

    /**
     * Bootstrap any application services.
     * 
     * This method is called after all service providers are registered.
     * Use this method for any bootstrapping that requires other services.
     *
     * @param Application $app The application instance
     */
    public function boot(Application $app): void;

    /**
     * Get the services provided by the provider.
     *
     * @return array<string> Array of provided service names
     */
    public function provides(): array;

    /**
     * Determine if the provider is deferred.
     * 
     * Deferred providers are only loaded when one of their provided services is needed.
     *
     * @return bool True if the provider is deferred
     */
    public function isDeferred(): bool;
} 
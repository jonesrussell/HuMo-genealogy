<?php

namespace HumoGen\Core\Contracts;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Container Interface
 * 
 * PSR-11 compliant container interface with additional methods for service registration.
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Register a binding with the container.
     *
     * @param string $abstract The abstract type to register
     * @param mixed $concrete The concrete type or factory
     */
    public function bind(string $abstract, mixed $concrete = null): void;

    /**
     * Register a shared binding with the container.
     *
     * @param string $abstract The abstract type to register
     * @param mixed $concrete The concrete type or factory
     */
    public function singleton(string $abstract, mixed $concrete = null): void;

    /**
     * Register an existing instance as shared in the container.
     *
     * @param string $abstract The abstract type to register
     * @param mixed $instance The instance to register
     */
    public function instance(string $abstract, mixed $instance): void;

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract The abstract type to resolve
     * @return mixed The resolved instance
     * 
     * @throws \HumoGen\Core\Container\BindingResolutionException
     */
    public function make(string $abstract): mixed;

    /**
     * Determine if a given type is bound.
     *
     * @param string $abstract The abstract type to check
     */
    public function bound(string $abstract): bool;
} 
<?php

namespace HumoGen\Core\Container;

use Closure;
use HumoGen\Core\Contracts\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;

/**
 * PSR-11 compliant service container implementation.
 */
class Container implements ContainerInterface
{
    /**
     * The container's bindings.
     *
     * @var array<string, array{concrete: mixed, shared: bool}>
     */
    protected array $bindings = [];

    /**
     * The container's shared instances.
     *
     * @var array<string, mixed>
     */
    protected array $instances = [];

    /**
     * Register a binding with the container.
     */
    public function bind(string $abstract, mixed $concrete = null): void
    {
        $concrete = $concrete ?? $abstract;

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => false,
        ];
    }

    /**
     * Register a shared binding with the container.
     */
    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $concrete = $concrete ?? $abstract;

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => true,
        ];
    }

    /**
     * Register an existing instance as shared in the container.
     */
    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve the given type from the container.
     *
     * @throws BindingResolutionException
     */
    public function make(string $abstract): mixed
    {
        // If we have an instance, return it
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // If we don't have a binding, try to build it
        if (!isset($this->bindings[$abstract])) {
            if (class_exists($abstract)) {
                return $this->build($abstract);
            }

            throw new BindingResolutionException("No binding found for {$abstract}");
        }

        $concrete = $this->bindings[$abstract]['concrete'];
        $shared = $this->bindings[$abstract]['shared'];

        // If the concrete is a Closure, execute it
        if ($concrete instanceof Closure) {
            $instance = $concrete($this);
        } else {
            $instance = $this->build($concrete);
        }

        // If it's shared, store the instance
        if ($shared) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Build a concrete instance of a class.
     *
     * @throws BindingResolutionException
     */
    protected function build(string $concrete): object
    {
        try {
            $reflector = new ReflectionClass($concrete);

            if (!$reflector->isInstantiable()) {
                throw new BindingResolutionException("Target [$concrete] is not instantiable");
            }

            $constructor = $reflector->getConstructor();

            if (is_null($constructor)) {
                return new $concrete;
            }

            $dependencies = array_map(
                fn (ReflectionParameter $param) => $this->resolveDependency($param),
                $constructor->getParameters()
            );

            return $reflector->newInstanceArgs($dependencies);
        } catch (\ReflectionException $e) {
            throw new BindingResolutionException("Error resolving [$concrete]: " . $e->getMessage());
        }
    }

    /**
     * Resolve a constructor dependency.
     *
     * @throws BindingResolutionException
     */
    protected function resolveDependency(ReflectionParameter $dependency): mixed
    {
        if ($dependency->isDefaultValueAvailable()) {
            return $dependency->getDefaultValue();
        }

        $type = $dependency->getType();

        if (!$type || $type->isBuiltin()) {
            throw new BindingResolutionException(
                "Unresolvable dependency: {$dependency->getName()}"
            );
        }

        return $this->make($type->getName());
    }

    /**
     * Get a service from the container.
     *
     * @throws BindingResolutionException
     */
    public function get(string $id): mixed
    {
        return $this->make($id);
    }

    /**
     * Check if a service exists in the container.
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }

    /**
     * Determine if a given type is bound.
     */
    public function bound(string $abstract): bool
    {
        return $this->has($abstract);
    }
} 
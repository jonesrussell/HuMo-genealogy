# Dependency Injection Implementation Plan

## Overview

This document outlines the plan to implement proper dependency injection in the HuMo-genealogy project, moving away from the current service locator pattern to a more testable and maintainable approach.

## Current State

- Using service locator pattern through `app()` helper
- Services registered in Application singleton
- Direct service instantiation in constructors
- Tight coupling to concrete implementations

## Target State

- Constructor-based dependency injection
- Interface-based dependencies
- PSR-11 compliant container
- Service provider pattern
- Improved testability

## Implementation Plan

### 1. Core Interfaces

```php
// DatabaseInterface
interface DatabaseInterface {
    public function query(string $sql, array $params = []): \PDOStatement;
    public function beginTransaction(): bool;
    public function commit(): bool;
    public function rollBack(): bool;
    public function lastInsertId(): string;
}

// ContainerInterface (PSR-11)
interface ContainerInterface {
    public function get(string $id): mixed;
    public function has(string $id): bool;
}

// ServiceProviderInterface
interface ServiceProviderInterface {
    public function register(Application $app): void;
    public function boot(Application $app): void;
}
```

### 2. Service Container Implementation

```php
class Container implements ContainerInterface {
    protected array $bindings = [];
    protected array $instances = [];
    
    public function bind(string $abstract, $concrete = null): void
    public function singleton(string $abstract, $concrete = null): void
    public function instance(string $abstract, $instance): void
    public function get(string $id): mixed
    public function has(string $id): bool
    public function make(string $abstract): mixed
}
```

### 3. Example Service Provider

```php
class DatabaseServiceProvider implements ServiceProviderInterface {
    public function register(Application $app): void {
        $app->singleton(DatabaseInterface::class, function ($app) {
            return new Database(
                $app->get('config')->get('database')
            );
        });
    }
    
    public function boot(Application $app): void {
        // Additional setup after all services are registered
    }
}
```

### 4. Constructor Injection Example

```php
class Repository {
    public function __construct(
        protected DatabaseInterface $db,
        protected LoggerInterface $logger
    ) {}
}
```

## Migration Steps

1. **Create Interfaces**
   - Define core interfaces
   - Document interface contracts
   - Version interfaces appropriately

2. **Implement Container**
   - PSR-11 compliant container
   - Service provider support
   - Autowiring capabilities
   - Circular dependency detection

3. **Create Service Providers**
   - Database provider
   - Logger provider
   - Cache provider
   - Mail provider

4. **Refactor Existing Classes**
   - Move to constructor injection
   - Implement interfaces
   - Update tests
   - Document dependencies

## Testing Strategy

1. **Unit Testing**
   - Mock dependencies using interfaces
   - Test container bindings
   - Verify service provider registration
   - Check dependency resolution

2. **Integration Testing**
   - Test service provider boot order
   - Verify concrete implementations
   - Test dependency chains
   - Check configuration injection

## Example Test Cases

```php
class RepositoryTest extends TestCase {
    public function testDatabaseInjection(): void {
        $db = $this->createMock(DatabaseInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $repository = new Repository($db, $logger);
        
        // Test repository with mocked dependencies
    }
}
```

## Benefits

1. **Testability**
   - Easy dependency mocking
   - Isolated unit tests
   - Clear dependency contracts

2. **Maintainability**
   - Explicit dependencies
   - Interface-based design
   - Reduced coupling

3. **Flexibility**
   - Swappable implementations
   - Easier refactoring
   - Better reusability

## Future Enhancements

1. **Autowiring**
   - Automatic dependency resolution
   - Parameter type inference
   - Configuration injection

2. **Scoped Services**
   - Request scoped bindings
   - Contextual binding
   - Tagged services

3. **Performance Optimization**
   - Compiled container
   - Dependency cache
   - Lazy loading

## Timeline

1. **Week 1: Foundation**
   - Create interfaces
   - Implement basic container
   - Write initial tests

2. **Week 2: Service Providers**
   - Implement core providers
   - Add provider tests
   - Document provider system

3. **Week 3: Refactoring**
   - Update existing classes
   - Add constructor injection
   - Update documentation

4. **Week 4: Testing & Review**
   - Complete test coverage
   - Performance testing
   - Code review
   - Final documentation

## Next Steps

1. Review this implementation plan
2. Create core interfaces
3. Begin container implementation
4. Update test infrastructure 
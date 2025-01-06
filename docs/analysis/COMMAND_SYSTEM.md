# Command System Implementation

## Overview

The HuMo-genealogy command system provides a robust CLI interface for managing the application. It supports both Docker and non-Docker environments, with automatic environment detection and appropriate execution paths.

## Architecture

### Core Components

1. **Application Class** (`src/Core/Console/Application.php`)
   - Command registration and execution
   - Help system
   - Error handling

2. **Base Command Class** (`src/Core/Console/Command.php`)
   - Common command functionality
   - Output formatting
   - User interaction methods
   - Return code handling

### Available Commands

1. **Migration Commands** (`migrate`)
   ```bash
   humo migrate           # Run pending migrations
   humo migrate rollback  # Rollback last batch
   humo migrate fresh     # Drop all tables and migrate
   ```

2. **Database Commands** (`db`)
   ```bash
   humo db export [name]  # Export database to dump
   humo db import [name]  # Import database from dump
   humo db list          # List available dumps
   humo db switch        # Switch between branch databases
   ```

3. **Seeding Commands** (`db:seed`)
   ```bash
   humo db:seed                    # Run DatabaseSeeder
   humo db:seed SomeSeeder        # Run specific seeder
   ```

4. **Generator Commands** (`make`)
   ```bash
   humo make migration Name  # Create migration
   humo make seeder Name    # Create seeder
   humo make factory Name   # Create factory
   ```

5. **Debug Commands** (`debug`)
   ```bash
   humo debug      # Show all debug info
   humo debug env  # Show environment info
   humo debug db   # Show database info
   ```

## Implementation Details

### Command Execution Flow

1. **Environment Detection**
   ```bash
   # Check if running in Docker
   [ -f /.dockerenv ] || grep -q docker /proc/1/cgroup
   ```

2. **Docker Integration**
   - Automatic container health checks
   - Environment variable handling
   - Volume mounting for file access

3. **Database Operations**
   - Safe schema modifications
   - Transaction support
   - Error handling and rollback

### Templates and Stubs

1. **Migration Template**
   ```php
   class DummyClass extends Migration
   {
       public function up(): void
       {
           Schema::create('table_name', function (Table $table) {
               // Define table structure
           });
       }

       public function down(): void
       {
           Schema::dropIfExists('table_name');
       }
   }
   ```

2. **Seeder Template**
   ```php
   class DummyClass extends Seeder
   {
       public function run(): void
       {
           // Define seeding logic
       }
   }
   ```

3. **Factory Template**
   ```php
   class DummyClassFactory extends Factory
   {
       public function definition(): array
       {
           return [
               // Define factory attributes
           ];
       }

       protected function store(array $attributes): array
       {
           // Define storage logic
       }
   }
   ```

## Best Practices

1. **Code Style**
   - Follow PSR-12 standards
   - Use type hints
   - Document all methods
   - Keep methods under 20 lines
   - Use meaningful names

2. **Security**
   - Validate all input
   - Use prepared statements
   - Handle sensitive data carefully
   - Implement proper error handling

3. **Testing**
   - Write unit tests for commands
   - Test both success and failure paths
   - Mock database operations
   - Test Docker and non-Docker paths

## Testing Strategy

### Initial Testing Setup
1. **Basic Command Tests**
   ```php
   class MigrateCommandTest extends TestCase
   {
       public function testBasicMigration(): void
       {
           $command = new MigrateCommand();
           $result = $command->handle([]);
           $this->assertEquals(0, $result);
       }
   }
   ```

2. **Test Environment**
   - Use SQLite in-memory database for tests
   - Mock filesystem operations
   - Skip Docker-specific checks in tests

3. **Test Coverage**
   - Focus on core command logic
   - Test command registration
   - Test basic error handling
   - Test help system

### Test Organization
```
tests/
├── Unit/
│   └── Console/
│       ├── Commands/
│       │   ├── MigrateCommandTest.php
│       │   ├── SeedCommandTest.php
│       │   └── DebugCommandTest.php
│       └── ApplicationTest.php
└── TestCase.php
```

## Future Enhancements

1. **Planned Features**
   - Interactive mode for complex operations
   - Command scheduling
   - Plugin system for custom commands
   - Better progress indicators
   - Dry-run mode

2. **Performance Improvements**
   - Command autocompletion
   - Parallel execution where possible
   - Better caching of repeated operations

3. **Future Testing Improvements**
   - Advanced Testing
     - Docker environment integration tests
     - End-to-end command testing
     - Performance benchmarking
     - Cross-platform testing

   - Test Infrastructure
     - Parallel test execution
     - Test data factories
     - Snapshot testing
     - Coverage reporting

   - CI/CD Integration
     - Automated test matrix
     - Platform-specific test runs
     - Performance regression testing
     - Security scanning

   - Documentation Testing
     - Command example validation
     - Documentation coverage checks
     - API documentation testing
     - Schema validation

## Usage Examples

```bash
# Basic migration workflow
./bin/humo migrate
./bin/humo make migration CreateUsersTable
./bin/humo migrate fresh

# Database management
./bin/humo db export dev_snapshot
./bin/humo db import production_backup

# Development helpers
./bin/humo debug
./bin/humo make factory User
``` 
# Branching Strategy for HuMo-genealogy Modernization

## Overview

This document outlines the branching strategy for modernizing HuMo-genealogy. The goal is to maintain a stable main branch while systematically implementing modern PHP practices and infrastructure.

## Branch Structure

### Main Branches
- `main` - Production-ready code
- `develop` - Integration branch for features

### Feature Branches

#### 1. Infrastructure (`feature/modernization/docker`)
Focus: Development environment and tooling
```
├── Docker configuration
├── Development tools setup
├── Environment configuration
└── Dependency management
```

**Key Files**:
- `docker-compose.yml`
- `docker/`
- `.env.example`
- `composer.json`

#### 2. Core Architecture (`feature/modernization/core-foundation`)
Focus: Application foundation and architecture
```
├── PSR-4 autoloading
├── Service container
├── Configuration management
└── Error handling
```

**Key Files**:
- `src/Core/Application.php`
- `src/Core/Config.php`
- `src/Core/Container.php`
- `src/Core/ErrorHandler.php`

#### 3. Database Layer (`feature/modernization/database`)
Focus: Data access and models
```
├── Database abstraction
├── Model system
├── Repository pattern
└── Query builder
```

**Key Files**:
- `src/Core/Database.php`
- `src/Core/Model.php`
- `src/Core/Repository.php`
- `src/Models/`
- `src/Repositories/`

#### 4. Security (`feature/modernization/security`)
Focus: Security infrastructure
```
├── Session management
├── CSRF protection
├── Input validation
└── Security headers
```

**Key Files**:
- `src/Core/Security/`
- `src/Core/Session.php`
- `src/Core/Validation/`
- `public/.htaccess`

#### 5. Application Structure (`feature/modernization/routing`)
Focus: Request handling and routing
```
├── Routing system
├── Controllers
├── Middleware
└── Request/Response
```

**Key Files**:
- `src/Core/Router.php`
- `src/Controllers/`
- `src/Middleware/`
- `routes/`

#### 6. View Layer (`feature/modernization/views`)
Focus: Template and asset management
```
├── Template system
├── Asset pipeline
├── Frontend structure
└── Response formatting
```

**Key Files**:
- `src/Core/View/`
- `resources/views/`
- `resources/assets/`
- `public/assets/`

#### 7. Testing (`feature/modernization/testing`)
Focus: Testing infrastructure
```
├── PHPUnit setup
├── Test helpers
├── Initial test suite
└── CI configuration
```

**Key Files**:
- `tests/`
- `phpunit.xml`
- `.github/workflows/`

## Branching Rules

1. **Branch Creation**
   - Create from: `develop`
   - Naming: `feature/modernization/<area>`
   - Example: `feature/modernization/docker`

2. **Commits**
   - Atomic commits
   - Clear commit messages
   - Reference issues where applicable

3. **Code Review**
   - Required for all PRs
   - Must pass CI checks
   - Must maintain GPL compliance

4. **Merging**
   - Merge to: `develop`
   - Require: Code review
   - Method: Squash and merge

## Implementation Order

1. **Phase 1: Foundation**
   - Docker environment
   - Core architecture
   - Database layer

2. **Phase 2: Structure**
   - Routing system
   - Security
   - View layer

3. **Phase 3: Quality**
   - Testing
   - Documentation
   - Performance optimization

## Branch Lifecycle

1. **Creation**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/modernization/<area>
   ```

2. **Development**
   ```bash
   # Regular commits
   git add .
   git commit -m "feat(<area>): descriptive message"
   ```

3. **Updates**
   ```bash
   # Keep branch updated
   git fetch origin develop
   git rebase origin/develop
   ```

4. **Completion**
   ```bash
   # Final testing
   composer test
   composer cs-check
   composer stan
   ```

## Quality Gates

Each branch must pass:
- PHPUnit tests
- PHP CS Fixer checks
- PHPStan analysis
- Security checks
- GPL compliance review

## Documentation Requirements

Each branch should update:
- README.md (if needed)
- Relevant documentation in `/docs`
- PHPDoc blocks
- Change log

## Conflict Resolution

1. Keep `develop` as the source of truth
2. Resolve conflicts at rebase
3. Maintain feature isolation
4. Document breaking changes 
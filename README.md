# HuMo-genealogy

A modern PHP-based genealogy application.

## Development Setup

### Prerequisites
- Docker
- Docker Compose

### Quick Start
1. Clone the repository
```bash
git clone https://github.com/your-org/HuMo-genealogy.git
cd HuMo-genealogy
```

2. Copy environment file
```bash
cp .env.example .env
```

3. Start the development environment
```bash
docker compose up -d
```

4. Install dependencies
```bash
docker compose exec php composer install
```

### Development Tools

#### Code Style
The project uses PHP CS Fixer for code style enforcement. To check your code:
```bash
docker compose exec php composer cs-check
```

To automatically fix code style issues:
```bash
docker compose exec php composer cs-fix
```

#### Static Analysis
PHPStan is used for static analysis. To analyze your code:
```bash
docker compose exec php composer stan
```

#### Testing
PHPUnit is used for testing. To run tests:
```bash
docker compose exec php composer test
```

### Project Structure

```
HuMo-genealogy/
├── src/                    # Application source code
│   ├── Core/              # Core functionality
│   ├── Models/            # Database models
│   ├── Controllers/       # Request handlers
│   ├── Services/          # Business logic
│   └── Repositories/      # Data access layer
├── tests/                 # Test files
├── public/                # Web root
├── storage/               # Application storage
├── docker/               # Docker configuration
└── docs/                 # Documentation
```

## Contributing
Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License
This project is licensed under the GPL-2.0-or-later License - see the [COPYING](COPYING) file for details.

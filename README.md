# HuMo-genealogy

A modern PHP-based genealogy application that helps you manage and explore your family tree.

## Features

- Modern PHP 8.2+ architecture
- Docker-based development environment
- Clean and maintainable codebase
- Robust database design
- RESTful API support
- Secure authentication
- GEDCOM import/export
- Advanced search capabilities
- Mobile-friendly interface

## Technology Stack

- PHP 8.2+
- MariaDB 11.6+
- Nginx
- Docker & Docker Compose
- Composer for dependency management
- PHPUnit for testing
- PHPStan for static analysis
- PHP CS Fixer for code style

## Development Setup

### Prerequisites

- Docker Engine 24.0+
- Docker Compose V2
- Git
- Make (optional)

### Quick Start

1. Clone the repository
```bash
git clone https://github.com/humo-gen/HuMo-genealogy.git
cd HuMo-genealogy
```

2. Copy environment file and configure
```bash
cp .env.example .env
# Edit .env with your preferred settings
```

3. Start the development environment
```bash
docker compose up -d
```

4. Install dependencies
```bash
docker compose exec php composer install
```

5. Access the application
- Web: http://localhost:8080
- Database Admin: http://localhost:8081

### Development Tools

#### Code Style
```bash
# Check code style
docker compose exec php composer cs-check

# Fix code style
docker compose exec php composer cs-fix
```

#### Static Analysis
```bash
# Run PHPStan
docker compose exec php composer stan
```

#### Testing
```bash
# Run tests
docker compose exec php composer test
```

### Project Structure

```
HuMo-genealogy/
├── src/                    # Application source code
│   ├── Core/              # Core framework components
│   ├── Models/            # Database models
│   ├── Controllers/       # Request handlers
│   ├── Services/          # Business logic
│   └── Repositories/      # Data access layer
├── tests/                 # Test files
├── public/                # Web root
├── config/               # Configuration files
├── resources/            # Frontend resources
│   ├── views/            # Templates
│   ├── lang/             # Translations
│   ├── js/               # JavaScript files
│   └── css/             # Stylesheets
├── storage/              # Application storage
│   ├── logs/            # Log files
│   ├── cache/           # Cache files
│   └── uploads/         # User uploads
├── docker/              # Docker configuration
└── docs/                # Documentation
```

## Configuration

The application can be configured through:
- Environment variables (`.env`)
- PHP configuration files (`config/`)
- Docker configuration (`docker/`)

See [Configuration Guide](docs/configuration.md) for details.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and development process.

## Documentation

- [User Guide](docs/user-guide.md)
- [Developer Guide](docs/developer-guide.md)
- [API Documentation](docs/api.md)
- [Database Schema](docs/database.md)
- [Configuration Guide](docs/configuration.md)
- [Deployment Guide](docs/deployment.md)

## License

This project is licensed under the GPL-2.0-or-later License - see the [COPYING](COPYING) file for details.

## Acknowledgments

- Original HuMo-gen team
- All contributors
- Open source community

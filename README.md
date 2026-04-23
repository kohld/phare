# Phare

![Tests](https://img.shields.io/github/actions/workflow/status/kohld/phare/tests.yml?branch=main&style=flat-square&label=Tests)
![PHPStan](https://img.shields.io/github/actions/workflow/status/kohld/phare/phpstan.yml?branch=main&style=flat-square&label=PHPStan)
![PHP CS Fixer](https://img.shields.io/github/actions/workflow/status/kohld/phare/php-cs-fixer.yml?branch=main&style=flat-square&label=CS+Fixer)
![Security Audit](https://img.shields.io/github/actions/workflow/status/kohld/phare/security-audit.yml?branch=main&style=flat-square&label=Security+Audit)
![Latest Release](https://img.shields.io/github/v/release/kohld/phare?style=flat-square&label=Release)

A minimalist PHP static site generator for GitHub Pages.

**Phare** transforms your Markdown content and Twig templates into static HTML. Built for developers who want full control without bloat.

## Features

- **Lightweight** – No unnecessary dependencies
- **Twig Templates** – Familiar Symfony templating with override support
- **CommonMark** – Full Markdown support with tables, code blocks
- **Collections** – Automatic post organization by tags and categories
- **Docker** – Zero local PHP install required
- **Extensible** – Clean PHP 8.2+ classes, easy to customize

## Installation

```bash
composer require kohld/phare
```

## Quick Start (Docker)

```bash
git clone https://github.com/kohld/phare.git my-site
cd my-site

# Install dependencies
docker compose run --rm phare composer install

# Generate site
docker compose --profile generate up

# Preview at http://localhost:8080
docker compose --profile preview up
```

## Development

```bash
# Run tests
docker compose run --rm phare vendor/bin/phpunit

# Static analysis
docker compose run --rm phare vendor/bin/phpstan analyse

# Code style
docker compose run --rm phare vendor/bin/php-cs-fixer fix --dry-run --diff

# All CI checks
docker compose run --rm phare composer ci
```

## Documentation

- [Getting Started](./docs/getting-started.md)
- [GitHub Pages Deployment](./docs/github-pages.md)

## License

MIT

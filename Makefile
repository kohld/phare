.PHONY: help install update test test-coverage lint lint-fix analyze audit ci generate preview docker-build

DOCKER_RUN = docker compose run --rm phare

help:
	@echo "Phare Development Commands"
	@echo ""
	@echo "install       Install dependencies"
	@echo "update        Update dependencies"
	@echo "test          Run PHPUnit tests"
	@echo "test-coverage Run tests with HTML coverage report"
	@echo "lint          Check code style (PHP CS Fixer)"
	@echo "lint-fix      Auto-fix code style"
	@echo "analyze       Run PHPStan static analysis"
	@echo "audit         Run composer security audit"
	@echo "ci            Run all checks (lint, analyze, test)"
	@echo "generate      Generate static site (basic-blog example)"
	@echo "preview       Preview generated site at http://localhost:8080"
	@echo "docker-build  Build Docker image"

install:
	$(DOCKER_RUN) composer install

update:
	$(DOCKER_RUN) composer update

test:
	$(DOCKER_RUN) vendor/bin/phpunit

test-coverage:
	$(DOCKER_RUN) vendor/bin/phpunit --coverage-html coverage --coverage-text

lint:
	$(DOCKER_RUN) vendor/bin/php-cs-fixer fix --dry-run --diff

lint-fix:
	$(DOCKER_RUN) vendor/bin/php-cs-fixer fix

analyze:
	$(DOCKER_RUN) vendor/bin/phpstan analyse

audit:
	$(DOCKER_RUN) composer audit

ci: lint analyze test
	@echo "All checks passed."

generate:
	docker compose --profile generate up --abort-on-container-exit

preview:
	docker compose --profile preview up

docker-build:
	docker compose build

PORT ?= 8000
PHP_DIR := php-basics
PHP_FILES := $(PHP_DIR)/index.php $(PHP_DIR)/form.php $(PHP_DIR)/process.php $(PHP_DIR)/helpers.php $(PHP_DIR)/nav.php

.PHONY: help run lint docker-up docker-down docker-logs

help:
	@echo "Available targets:"
	@echo "  make run   - Start the PHP development server on http://localhost:$(PORT)"
	@echo "  make lint  - Check PHP files for syntax errors"
	@echo "  make docker-up    - Build and start the Docker app on http://localhost:8000"
	@echo "  make docker-down  - Stop the Docker app"
	@echo "  make docker-logs  - Show Docker app logs"

run:
	php -S localhost:$(PORT) -t $(PHP_DIR)

lint:
	php -l $(PHP_DIR)/index.php
	php -l $(PHP_DIR)/form.php
	php -l $(PHP_DIR)/process.php
	php -l $(PHP_DIR)/helpers.php
	php -l $(PHP_DIR)/nav.php

docker-up:
	docker compose up --build -d

docker-down:
	docker compose down

docker-logs:
	docker compose logs -f

PHP_HOST := 127.0.0.1
PHP_PORT := 8000
PHP_DOCROOT := backend/public
PHP_PID_FILE := .php-server.pid
PHP_LOG_FILE := .php-server.log

ifneq (,$(wildcard .env))
include .env
export
endif

.PHONY: php-start php-stop php-restart php-status php-logs

php-start:
	@if [ -f "$(PHP_PID_FILE)" ] && kill -0 $$(cat $(PHP_PID_FILE)) 2>/dev/null; then \
		echo "PHP server already running at http://$(PHP_HOST):$(PHP_PORT) (PID $$(cat $(PHP_PID_FILE)))"; \
	else \
		echo "Starting PHP server at http://$(PHP_HOST):$(PHP_PORT)"; \
		nohup php -S $(PHP_HOST):$(PHP_PORT) -t $(PHP_DOCROOT) > $(PHP_LOG_FILE) 2>&1 & echo $$! > $(PHP_PID_FILE); \
		echo "Started with PID $$(cat $(PHP_PID_FILE))"; \
	fi

php-stop:
	@if [ -f "$(PHP_PID_FILE)" ] && kill -0 $$(cat $(PHP_PID_FILE)) 2>/dev/null; then \
		kill $$(cat $(PHP_PID_FILE)); \
		rm -f $(PHP_PID_FILE); \
		echo "PHP server stopped."; \
	else \
		echo "PHP server is not running."; \
		rm -f $(PHP_PID_FILE); \
	fi

php-restart: php-stop php-start

php-status:
	@if [ -f "$(PHP_PID_FILE)" ] && kill -0 $$(cat $(PHP_PID_FILE)) 2>/dev/null; then \
		echo "PHP server is running at http://$(PHP_HOST):$(PHP_PORT) (PID $$(cat $(PHP_PID_FILE)))"; \
	else \
		echo "PHP server is not running."; \
	fi

php-logs:
	@if [ -f "$(PHP_LOG_FILE)" ]; then \
		tail -50 $(PHP_LOG_FILE); \
	else \
		echo "No log file found."; \
	fi
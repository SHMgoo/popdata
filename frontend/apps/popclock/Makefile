SHELL := /bin/bash

APP_NAME := popclock
PID_FILE := .vite.pid
LOG_FILE := .vite.log
PORT := 5173

.PHONY: help dev stop restart rebuild status logs

help:
	@echo "Available targets:"
	@echo "  make dev      - start Vite dev server in background"
	@echo "  make stop     - stop Vite dev server"
	@echo "  make restart  - stop then start Vite dev server"
	@echo "  make rebuild  - reinstall deps, then restart Vite dev server"
	@echo "  make status   - show whether Vite dev server is running"
	@echo "  make logs     - tail the Vite log"

dev:
	@if [ -f "$(PID_FILE)" ] && kill -0 "$$(cat $(PID_FILE))" 2>/dev/null; then \
		echo "$(APP_NAME) dev server is already running with PID $$(cat $(PID_FILE))"; \
	else \
		if lsof -ti tcp:$(PORT) >/dev/null 2>&1; then \
			echo "Port $(PORT) is already in use. Cleaning it up first..."; \
			lsof -ti tcp:$(PORT) | xargs -r kill -9; \
			sleep 1; \
		fi; \
		echo "Starting $(APP_NAME) dev server on port $(PORT)..."; \
		nohup npm run dev -- --host 127.0.0.1 --port $(PORT) > "$(LOG_FILE)" 2>&1 & echo $$! > "$(PID_FILE)"; \
		sleep 3; \
		if lsof -ti tcp:$(PORT) >/dev/null 2>&1; then \
			echo "$(APP_NAME) dev server started"; \
			echo "PID file: $$(cat $(PID_FILE))"; \
			echo "URL: http://127.0.0.1:$(PORT)"; \
			echo "Log: $(LOG_FILE)"; \
		else \
			echo "Failed to start $(APP_NAME) dev server"; \
			[ -f "$(LOG_FILE)" ] && tail -20 "$(LOG_FILE)"; \
			rm -f "$(PID_FILE)"; \
			exit 1; \
		fi; \
	fi

stop:
	@FOUND=0; \
	if [ -f "$(PID_FILE)" ]; then \
		PID=$$(cat "$(PID_FILE)"); \
		if kill -0 "$$PID" 2>/dev/null; then \
			echo "Stopping $(APP_NAME) dev server from PID file (PID $$PID)..."; \
			pkill -TERM -P "$$PID" 2>/dev/null || true; \
			kill "$$PID" 2>/dev/null || true; \
			sleep 1; \
			pkill -KILL -P "$$PID" 2>/dev/null || true; \
			kill -9 "$$PID" 2>/dev/null || true; \
			FOUND=1; \
		else \
			echo "PID file exists but process is not running"; \
		fi; \
		rm -f "$(PID_FILE)"; \
	fi; \
	if lsof -ti tcp:$(PORT) >/dev/null 2>&1; then \
		echo "Killing process(es) still using port $(PORT)..."; \
		lsof -ti tcp:$(PORT) | xargs -r kill -9; \
		FOUND=1; \
	fi; \
	if [ "$$FOUND" -eq 1 ]; then \
		echo "$(APP_NAME) dev server stopped"; \
	else \
		echo "$(APP_NAME) dev server is not running"; \
	fi

restart: stop dev

rebuild:
	@echo "Rebuilding $(APP_NAME)..."
	npm install
	$(MAKE) restart

status:
	@if lsof -ti tcp:$(PORT) >/dev/null 2>&1; then \
		echo "$(APP_NAME) dev server is running on port $(PORT)"; \
		echo "PID(s): $$(lsof -ti tcp:$(PORT) | tr '\n' ' ')"; \
	else \
		echo "$(APP_NAME) dev server is not running"; \
	fi

logs:
	@if [ -f "$(LOG_FILE)" ]; then \
		tail -f "$(LOG_FILE)"; \
	else \
		echo "No log file found"; \
	fi
# popdata

`popdata` is a backend data service (PHP) plus a frontend workspace (Vue clients + shared widgets) for population-based applications.

The goal is to centralize data gathering and transformation in one backend, and allow multiple frontend clients to consume the same standardized API responses.

## Project Structure

```text
popdata/
├── backend/                 # PHP backend API (data service)
│   ├── public/              # endpoints (thin controllers)
│   ├── src/                 # services/providers/helpers
│   ├── config/              # app + external service config
│   └── data/                # static + cached data
├── frontend/                # client apps + shared widgets (work in progress)
├── docs/                    # documentation (contracts, sources, setup)
├── Makefile
└── README.md
```

## What Works Right Now

The backend currently has these working endpoints:

- `GET /health.php`  
  Simple health check.

- `GET /manifest.php`  
  Lists available endpoints.

- `GET /us-config.php`  
  U.S. population clock config and components-of-change metrics.  
  This currently pulls live data from the Census daily PEP API.

- `GET /us-rankings.php`  
  Most populous states, counties, and cities.  
  This currently reads from `backend/data/static/us-rankings.json`.

You can also filter rankings by group:

- `GET /us-rankings.php?geo=state`
- `GET /us-rankings.php?geo=county`
- `GET /us-rankings.php?geo=city`

## Backend Requirements

- PHP 8+
- A Census API key for `us-config.php`

Helpful but optional:

- `curl` for testing
- `jq` for pretty-printing JSON in the terminal

## Running the Backend

From the project root, set your environment variables:

```bash
export APP_ENV=local
export CENSUS_API_KEY='your_real_census_key_here'
```

Then start the PHP development server:

```bash
php -S 127.0.0.1:8000 -t backend/public
```

## Quick Test Commands

```bash
curl http://127.0.0.1:8000/health.php
curl http://127.0.0.1:8000/manifest.php
curl http://127.0.0.1:8000/us-config.php
curl http://127.0.0.1:8000/us-rankings.php
curl "http://127.0.0.1:8000/us-rankings.php?geo=state"
```

## Documentation

See the `docs/` folder for more detail:

- `docs/setup.md` — how to run the backend
- `docs/api-contracts.md` — response shapes for each endpoint
- `docs/data-sources.md` — where each endpoint gets its data

## Design Notes

The backend follows this pattern:

- `backend/public/*.php` are thin endpoint files
- `backend/src/Services/*` builds final JSON payloads
- `backend/src/Providers/*` talks to external APIs (like Census)
- `backend/src/Http/*` handles outbound requests and JSON responses
- `backend/data/static` holds hardcoded or seed data
- `backend/data/cache` is reserved for generated cached payloads

This keeps the frontend stable even if backend data sources change later.

## Next Planned Work

- Add more endpoints (for example: `us-regions`, `world-current`, `world-rankings`)
- Begin wiring `frontend/clients/popclock` to the new backend endpoints
- Move widget UI into `frontend/widgets`

# popdata API Contracts

This document defines the current backend API contract for the `popdata` service.

The purpose of these contracts is to keep the frontend and backend aligned.  
As backend internals change (for example, switching from static JSON to live API calls), the response shape should remain stable unless intentionally versioned.

---

## Response Conventions

### Success Response
Successful responses return JSON with:

- `ok: true`

and then endpoint-specific fields.

### Error Response
Error responses return JSON with:

- `ok: false`
- `error` — short error message
- `detail` — optional technical detail

Example:

```json
{
  "ok": false,
  "error": "Unable to build us-config response.",
  "detail": "Census API key is not configured."
}

## GET /us-regions.php

Returns regional U.S. population data.

### Purpose
Provides data for region-based U.S. population widgets.

### Current Source
Currently backed by a static JSON file in:

- `backend/data/static/us-regions.json`

This may later be replaced with a live API or generated dataset without changing the response contract.

### Response

```json
{
  "ok": true,
  "regions": [
    {
      "id": "northeast",
      "name": "Northeast",
      "population": 57690830
    },
    {
      "id": "midwest",
      "name": "Midwest",
      "population": 68999537
    },
    {
      "id": "south",
      "name": "South",
      "population": 132665693
    },
    {
      "id": "west",
      "name": "West",
      "population": 83010175
    }
  ]
}

Fields

ok (boolean)
Indicates success.

regions (array<object>)
Array of region objects.

Region Item Shape

Each region item contains:

id (string)
Stable internal identifier.

name (string)
Display name.

population (integer)
Population value.


And in `docs/data-sources.md`, add:

```md
### GET /us-regions.php

**Source type:** static data  
**Current source:** local JSON file

#### Purpose
Provides data for region-based U.S. population widgets.

#### Current local source

- `backend/data/static/us-regions.json`

#### What the backend does with it

The backend reads the local JSON file and returns:

- `regions`

#### Related files

- `backend/public/us-regions.php`
- `backend/src/Services/UsRegionsService.php`
- `backend/data/static/us-regions.json`

#### Notes

- This is intentionally static for the first build.
- Later, this can be replaced by:
  - a live API
  - a generated data pipeline
  - a cached file
- The frontend should not need to change if the response contract stays the same.


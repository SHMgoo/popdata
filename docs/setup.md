# popdata Setup

This document explains how to run the `popdata` backend during development.

---

## Project Purpose

`popdata` is a backend data service for population-based applications.

It is designed to:

- gather data from external APIs
- transform that data into stable frontend-friendly JSON
- serve multiple frontend clients from a shared backend

Current backend endpoints include:

- `/health.php`
- `/manifest.php`
- `/us-config.php`
- `/us-rankings.php`

---

## Current Project Structure

```text id="gg4anl"
popdata/
├── backend/
│   ├── config/
│   ├── data/
│   ├── public/
│   ├── src/
│   └── tests/
├── frontend/
├── docs/
├── Makefile
└── README.md

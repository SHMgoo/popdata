 # popdata

`popdata` is a modular platform for building population-based applications.

It consists of:

- a **backend data service** (PHP)
- a **shared widget library** (Vue)
- multiple **frontend applications** built on top of those widgets

The goal is to centralize data logic in one place and enable multiple applications to consume consistent, standardized APIs.

---

## 🧠 Architecture Overview

PopData is designed as a **single platform (monorepo)** with three layers:

1. **Data Service (Backend)**
   - Handles all communication with external data sources (e.g., Census APIs)
   - Normalizes and prepares data for consumption

2. **Shared Widgets**
   - Reusable UI components that consume PopData endpoints
   - Designed to be portable across multiple applications

3. **Applications (`frontend/apps`)**
   - Independent apps built using shared widgets
   - Example: Popclock

---

## 📁 Project Structure

```text
popdata/
├── backend/
├── frontend/
│   ├── apps/
│   ├── widgets/
│   ├── shared/
│   └── clients/
├── docs/
├── scripts/
├── distribution/        # ignored
├── github-publish/      # ignored
└── README.md
```

---

## 🌿 Branch Strategy

- **development** → source of truth  
- **publish** → deployment artifacts (generated, not edited)

---

## 🚀 Quick Start

```bash
export APP_ENV=local
export CENSUS_API_KEY='your_key_here'

php -S 127.0.0.1:8000 -t backend/public
```

---

## 🔍 Example API Calls

```bash
curl http://127.0.0.1:8000/health.php
curl http://127.0.0.1:8000/us-population-summary.php
curl http://127.0.0.1:8000/us-populous.php
```

---

## 🧩 Backend Design

- public/ → endpoints  
- src/Services → business logic  
- src/Providers → external APIs  
- data/static → seed data  
- data/cache → runtime cache  

---

## 🎯 Vision

PopData is not a single app — it is a **platform**.

> Build once. Reuse everywhere.

---

## 🔮 Future Direction

- Containerization (Podman / AWS)
- CI/CD pipeline
- Expand widget library
- Multiple production apps

---

## ⚠️ Notes

- Do not edit `publish` branch manually  
- Generated folders are ignored  
- All development happens on `development`  


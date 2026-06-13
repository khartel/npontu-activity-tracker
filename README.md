# Npontu Technologies — Application Support Activity Tracker

> **Candidate Submission** | Application Support Team Interview Assignment  
> Framework: Laravel 10 | PHP 8.1+ | MySQL

---

## Overview

A full-featured web application for tracking the daily activities of an Application Support team. Personnel can log, update, and hand over activities in real time. Administrators manage the activity list and team members. Every status change is stored with a bio snapshot and timestamp for full audit traceability.

---

## Requirements Met

| # | Requirement | Implementation |
|---|-------------|----------------|
| 1 | Input activities (e.g. Daily SMS count vs log count) | Activity model with category `sms_monitoring`; SMS system/log count fields with live discrepancy calculation |
| 2 | Update status (done/pending) + remark | Modal on dashboard; AJAX quick-update endpoint; status choices: Pending, In Progress, Done, Skipped |
| 3 | Capture bio details + timestamp | `activity_log_history` table stores name, employee ID, email, department, and exact timestamp for every change |
| 4 | Daily view with all updates per activity | Dashboard shows all activities for a selected date; Handover Log panel shows chronological timeline of every update |
| 5 | Reporting with custom date ranges | Reports page: filter by date range, activity, status, personnel; CSV export; expandable inline history |
| 6 | User authentication | Laravel session auth; roles: `admin` and `personnel`; inactive account blocking |

---

## Architecture

```
app/
├── Http/
│   └── Controllers/
│       ├── Auth/AuthController.php          # Login / logout
│       ├── DashboardController.php          # Daily activity view
│       ├── ActivityController.php           # Admin CRUD for activities
│       ├── ActivityLogController.php        # Status updates + history recording
│       ├── ReportController.php             # Date-range queries + CSV export
│       └── UserController.php              # Admin user management
├── Models/
│   ├── User.php                             # Auth + role helpers
│   ├── Activity.php                         # Activity definitions
│   ├── ActivityLog.php                      # Daily status per activity (one per activity per day)
│   └── ActivityLogHistory.php              # Immutable audit trail of every change

database/migrations/
├── create_users_table.php
├── create_activities_table.php
├── create_activity_logs_table.php           # UNIQUE(activity_id, log_date)
└── create_activity_log_history_table.php    # Every update ever made

resources/views/
├── layouts/app.blade.php                    # Sidebar shell
├── auth/login.blade.php                     # Split-panel login
├── activities/
│   ├── dashboard.blade.php                  # Core daily tracker
│   ├── index.blade.php                      # Admin: manage activities
│   ├── create.blade.php
│   └── edit.blade.php
├── reports/index.blade.php                  # Date-range reporting
└── users/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

---

## Key Design Decisions

### 1. Dual-table Log Architecture
- `activity_logs` — one row per activity per day (unique constraint); the "current state" for that day
- `activity_log_history` — append-only; every change ever made, with full bio snapshot

This cleanly separates "what is the current status of X today?" from "what happened to X today and who did what?"

### 2. Bio Snapshot on Every Update
Rather than just linking to the user record (which can be edited), each history entry stores `personnel_name`, `personnel_employee_id`, `personnel_email`, and `personnel_department` at the exact moment of the update. This provides tamper-proof audit evidence.

### 3. AJAX Updates Without Page Reload
The dashboard uses AJAX (`/activities/{id}/quick-update`) so personnel can update multiple activities quickly without losing their scroll position. The page reloads only after a successful save to reflect the latest state.

### 4. SMS Discrepancy Flagging
SMS monitoring activities expose system count and log count fields. A discrepancy greater than ±5 is flagged visually (amber warning) both on the dashboard and in the reports table.

### 5. Role-Based Access Control
- `personnel` — can view dashboard, update activity statuses, view reports
- `admin` — all of the above + manage activities list, manage user accounts

A Laravel Gate (`admin`) is used for clean middleware-based protection.

---

## Setup Instructions

### Prerequisites
- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js 18+ (for asset compilation, optional — CSS is inline)

### 1. Clone & Install

```bash
git clone [<repo-url>](https://github.com/khartel/npontu-activity-tracker) npontu-tracker
cd npontu-tracker
composer install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` — set your database credentials:

```env
DB_DATABASE=npontu_tracker
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Database

```bash
# Create the database first
mysql -u root -p -e "CREATE DATABASE npontu_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations + seed with demo data
php artisan migrate --seed
```

### 4. Run

```bash
php artisan serve
```

Open: **http://localhost:8000**

---

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@npontu.com | password |
| Personnel | ama.asante@npontu.com | password |
| Personnel | kofi.boateng@npontu.com | password |

---

## Non-Functional Requirements

| Concern | Approach |
|---------|----------|
| **Security** | CSRF on all forms; session auth; bcrypt passwords; soft-deletes for data retention; inactive account check on login |
| **Performance** | Eager loading with `with()` to prevent N+1 queries; database indexes on `log_date`, `user_id`, `activity_id`; pagination on all list views |
| **Auditability** | Immutable `activity_log_history` records with bio snapshots; status before/after tracking |
| **Data Integrity** | `UNIQUE(activity_id, log_date)` prevents duplicate daily logs; DB transactions wrap all log + history writes |
| **Usability** | AJAX updates; live SMS discrepancy calculation; pending handover alert panel; date navigator on dashboard |
| **Maintainability** | Service-layer separation; model scopes (`forDate`, `byStatus`, etc.); constants in models (`$statuses`, `$categories`) |

---

## Grading Checklist

- ✅ **Logic** — Clean separation of concerns, transactions, proper relationships
- ✅ **Code Clarity** — Docblocks, named scopes, consistent naming, readable controllers
- ✅ **UI Innovation** — Branded design (Npontu green + gold), handover timeline panel, live discrepancy calculator, split login page
- ✅ **Requirement Interpretation** — All 6 requirements implemented with extras (audit trail, CSV export, SMS discrepancy, date navigator)
- ✅ **Non-Functional Requirements** — Security, performance indexes, auditability, data integrity, pagination

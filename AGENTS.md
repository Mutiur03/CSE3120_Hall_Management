# Hall Management System - AI Instructions

Read this file before planning, editing, generating, or running commands in this repository.

All teammates and AI agents must follow the same two-file instruction chain:

1. `AGENTS.md` defines mandatory engineering rules.
2. `docs/PROJECT_BLUEPRINT.md` defines diagrams, schema, workflows, and development order.

Do not implement from memory or from the shared ChatGPT conversation alone.

## Source Of Truth

- This repository is the implementation source of truth.
- Project requirements originated from the shared Hall Management System SRS/Jira planning document:
  `https://chatgpt.com/share/6a21e1bc-7e4c-83a8-9b8b-72a1b3dc94b9`
- Detailed requirements diagrams, target schema, and development path live in
  [`docs/PROJECT_BLUEPRINT.md`](docs/PROJECT_BLUEPRINT.md). Read it before implementing a feature.
- When old planning documents conflict with the repository, follow the repository unless the user explicitly requests a migration.
- In particular, ignore the old React/Node/Prisma/PostgreSQL recommendation. This project uses Laravel, Blade, Vite, Tailwind CSS, and SQLite.

## Project Goal

Build a web-based Hall Management System for one independent hall. There is no super-admin or multi-hall layer.

Primary roles:

- `admin`: manages students, rooms, seats, applications, room changes, dining, and reports.
- `student`: manages own profile and requests, views seat status, controls meal status, and views notices/reports allowed to students.

## Current Stack

- PHP `^8.2`
- Laravel `^12.0`
- Blade views
- SQLite for local development
- Vite `^7`
- Tailwind CSS `^4`
- PHPUnit `^11`

Do not introduce a SPA framework, separate API backend, PostgreSQL, or a new major dependency without explicit user approval.

## Functional Scope

Implement work in this dependency order unless the user or active Jira story says otherwise:

1. Authentication and user management
2. Student profile management
3. Room management
4. Seat availability and allocation
5. Online seat applications
6. Room change requests
7. Dining management
8. Dashboard and reports

Expected capabilities:

- Admin login/logout/change password
- Admin-created student accounts
- Student login/logout/change/forgot password
- Student list, profile, contact update, edit, and deactivation
- Room create/edit/delete/list/details/occupancy
- Seat allocate/transfer/vacate and availability views
- Seat application submit/status/approve/reject
- Room change request submit/status/approve/reject/reallocate
- Meal on/off/status, dining attendance, daily count, and monthly report
- Admin dashboard and exportable reports

## Critical Business Rules

- Only an admin can create student accounts.
- Student default password is the student's roll number.
- Passwords must always be hashed; never store or log plaintext passwords.
- A newly created student must change the default password on first login.
- One student can have at most one active seat allocation.
- One seat can belong to at most one active student.
- Room capacity must never be exceeded.
- Approval, rejection, transfer, allocation, and vacating operations must be authorized and atomic.
- Meal-off requests must respect a configurable cutoff time.
- Meal-status changes affect the next day's meal count.
- Students may only read or modify their own protected data.
- Deactivation must block login/operations without destroying historical records.

## Target Domain Model

The project is still near the Laravel starter state. Add domain tables through new migrations; do not rewrite old migrations after they may have been shared.

Core entities:

- `users`: role, email, password, first-login flag, active state
- `students`: user, roll, registration number, department, session, phone, status
- `rooms`: room number, floor, capacity, active state
- `seats`: room, seat label/number, active state
- `seat_allocations`: student, seat, allocation/vacated dates, active/vacated state
- `seat_applications`: student, reason/documents, status, admin comment
- `room_change_requests`: student, current/requested room, reason, status, admin comment
- `meal_statuses`: student, active state, effective date
- `dining_attendances`: student, date, meal type, attendance state

Use foreign keys, unique constraints, indexes, and transactions to enforce invariants. Prefer PHP backed enums or centralized constants for roles and statuses over repeated strings.

## Laravel Implementation Rules

- Follow existing Laravel 12 conventions and repository style.
- Keep controllers thin. Put reusable domain operations in focused action/service classes when complexity warrants it.
- Use Form Request classes for non-trivial validation.
- Use policies/gates or middleware for role and ownership authorization.
- Use Eloquent relationships and query scopes; avoid raw SQL unless justified.
- Prevent N+1 queries with eager loading.
- Use database transactions and locking where concurrent allocation/approval could violate capacity or uniqueness.
- Never trust client-provided role, ownership, occupancy, or approval state.
- Calculate or transactionally maintain derived counts; do not let forms directly set them.
- Return useful validation errors and preserve submitted input.
- Keep secrets and local settings in `.env`; never commit `.env`.

## UI Rules

- Use Blade, Vite, and Tailwind already present in the repository.
- Build accessible, responsive forms and tables.
- Show clear empty, loading, validation, success, and error states.
- Separate admin and student navigation/actions by authorization.
- Do not expose actions in UI that the current user cannot perform, while still enforcing authorization server-side.

## Testing And Definition Of Done

Every behavioral change must include focused tests. Prefer feature tests for workflows and unit tests for isolated domain logic.

Test at least:

- happy path
- validation failure
- unauthorized role or wrong owner
- important business-rule boundary
- database state after success/failure

Before declaring work complete, run the narrowest relevant checks, then broader checks when practical:

```powershell
php artisan test --filter=<RelevantTest>
composer run test
vendor\bin\pint --test
npm run build
```

A story is done only when acceptance criteria work, authorization and validation exist, tests pass, and related documentation is updated when behavior or setup changed.

## Working Agreement For AI Agents

1. Read `AGENTS.md`, `docs/PROJECT_BLUEPRINT.md` relevant routes, models, migrations, and tests before editing.
2. Inspect current git changes and preserve user work.
3. Confirm the requested story's acceptance criteria from local context; make conservative assumptions when details are missing.
4. Implement the smallest complete vertical slice: migration/model, authorization, validation, backend behavior, UI, and tests as applicable.
5. Never silently change stack or business rules.
6. Never run destructive commands such as `migrate:fresh`, database deletion, or broad file deletion without explicit user approval.
7. Do not edit `.env` or expose secrets unless explicitly requested.
8. Report changed files, checks run, and any remaining risk.
9. If implementation changes architecture, schema, workflow states, or module dependencies, update `docs/PROJECT_BLUEPRINT.md` in the same change.

## Useful Commands

```powershell
composer run setup
composer run dev
composer run test
php artisan migrate
php artisan db:seed
php artisan optimize:clear
npm run dev
npm run build
```

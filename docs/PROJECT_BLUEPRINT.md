# Hall Management System Project Blueprint

This document is the detailed system requirements, target schema, and development path for the Hall Management System. It supplements `AGENTS.md`.

## 1. System Boundary

The application manages one independent residential hall. It has two authenticated roles:

- Hall Admin: operates and supervises the hall.
- Student: uses self-service hall facilities.

There is no public registration, super-admin, multi-hall tenancy, payment module, or guardian role in current scope.

## 2. System Requirements Diagram

```mermaid
flowchart LR
    Admin[Hall Admin]
    Student[Student]

    subgraph HMS[Hall Management System]
        Auth[Authentication and Access Control]
        StudentMgmt[Student Management]
        RoomMgmt[Room and Seat Management]
        SeatApp[Seat Application Workflow]
        ChangeReq[Room Change Workflow]
        Dining[Dining Management]
        Reports[Dashboard and Reports]
    end

    Admin -->|login, logout, change password| Auth
    Student -->|login, logout, forgot/change password| Auth

    Admin -->|create, edit, deactivate, search| StudentMgmt
    Student -->|view profile, update contact data| StudentMgmt

    Admin -->|create rooms and seats, allocate, transfer, vacate| RoomMgmt
    Student -->|view own seat and availability| RoomMgmt

    Student -->|submit and track| SeatApp
    Admin -->|review, approve, reject| SeatApp
    SeatApp -->|approved application triggers allocation| RoomMgmt

    Student -->|submit and track| ChangeReq
    Admin -->|review, approve, reject| ChangeReq
    ChangeReq -->|approved request triggers transfer| RoomMgmt

    Student -->|meal on/off, view status| Dining
    Admin -->|attendance, daily count, monthly report| Dining

    StudentMgmt --> Reports
    RoomMgmt --> Reports
    SeatApp --> Reports
    ChangeReq --> Reports
    Dining --> Reports
    Admin -->|view and export| Reports
```

## 3. Core Workflow Diagram

```mermaid
flowchart TD
    Start([User opens application]) --> Login[Submit credentials]
    Login --> Valid{Credentials valid and user active?}
    Valid -->|No| LoginError[Show safe login error]
    LoginError --> Login
    Valid -->|Yes| FirstLogin{Student first login?}
    FirstLogin -->|Yes| ForcePassword[Require password change]
    ForcePassword --> Dashboard
    FirstLogin -->|No| Dashboard{Role dashboard}

    Dashboard -->|Admin| AdminActions[Manage students, rooms, seats, requests, dining, reports]
    Dashboard -->|Student| StudentActions[Profile, applications, room changes, meal status]

    StudentActions --> SubmitRequest[Submit application or room-change request]
    SubmitRequest --> Pending[Status: pending]
    Pending --> Review[Admin reviews request]
    Review --> Decision{Approve?}
    Decision -->|No| Rejected[Status: rejected with comment]
    Decision -->|Yes| Capacity{Active seat available and rules valid?}
    Capacity -->|No| PendingOrReject[Keep pending or reject with reason]
    Capacity -->|Yes| Transaction[Atomic allocate or transfer transaction]
    Transaction --> Approved[Status: approved]
```

## 4. Authorization Matrix

| Capability | Admin | Student |
|---|---:|---:|
| Create student account | Yes | No |
| View all students | Yes | No |
| View own profile | Yes | Yes |
| Update student administrative fields | Yes | No |
| Update student contact information | Yes | No |
| Create/edit/deactivate rooms and seats | Yes | No |
| Allocate/transfer/vacate seat | Yes | No |
| View available seats | Yes | Yes |
| Submit seat application | No | Yes |
| Approve/reject seat application | Yes | No |
| Submit room change request | No | Yes |
| Approve/reject room change request | Yes | No |
| Change own meal status | No | Yes |
| Record dining attendance | Yes | No |
| View operational reports | Yes | No |

All permissions must be enforced server-side with middleware, policies, or gates. UI visibility alone is not authorization.

## 5. Target Schema Diagram

SQLite is the local database. Use Laravel migrations, foreign keys, indexes, and application transactions. IDs may remain Laravel integer IDs unless the user explicitly requests UUID migration.

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role "admin|student"
        boolean is_first_login
        boolean is_active
        timestamp email_verified_at
        string remember_token
        timestamps timestamps
    }

    STUDENTS {
        bigint id PK
        bigint user_id FK,UK
        string roll UK
        string registration_no UK
        string department
        string academic_session
        string phone
        string status "active|inactive"
        timestamps timestamps
    }

    ROOMS {
        bigint id PK
        string room_no UK
        integer floor
        integer capacity
        string status "active|inactive"
        timestamps timestamps
    }

    SEATS {
        bigint id PK
        bigint room_id FK
        string seat_no
        string status "active|inactive"
        timestamps timestamps
    }

    SEAT_ALLOCATIONS {
        bigint id PK
        bigint student_id FK
        bigint seat_id FK
        bigint allocated_by FK
        date allocated_at
        date vacated_at
        string status "active|vacated"
        timestamps timestamps
    }

    SEAT_APPLICATIONS {
        bigint id PK
        bigint student_id FK
        text reason
        string document_path
        string status "pending|approved|rejected"
        text admin_comment
        bigint reviewed_by FK
        timestamp reviewed_at
        timestamps timestamps
    }

    ROOM_CHANGE_REQUESTS {
        bigint id PK
        bigint student_id FK
        bigint current_seat_id FK
        bigint requested_room_id FK
        text reason
        string status "pending|approved|rejected"
        text admin_comment
        bigint reviewed_by FK
        timestamp reviewed_at
        timestamps timestamps
    }

    MEAL_STATUSES {
        bigint id PK
        bigint student_id FK
        boolean is_active
        date effective_date
        timestamp requested_at
        timestamps timestamps
    }

    DINING_ATTENDANCES {
        bigint id PK
        bigint student_id FK
        date attendance_date
        string meal_type "breakfast|lunch|dinner"
        string status "present|absent"
        bigint recorded_by FK
        timestamps timestamps
    }

    USERS ||--o| STUDENTS : "student account"
    ROOMS ||--|{ SEATS : contains
    STUDENTS ||--o{ SEAT_ALLOCATIONS : receives
    SEATS ||--o{ SEAT_ALLOCATIONS : assigned_by
    USERS ||--o{ SEAT_ALLOCATIONS : allocates
    STUDENTS ||--o{ SEAT_APPLICATIONS : submits
    USERS ||--o{ SEAT_APPLICATIONS : reviews
    STUDENTS ||--o{ ROOM_CHANGE_REQUESTS : submits
    SEATS ||--o{ ROOM_CHANGE_REQUESTS : current_seat
    ROOMS ||--o{ ROOM_CHANGE_REQUESTS : requested_room
    USERS ||--o{ ROOM_CHANGE_REQUESTS : reviews
    STUDENTS ||--o{ MEAL_STATUSES : schedules
    STUDENTS ||--o{ DINING_ATTENDANCES : has
    USERS ||--o{ DINING_ATTENDANCES : records
```

## 6. Schema Constraints

Required database or transaction-level protections:

- `users.email`, `students.roll`, `students.registration_no`, and `rooms.room_no` are unique.
- `students.user_id` is unique, enforcing one student profile per student user.
- Composite unique index on `seats(room_id, seat_no)`.
- Composite unique index on `dining_attendances(student_id, attendance_date, meal_type)`.
- A room cannot have more active seats than its capacity.
- Only one active allocation may exist per student.
- Only one active allocation may exist per seat.
- Request approval and allocation/transfer happen in one database transaction.
- A room-change request stores the current seat at submission time for auditability.
- Historical allocations, requests, meal changes, and attendance records are retained.

SQLite cannot express every conditional uniqueness rule consistently across environments. Enforce active-allocation uniqueness with transactions, locked/rechecked records where supported, and focused tests.

## 7. Laravel Module Shape

Use this shape as modules become necessary; do not create empty files early.

```text
app/
  Actions/                 Complex transactional use cases
  Enums/                   Role and workflow statuses
  Http/Controllers/
    Admin/
    Student/
  Http/Requests/           Form Request validation
  Models/                  Eloquent models and relationships
  Policies/                Ownership and role authorization
resources/views/
  layouts/
  admin/
  student/
database/
  migrations/
  factories/
  seeders/
tests/
  Feature/
  Unit/
```

## 8. Development Dependency Diagram

```mermaid
flowchart LR
    P0[Phase 0: Foundation] --> P1[Phase 1: Authentication]
    P1 --> P2[Phase 2: Student Management]
    P2 --> P3[Phase 3: Rooms and Seats]
    P3 --> P4[Phase 4: Seat Allocation]
    P4 --> P5[Phase 5: Seat Applications]
    P4 --> P6[Phase 6: Room Changes]
    P2 --> P7[Phase 7: Dining]
    P5 --> P8[Phase 8: Dashboard and Reports]
    P6 --> P8
    P7 --> P8
    P8 --> P9[Phase 9: Hardening and Release]
```

## 9. Detailed Development Path

### Phase 0: Foundation

Deliver:

- Confirm environment and SQLite migrations run.
- Replace starter welcome page with shared authenticated layout skeleton.
- Add enums/constants strategy, test helpers, and admin development seeder.
- Define route groups and authorization approach.

Exit gate:

- `composer run test` and `npm run build` pass.
- Admin seed account can be created without committing credentials.

### Phase 1: Authentication And Access Control

Deliver:

- Admin and student login/logout.
- Role and active-state middleware.
- Forgot/change password.
- Forced first-login password change.
- Block public registration.

Exit gate:

- Inactive users cannot log in.
- Students cannot access admin routes.
- First-login students cannot bypass password change.

**Implemented (2026-07-05):** `users.is_first_login`, `users.is_active`, student change-password routes, `EnsureStudentPasswordChanged` and `EnsureUserIsActive` middleware, login redirect for first-login students, and inactive-account login blocking.

### Phase 2: Student Management

Deliver:

- Admin creates, lists, searches, edits, and deactivates students.
- Creation atomically creates `users` and `students`.
- Default password equals roll number and is hashed.
- Student views profile and updates allowed contact fields.

Exit gate:

- Duplicate roll/email/registration number rejected.
- Students cannot view or edit another student's profile.


### Phase 3: Room And Seat Management

Deliver:

- Admin room CRUD and activation state.
- Admin creates/manages seats within capacity.
- Room list/details show capacity, occupied, and available counts.
- Students can view available seats.

Exit gate:

- Room capacity cannot be less than existing active seats.
- Inactive rooms/seats cannot receive allocations.

### Phase 4: Seat Allocation

Deliver:

- Admin allocates, transfers, and vacates seats.
- Allocation history remains available.
- All allocation changes use transactions.

Exit gate:

- Concurrent or repeated actions cannot double-allocate student or seat.
- Occupancy and availability remain correct after allocate/transfer/vacate.

### Phase 5: Online Seat Applications

Deliver:

- Student submits one valid pending application and tracks status.
- Admin lists, reviews, comments, approves, or rejects.
- Approval optionally allocates a selected available seat atomically.

Exit gate:

- Student cannot submit when already allocated unless business rule permits it.
- Approval cannot overbook a room or seat.

### Phase 6: Room Change Requests

Deliver:

- Allocated student submits and tracks room-change request.
- Admin approves/rejects with comment.
- Approval transfers seat atomically.

Exit gate:

- Only allocated students can request a change.
- Failed transfer leaves original allocation unchanged.

### Phase 7: Dining Management

Deliver:

- Student schedules meal on/off changes.
- Enforce configurable cutoff and next-day effective date.
- Admin records attendance and views daily/monthly summaries.

Exit gate:

- Duplicate attendance records are prevented.
- Daily count uses effective meal status for selected date.

### Phase 8: Dashboard And Reports

Deliver:

- Admin dashboard metrics: students, rooms, occupied/available seats, pending requests, today's meals.
- Student, occupancy, application, room-change, and dining reports.
- Export only after on-screen report filters and authorization work.

Exit gate:

- Metrics match database state and avoid N+1 queries.
- Students cannot access operational reports.

### Phase 9: Hardening And Release

Deliver:

- Full authorization audit.
- Validation and error-state audit.
- Accessibility and responsive UI review.
- Seed/demo data, deployment notes, backups, and restore procedure.
- Full automated test and production build.

Exit gate:

- `composer run test`, `vendor\bin\pint --test`, and `npm run build` pass.
- No secrets, debug output, or default credentials are committed.

## 10. Per-Story Implementation Path

For every story, work in this order:

1. Write acceptance criteria and identify role/ownership rules.
2. Add or update migration constraints.
3. Add model relationships, casts, and enums.
4. Add policy/gate and Form Request validation.
5. Add transactional action/service when business state changes.
6. Add thin controller and routes.
7. Add Blade/Tailwind UI with success/error/empty states.
8. Add feature tests and focused unit tests.
9. Run relevant test, full tests when practical, Pint, and frontend build.
10. Update this blueprint when schema, rules, or development path changes.

## 11. Application Architecture Diagram

Every feature should follow this request path. Keep business decisions out of Blade views and keep controllers thin.

```mermaid
flowchart LR
    Browser[Browser]

    subgraph Frontend[Blade and Tailwind UI]
        View[Blade View]
        Form[HTML Form]
    end

    subgraph Laravel[Laravel Application]
        Route[Named Route]
        Middleware[Auth, Role, Active, First-Login Middleware]
        Request[Form Request Validation]
        Controller[Thin Controller]
        Policy[Policy or Gate]
        Action[Transactional Action or Service]
        Model[Eloquent Models and Scopes]
        Resource[Redirect, View, or Download Response]
    end

    DB[(SQLite Database)]
    Queue[Queue Job - only when needed]

    Browser --> View
    View --> Form
    Form --> Route
    Route --> Middleware
    Middleware --> Request
    Request --> Controller
    Controller --> Policy
    Policy --> Action
    Action --> Model
    Model --> DB
    Action -. optional async work .-> Queue
    Controller --> Resource
    Resource --> Browser
```

## 12. Module Dependency Diagram

Dependencies flow downward. Avoid circular dependencies between modules.

```mermaid
flowchart TD
    Auth[Authentication and Authorization]
    Students[Student Management]
    Rooms[Room and Seat Management]
    Allocation[Seat Allocation]
    Applications[Seat Applications]
    Changes[Room Change Requests]
    Dining[Dining Management]
    Reports[Dashboard and Reports]

    Auth --> Students
    Auth --> Rooms
    Students --> Allocation
    Rooms --> Allocation
    Students --> Applications
    Allocation --> Applications
    Students --> Changes
    Rooms --> Changes
    Allocation --> Changes
    Students --> Dining
    Students --> Reports
    Rooms --> Reports
    Allocation --> Reports
    Applications --> Reports
    Changes --> Reports
    Dining --> Reports
```

Rules:

- Reports read module data but do not own operational business rules.
- Seat applications and room changes must call the same allocation actions used by direct admin allocation.
- Authentication owns identity and access; student profile owns hall-specific student information.
- Dining must not depend on allocation unless a future explicit rule requires residents to have seats.

## 13. Route And Request Lifecycle Diagram

```mermaid
sequenceDiagram
    actor User
    participant Route
    participant Middleware
    participant FormRequest
    participant Controller
    participant Policy
    participant Action
    participant Database

    User->>Route: Submit named route request
    Route->>Middleware: Authenticate and check role/state
    Middleware-->>User: Redirect or 403 when blocked
    Middleware->>FormRequest: Validate normalized input
    FormRequest-->>User: Redirect with field errors when invalid
    FormRequest->>Controller: Validated request
    Controller->>Policy: Authorize resource/action
    Policy-->>User: 403 when unauthorized
    Policy->>Action: Execute approved use case
    Action->>Database: Begin transaction
    Action->>Database: Recheck invariants and write
    Database-->>Action: Commit or rollback
    Action-->>Controller: Result
    Controller-->>User: Redirect/view with safe feedback
```

## 14. Workflow State Diagrams

### User Account State

```mermaid
stateDiagram-v2
    [*] --> ActiveFirstLogin: Admin creates student
    ActiveFirstLogin --> Active: Student changes default password
    ActiveFirstLogin --> Inactive: Admin deactivates
    Active --> Inactive: Admin deactivates
    Inactive --> Active: Admin reactivates
```

### Seat Allocation State

```mermaid
stateDiagram-v2
    [*] --> Active: Admin allocates available seat
    Active --> Vacated: Admin vacates seat
    Active --> Vacated: Transfer closes old allocation
    Vacated --> [*]
    Active --> Active: Transfer creates new allocation in same transaction
```

### Application And Request State

```mermaid
stateDiagram-v2
    [*] --> Pending: Student submits
    Pending --> Approved: Admin approves and transaction succeeds
    Pending --> Rejected: Admin rejects with comment
    Pending --> Pending: Approval transaction fails safely
    Approved --> [*]
    Rejected --> [*]
```

### Meal Status State

```mermaid
stateDiagram-v2
    [*] --> MealOn: Initial active status
    MealOn --> MealOffScheduled: Valid off request before cutoff
    MealOffScheduled --> MealOff: Effective date reached
    MealOff --> MealOnScheduled: Valid on request
    MealOnScheduled --> MealOn: Effective date reached
```

## 15. Atomic Seat Operation Diagram

All direct allocations, approved applications, and approved room changes must use one shared transaction path.

```mermaid
flowchart TD
    Start[Start allocation or transfer] --> Begin[Begin database transaction]
    Begin --> StudentCheck{Student active and eligible?}
    StudentCheck -->|No| Rollback[Rollback and return validation error]
    StudentCheck -->|Yes| SeatCheck{Seat and room active and available?}
    SeatCheck -->|No| Rollback
    SeatCheck -->|Yes| Existing{Student has active allocation?}
    Existing -->|Yes, transfer| Vacate[Vacate old allocation]
    Existing -->|Yes, new allocation| Rollback
    Existing -->|No| Create[Create active allocation]
    Vacate --> Create
    Create --> Recheck{All uniqueness and capacity rules valid?}
    Recheck -->|No| Rollback
    Recheck -->|Yes| UpdateRequest[Update related request status if present]
    UpdateRequest --> Commit[Commit transaction]
    Commit --> Success[Return success]
```

## 16. Security Boundary Diagram

```mermaid
flowchart LR
    Input[Untrusted Browser Input]
    Validation[Form Request Validation]
    AuthN[Authentication]
    AuthZ[Role and Ownership Authorization]
    Domain[Domain Rules and Transactions]
    Output[Escaped Blade Output and Safe Errors]
    Storage[(Database and Private Files)]

    Input --> AuthN
    AuthN --> AuthZ
    AuthZ --> Validation
    Validation --> Domain
    Domain --> Storage
    Storage --> Domain
    Domain --> Output

    Secrets[Environment Secrets] -. never expose .-> Storage
    Uploads[Uploaded Documents] --> ValidateFile[Type, size, ownership validation]
    ValidateFile --> PrivateStorage[Private storage]
```

Mandatory security rules:

- Never trust IDs, roles, statuses, occupancy, reviewer identity, or ownership from forms.
- Use CSRF protection on state-changing web requests.
- Escape output by default; do not use raw Blade output for user content.
- Store uploaded documents privately and authorize every download.
- Show generic login/reset errors that do not reveal account existence.
- Log important admin actions without logging passwords or sensitive document contents.

## 17. Test Coverage Diagram

```mermaid
flowchart TD
    Story[Story Acceptance Criteria]
    Feature[Feature Tests]
    Unit[Unit Tests]
    Security[Authorization and Ownership Tests]
    Boundary[Validation and Business Boundary Tests]
    DBState[Database State Assertions]
    Build[Frontend Production Build]
    Quality[Pint and Full Test Suite]
    Done[Definition of Done]

    Story --> Feature
    Story --> Unit
    Story --> Security
    Story --> Boundary
    Feature --> DBState
    Unit --> Quality
    Security --> Quality
    Boundary --> Quality
    DBState --> Quality
    Build --> Done
    Quality --> Done
```

Each workflow-changing story requires at least:

- happy-path feature test
- unauthorized-role or wrong-owner test
- invalid-input test
- critical business-rule boundary test
- database state assertion after success and failure

## 18. Team Git And Jira Development Flow

All teammates should follow this same flow. A story has one primary assignee; teammates may own separate subtasks.

```mermaid
flowchart LR
    Backlog[Prioritized Jira Story]
    Criteria[Confirm Acceptance Criteria]
    Branch[Create Focused Git Branch]
    Implement[Implement Vertical Slice]
    LocalTest[Run Focused Tests and Build]
    Review[Open Review and Move to Code Review]
    Feedback{Review Passed?}
    Test[Move to Testing]
    Acceptance{Acceptance Criteria Passed?}
    Merge[Merge and Move to Done]
    Fix[Fix on Same Branch]

    Backlog --> Criteria
    Criteria --> Branch
    Branch --> Implement
    Implement --> LocalTest
    LocalTest --> Review
    Review --> Feedback
    Feedback -->|No| Fix
    Fix --> LocalTest
    Feedback -->|Yes| Test
    Test --> Acceptance
    Acceptance -->|No| Fix
    Acceptance -->|Yes| Merge
```

Team rules:

- One branch and pull request per focused story or bug.
- Never mix unrelated refactors into feature work.
- Never rewrite or remove another teammate's uncommitted work.
- Database changes use new migrations; do not edit already-shared migrations.
- Pull requests state acceptance criteria, schema impact, screenshots for UI work, and checks run.
- Merge only after review, tests, and acceptance criteria pass.

## 19. Blueprint Change Control

```mermaid
flowchart TD
    Change[Proposed implementation change]
    Affects{Changes schema, state, architecture, dependency, or business rule?}
    CodeOnly[Implement focused code and tests]
    UpdateDocs[Update AGENTS.md or PROJECT_BLUEPRINT.md]
    Review[Review code and documentation together]
    Done[Complete]

    Change --> Affects
    Affects -->|No| CodeOnly
    Affects -->|Yes| UpdateDocs
    CodeOnly --> Review
    UpdateDocs --> Review
    Review --> Done
```

`AGENTS.md` and this blueprint are version-controlled project artifacts. Every teammate and AI agent must keep them synchronized with implementation.

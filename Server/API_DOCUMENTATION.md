# Task Management API Documentation

## Overview

A RESTful API for managing tasks with priority-based sorting, status tracking, and daily reporting capabilities. Built with Laravel using optimized database queries and comprehensive validation.

**Base URL:** `/api/v1`

**Content-Type:** `application/json`

---

## Endpoints

### 1. Create Task

**POST** `/tasks`

Creates a new task with validation for uniqueness and future due dates.

#### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `title` | string | Yes | Task title (max 255 characters) |
| `due_date` | string | Yes | Due date in `YYYY-MM-DD` format (today or future) |
| `priority` | string | Yes | Priority level: `low`, `medium`, `high` |

#### Example Request

```json
{
    "title": "Complete API documentation",
    "due_date": "2026-04-01",
    "priority": "high"
}
```

#### Success Response (201 Created)

```json
{
    "id": 1,
    "title": "Complete API documentation",
    "due_date": "2026-04-01",
    "priority": "high",
    "status": "pending",
    "created_at": "2026-03-30T10:00:00Z",
    "updated_at": "2026-03-30T10:00:00Z"
}
```

#### Error Responses

**422 Validation Error** - Duplicate task
```json
{
    "message": "A task with this title and due date already exists.",
    "errors": {
        "title": ["A task with this title and due date already exists."]
    }
}
```

**422 Validation Error** - Past due date
```json
{
    "message": "The due date must be today or a future date.",
    "errors": {
        "due_date": ["The due date must be today or a future date."]
    }
}
```

---

### 2. List Tasks

**GET** `/tasks`

Retrieves all tasks sorted by priority (high → medium → low) then by due date ascending. Optional status filter available.

#### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `status` | string | No | Filter by status: `pending`, `in_progress`, `done` |

#### Example Requests

```
GET /api/v1/tasks
GET /api/v1/tasks?status=pending
```

#### Success Response (200 OK)

```json
{
    "data": [
        {
            "id": 1,
            "title": "Urgent task",
            "due_date": "2026-03-30",
            "priority": "high",
            "status": "pending",
            "created_at": "2026-03-28T08:00:00Z",
            "updated_at": "2026-03-28T08:00:00Z"
        },
        {
            "id": 2,
            "title": "Normal task",
            "due_date": "2026-03-29",
            "priority": "medium",
            "status": "in_progress",
            "created_at": "2026-03-27T10:00:00Z",
            "updated_at": "2026-03-28T09:00:00Z"
        }
    ]
}
```

**Note:** Returns empty array if no tasks exist: `{"data": []}`

#### Error Response

**422 Validation Error** - Invalid status
```json
{
    "message": "The selected status is invalid.",
    "errors": {
        "status": ["The selected status is invalid."]
    }
}
```

---

### 3. Update Task Status

**PATCH** `/tasks/{id}/status`

Updates task status with strict transition rules. Only forward progression is allowed.

#### URL Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | integer | Task ID |

#### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `status` | string | Yes | New status: `pending`, `in_progress`, `done` |

#### Example Request

```json
{
    "status": "in_progress"
}
```

#### Success Response (200 OK)

```json
{
    "id": 1,
    "title": "Complete API documentation",
    "due_date": "2026-04-01",
    "priority": "high",
    "status": "in_progress",
    "created_at": "2026-03-30T10:00:00Z",
    "updated_at": "2026-03-30T14:30:00Z"
}
```

#### Error Responses

**404 Not Found** - Task doesn't exist
```json
{
    "message": "No query results for model [App\\Models\\Task] 99"
}
```

**422 Validation Error** - Invalid status value
```json
{
    "message": "The selected status is invalid.",
    "errors": {
        "status": ["The selected status must be pending, in_progress, or done."]
    }
}
```

**422 Unprocessable Entity** - Invalid transition
```json
{
    "message": "Invalid status transition",
    "current_status": "pending",
    "requested_status": "done"
}
```

#### Status Transition Rules

| Current Status | Allowed Transitions | Invalid Transitions |
|----------------|---------------------|---------------------|
| `pending` | `in_progress` | `done`, `pending` |
| `in_progress` | `done` | `pending`, `in_progress` |
| `done` | none | `pending`, `in_progress`, `done` |

---

### 4. Delete Task

**DELETE** `/tasks/{id}`

Deletes a task only if its status is `done`. Returns 403 Forbidden for incomplete tasks.

#### URL Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | integer | Task ID |

#### Example Request

```
DELETE /api/v1/tasks/1
```

#### Success Response (204 No Content)

Empty response body.

#### Error Responses

**404 Not Found** - Task doesn't exist
```json
{
    "message": "No query results for model [App\\Models\\Task] 99"
}
```

**403 Forbidden** - Task not completed
```json
{
    "message": "Only completed tasks can be deleted"
}
```

---

### 5. Daily Report

**GET** `/tasks/report`

Generates an aggregated report of tasks for a specific date, grouped by priority and status.

#### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `date` | string | Yes | Report date in `YYYY-MM-DD` format |

#### Example Request

```
GET /api/v1/tasks/report?date=2026-03-28
```

#### Success Response (200 OK)

```json
{
    "date": "2026-03-28",
    "summary": {
        "high": {
            "pending": 2,
            "in_progress": 1,
            "done": 0
        },
        "medium": {
            "pending": 1,
            "in_progress": 0,
            "done": 3
        },
        "low": {
            "pending": 0,
            "in_progress": 0,
            "done": 1
        }
    }
}
```

#### Error Responses

**422 Validation Error** - Missing date
```json
{
    "message": "The date field is required.",
    "errors": {
        "date": ["The date field is required."]
    }
}
```

**422 Validation Error** - Invalid format
```json
{
    "message": "The date does not match the format YYYY-MM-DD.",
    "errors": {
        "date": ["The date does not match the format YYYY-MM-DD."]
    }
}
```

**422 Validation Error** - Invalid calendar date
```json
{
    "message": "The date is not a valid date.",
    "errors": {
        "date": ["The date is not a valid date."]
    }
}
```

---

## Data Models

### Task Object

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique identifier |
| `title` | string | Task title |
| `due_date` | string (YYYY-MM-DD) | Due date |
| `priority` | string | `low`, `medium`, `high` |
| `status` | string | `pending`, `in_progress`, `done` |
| `created_at` | string (ISO 8601) | Creation timestamp |
| `updated_at` | string (ISO 8601) | Last update timestamp |

### Priority Enum

- `high` - Highest priority, appears first in lists
- `medium` - Normal priority
- `low` - Lowest priority, appears last in lists

### Status Enum

- `pending` - Initial status, waiting to be started
- `in_progress` - Currently being worked on
- `done` - Completed, eligible for deletion

---

## HTTP Status Codes

| Code | Meaning | Usage |
|------|---------|-------|
| 200 | OK | Successful GET, PATCH requests |
| 201 | Created | Successful POST requests |
| 204 | No Content | Successful DELETE requests |
| 403 | Forbidden | Attempting to delete incomplete task |
| 404 | Not Found | Task ID does not exist |
| 422 | Unprocessable Entity | Validation errors, invalid status transitions |
| 500 | Internal Server Error | Unexpected server errors |

---

## Validation Rules

### Create Task

| Field | Rules |
|-------|-------|
| `title` | Required, string, max 255 characters, unique with `due_date` |
| `due_date` | Required, valid date, format `YYYY-MM-DD`, today or future |
| `priority` | Required, must be `low`, `medium`, or `high` |

### Update Status

| Field | Rules |
|-------|-------|
| `status` | Required, must be `pending`, `in_progress`, or `done` |
| Transition | Must follow: `pending` → `in_progress` → `done` |

### Daily Report

| Field | Rules |
|-------|-------|
| `date` | Required, valid date, format `YYYY-MM-DD` |

---

## Database Optimizations

### Indexes

| Index Name | Columns | Purpose |
|------------|---------|---------|
| `PRIMARY` | `id` | Task lookup |
| `unique_title_due_date` | `title`, `due_date` | Uniqueness constraint |
| `idx_tasks_due_date` | `due_date` | Date filtering |
| `idx_tasks_status` | `status` | Status filtering |
| `idx_tasks_priority` | `priority` | Priority sorting |
| `idx_tasks_status_priority_due_date` | `status`, `priority`, `due_date` | Filtered list queries |
| `idx_tasks_priority_due_date` | `priority`, `due_date` | Unfiltered list queries |
| `idx_tasks_due_date_priority_status` | `due_date`, `priority`, `status` | Daily report aggregation |

### Query Efficiency

| Endpoint | Query Count | Optimization |
|----------|-------------|--------------|
| `POST /tasks` | 2 | Uniqueness check + insert |
| `GET /tasks` | 1 | Single query with conditional filtering |
| `PATCH /tasks/{id}/status` | 2 | Find + update |
| `DELETE /tasks/{id}` | 2 | Find + delete (if done) |
| `GET /tasks/report` | 1 | Group by aggregation |

---

## Error Handling

All errors follow a consistent JSON structure:

```json
{
    "message": "Human-readable error description",
    "errors": {
        "field_name": ["Field-specific error messages"]
    }
}
```

Validation errors include the specific field and rule that failed. Business logic errors (like invalid status transitions) provide context about the current and requested states.

---

## Testing

The API includes comprehensive feature tests covering:

- Task creation with validation
- Duplicate task prevention
- Task listing and filtering
- Status transitions and edge cases
- Task deletion rules
- Daily report generation

Run tests with: `php artisan test`

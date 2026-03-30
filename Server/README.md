# Task Management API

A RESTful API for managing tasks with priority-based sorting, status tracking, and daily reporting capabilities. Built with Laravel 13 and optimized database queries.

## Features

- **Task Management** - Create, list, update status, and delete tasks
- **Priority System** - Tasks sorted by priority (high → medium → low)
- **Status Workflow** - Enforced progression: pending → in_progress → done
- **Daily Reports** - Aggregated task counts by priority and status
- **Database Optimization** - 8 indexes for optimal query performance
- **Comprehensive Validation** - Form requests with strict business rules
- **Full Test Coverage** - Feature tests for all endpoints

## Prerequisites

| Requirement | Version | Description |
|-------------|---------|-------------|
| PHP | ^8.3 | PHP runtime (CLI and FPM) |
| Composer | 2.x | PHP dependency manager |
| MySQL | 8.0+ or 5.7+ | Database server (or SQLite for local dev) |

### Check Prerequisites

```bash
php -v          # Should show PHP 8.3.x
composer -v     # Should show Composer 2.x
mysql -V        # Should show MySQL 8.0+
```

## Installation

```bash
# Navigate to Server directory
cd Server

# Install PHP dependencies
composer install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

## Environment Configuration

Edit `.env` file with your database credentials:

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

**For SQLite (easier for local development):**
```env
DB_CONNECTION=sqlite
# Remove or comment out other DB_* settings
```

Then create the SQLite database file:
```bash
touch database/database.sqlite
```

## Database Setup

### Run Migrations

```bash
php artisan migrate
```

**Tables Created:**
- `tasks` - Main tasks table with all indexes
- `users`, `cache`, `jobs` - Laravel system tables

### (Optional) Seed Sample Data

```bash
php artisan db:seed --class=TaskSeeder
```

### Verify Setup

```bash
php artisan migrate:status
```

## Running the Application

```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

Base API endpoint: `http://localhost:8000/api/v1`

## Deployment (Render + Docker)

Deploy the API to production using Render's free tier with Docker.

### Prerequisites

- [Aiven](https://aiven.io) account (free MySQL)
- [Render](https://render.com) account (free tier)
- GitHub repository with your code

### Step 1: Aiven MySQL Setup

1. Go to [aiven.io](https://aiven.io) → Create service → **MySQL**
2. Plan: **Startup-1 (Free)**
3. Copy credentials: Host, Port, Database, Username, Password

### Step 2: Prepare Environment

Create `.env.production`:

```env
APP_NAME="Task Management API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.onrender.com
APP_KEY=base64:your_generated_key

LOG_CHANNEL=stderr
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=your-aiven-host.aivencloud.com
DB_PORT=your-port
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=your-password
DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

FRONTEND_URL=https://your-frontend-url.com
```

Generate APP_KEY:
```bash
php artisan key:generate --show
```

### Step 3: Deploy to Render

1. Go to [render.com](https://render.com) → **New +** → **Web Service**
2. Connect your GitHub repository
3. Configure:

| Setting | Value |
|---------|-------|
| Name | `task-management-api` |
| Environment | **Docker** |
| Root Directory | `Server` (if in subfolder) |
| Dockerfile Path | `./Dockerfile` |

4. Add all environment variables from `.env.production`
5. Click **Create Web Service**

Render will:
- Build the Docker image
- Run `php artisan migrate --force`
- Start the container

### Step 4: Verify Deployment

**Health Check:**
```
https://your-app.onrender.com/health
```
Response: `{"status":"ok"}`

**API Test:**
```
https://your-app.onrender.com/api/v1/tasks
```
Response: `{"data":[]}`

### Docker Features

- **Multi-stage build** (~80MB image)
- **PHP 8.3 + OPcache** for performance
- **Nginx + PHP-FPM** with supervisor
- **Pre-optimized Laravel** (cached config/routes/views)
- **Health checks** for monitoring
- **SSL-ready** for Aiven MySQL

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed troubleshooting.

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Task creation tests
php artisan test tests/Feature/Api/V1/CreateTaskTest.php

# Task listing tests
php artisan test tests/Feature/Api/V1/ListTasksTest.php

# Status update tests
php artisan test tests/Feature/Api/V1/UpdateTaskStatusTest.php

# Delete task tests
php artisan test tests/Feature/Api/V1/DeleteTaskTest.php

# Daily report tests
php artisan test tests/Feature/Api/V1/DailyReportTest.php
```

### Run Tests with Coverage

```bash
php artisan test --coverage
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/v1/tasks` | Create a new task |
| `GET` | `/api/v1/tasks` | List all tasks (optional `?status` filter) |
| `PATCH` | `/api/v1/tasks/{id}/status` | Update task status |
| `DELETE` | `/api/v1/tasks/{id}` | Delete completed task |
| `GET` | `/api/v1/tasks/report?date=YYYY-MM-DD` | Daily report |

### Quick Test with cURL

```bash
# Create a task
curl -X POST http://localhost:8000/api/v1/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"Test Task","due_date":"2026-04-01","priority":"high"}'

# List tasks
curl http://localhost:8000/api/v1/tasks

# Update status
curl -X PATCH http://localhost:8000/api/v1/tasks/1/status \
  -H "Content-Type: application/json" \
  -d '{"status":"in_progress"}'

# Get daily report
curl "http://localhost:8000/api/v1/tasks/report?date=2026-04-01"
```

## Full API Documentation

See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for detailed endpoint documentation including:
- Request/response schemas
- Validation rules
- Error responses
- Database optimization details
- Business rules

## Project Structure

```
app/
├── DTOs/                    # Data Transfer Objects
├── Http/
│   ├── Controllers/Api/V1/  # API Controllers
│   ├── Requests/Api/V1/    # Form Request Validations
│   └── Resources/Api/V1/   # API Response Resources
├── Models/                  # Eloquent Models
├── Services/                # Business Logic Layer
│   └── Interfaces/         # Service Contracts
├── Enums/                   # Enumerations
database/
├── factories/              # Model Factories
├── migrations/             # Database Migrations
└── seeders/                # Database Seeders
tests/
└── Feature/Api/V1/         # API Feature Tests
routes/
└── api.php                # API Routes
```

## Database Schema

### Tasks Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `title` | string(255) | Task title |
| `due_date` | date | Due date |
| `priority` | enum | low, medium, high |
| `status` | enum | pending, in_progress, done |
| `created_at` | timestamp | Creation time |
| `updated_at` | timestamp | Last update |

### Indexes

- `unique_title_due_date` - Unique constraint on title + due_date
- `idx_tasks_due_date` - For date filtering
- `idx_tasks_status` - For status filtering
- `idx_tasks_priority` - For priority sorting
- `idx_tasks_status_priority_due_date` - For filtered list queries
- `idx_tasks_priority_due_date` - For unfiltered list queries
- `idx_tasks_due_date_priority_status` - For daily report aggregation

## Troubleshooting

### Common Issues

**1. "Could not find driver" error**
- Install PDO MySQL extension: `sudo apt-get install php8.3-mysql`
- Or use SQLite: change `DB_CONNECTION` to `sqlite` in `.env`

**2. "Permission denied" on storage/logs**
```bash
chmod -R 775 storage bootstrap/cache
chmod -R 775 storage/logs
```

**3. "Base table or view not found"**
- Run migrations: `php artisan migrate`

**4. "Unique constraint violation" on migration**
- If tasks already exist, either:
  - Use `php artisan migrate:fresh` (⚠️ deletes all data)
  - Or manually check for duplicates in database

**5. "No application encryption key"**
```bash
php artisan key:generate
```

**6. Port 8000 already in use**
```bash
php artisan serve --port=8080
```

## Development Commands

| Command | Description |
|---------|-------------|
| `php artisan serve` | Start dev server |
| `php artisan migrate` | Run migrations |
| `php artisan migrate:fresh` | Reset database with migrations |
| `php artisan migrate:fresh --seed` | Reset, migrate, and seed |
| `php artisan db:seed --class=TaskSeeder` | Seed tasks only |
| `php artisan test` | Run PHPUnit tests |
| `php artisan route:list` | List all routes |
| `composer install` | Install PHP dependencies |

## License

This project is open-sourced software.

## Support

For issues or questions:
1. Check [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for endpoint details
2. Review [Tests.md](Tests.md) for test scenarios
3. Run `php artisan route:list` to verify routes

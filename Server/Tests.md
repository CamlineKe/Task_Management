# Backend Testing Documentation

## Prerequisites

Before running tests, ensure the following are installed:

- **PHP 8.3+** with extensions: `sqlite3`, `pdo_mysql`, `pdo_sqlite`
- **Composer** (dependency manager)
- **SQLite3** (for in-memory testing)
- **MySQL** (for development database)

### Install SQLite for Testing
```bash
sudo apt-get install php8.3-sqlite3 sqlite3
```

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database
Update `.env` with your MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Seed Database (Optional)
```bash
php artisan db:seed
```

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites

| Test File | Command |
|-----------|---------|
| Status Transition Test | `php artisan test --filter StatusTransitionTest` |
| Task Model Test | `php artisan test --filter TaskTest` |
| Task Service Test | `php artisan test --filter TaskServiceTest` |
| Create Task API Test | `php artisan test --filter CreateTaskTest` |
| Daily Report Test | `php artisan test --filter DailyReportTest` |
| Delete Task Test | `php artisan test --filter DeleteTaskTest` |
| List Tasks Test | `php artisan test --filter ListTasksTest` |
| Update Task Status Test | `php artisan test --filter UpdateTaskStatusTest` |

### Run Unit Tests Only
```bash
php artisan test --testsuite=Unit
```

### Run Feature Tests Only
```bash
php artisan test --testsuite=Feature
```

## Test Database

Tests automatically use SQLite in-memory database by default. No additional configuration needed.
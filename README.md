# Task Management Application

A full-stack task management application with a Vue 3 frontend and Laravel API backend. Features task prioritization, status tracking, daily reports, and dark mode support.

**Repository:** https://github.com/CamlineKe/Task_Management

## Overview

This project consists of two main components:

- **Frontend** (`frontend/`) - Vue 3 SPA with Pinia state management
- **Backend** (`Server/`) - Laravel 13 RESTful API

## Getting Started

### Prerequisites

- **Node.js**: ^20.19.0 || >=22.12.0
- **PHP**: ^8.3
- **Composer**: 2.x
- **MySQL**: 8.0+ (or SQLite for local dev)

### Clone the Repository

```bash
git clone https://github.com/CamlineKe/Task_Management.git
cd Task_Management
```

### 1. Setup Backend

```bash
cd Server

# Install PHP dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database (choose MySQL or SQLite)
# Edit .env file with your database credentials

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed --class=TaskSeeder

# Start API server
php artisan serve
```

API will be available at `http://localhost:8000`

### 2. Setup Frontend

In a new terminal:

```bash
cd Task_Management/frontend

# Install dependencies
npm install

# Start dev server
npm run dev
```

Frontend will be available at `http://localhost:5173`

### For Forkers

If you forked this repository:

1. Fork on GitHub
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/Task_Management.git`
3. Follow the setup steps above
4. The project is ready to run locally - no additional configuration needed

### From ZIP File

If you have the project as a ZIP file:

#### Prerequisites Check

Before starting, verify you have the required software:

```bash
# Check PHP version (must be 8.3 or higher)
php -v

# Check Composer is installed
composer -v

# Check Node.js version (must be 20.19.0 or higher, or 22.12.0+)
node -v

# Check npm is installed
npm -v

# Check MySQL (optional, can use SQLite instead)
mysql -V
```

**Install any missing prerequisites:**
- **PHP 8.3**: https://www.php.net/downloads.php
- **Composer**: https://getcomposer.org/download/
- **Node.js**: https://nodejs.org/ (Download LTS version)
- **MySQL** (optional): https://dev.mysql.com/downloads/

#### Setup Steps

1. **Extract the ZIP file:**
   ```bash
   unzip Task_Management.zip -d Task_Management
   cd Task_Management
   ```

2. **Setup Backend:**
   ```bash
   cd Server
   
   # Install PHP dependencies
   composer install
   
   # Create environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   
   # Configure database (see Database Options below)
   
   # Run migrations
   php artisan migrate
   
   # Optional: Seed with sample data
   php artisan db:seed --class=TaskSeeder
   
   # Start the API server
   php artisan serve
   ```

3. **Setup Frontend (in a new terminal):**
   ```bash
   cd Task_Management/frontend
   
   # Install Node.js dependencies
   npm install
   
   # Start the development server
   npm run dev
   ```

4. **Access the application:**
   - Frontend: http://localhost:5173
   - Backend API: http://localhost:8000

#### Database Setup (MySQL)

1. Create a MySQL database:
   ```sql
   CREATE DATABASE task_management;
   ```
2. Edit `Server/.env` file with your MySQL credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

#### Troubleshooting Common Issues

**"Could not find driver" error:**
- Install PHP MySQL PDO extension:
  - Linux: `sudo apt-get install php8.3-mysql`
  - Windows/Mac: Enable `extension=pdo_mysql` in `php.ini`

**"Permission denied" on storage/logs:**
```bash
cd Server
chmod -R 775 storage bootstrap/cache
```

**"No application encryption key":**
```bash
cd Server
php artisan key:generate
```

**Port already in use:**
- Backend: `php artisan serve --port=8080`
- Frontend: Edit `vite.config.js` to change port

**"composer install" fails with memory error:**
```bash
php -d memory_limit=-1 /usr/local/bin/composer install
```

## Project Structure

```
Task_Management/
├── frontend/               # Vue 3 Frontend
│   ├── src/
│   │   ├── components/     # Vue components
│   │   ├── stores/         # Pinia stores
│   │   ├── views/          # Page components
│   │   └── composables/    # Vue composables
│   └── README.md           # Frontend documentation
│
└── Server/                 # Laravel Backend
    ├── app/
    │   ├── Http/           # Controllers & Requests
    │   ├── Services/       # Business logic
    │   └── Models/         # Eloquent models
    ├── database/
    │   └── migrations/     # Database schema
    └── README.md           # Backend documentation
```

## Features

- **Task Management** - Create, read, update, delete tasks
- **Priority System** - High, medium, low priority levels
- **Status Workflow** - Pending → In Progress → Done
- **Daily Reports** - Task summary by date
- **Dark Mode** - Toggle between light/dark themes
- **Responsive Design** - Works on desktop and mobile
- **Pagination** - 15 tasks per page
- **Filtering** - Filter by status

## Tech Stack

### Frontend
- Vue 3 (Composition API)
- Pinia (State Management)
- Vue Router 5
- Vite 7
- Axios

### Backend
- Laravel 13
- PHP 8.3
- MySQL
- PHPUnit (Testing)

## Documentation

- **Frontend**: See [`frontend/README.md`](frontend/README.md)
- **Backend**: See [`Server/README.md`](Server/README.md)
- **API Docs**: See [`Server/API_DOCUMENTATION.md`](Server/API_DOCUMENTATION.md)
- **Deployment**: See [`Server/DEPLOYMENT.md`](Server/DEPLOYMENT.md)

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/tasks` | Create task |
| GET | `/api/v1/tasks` | List tasks |
| PATCH | `/api/v1/tasks/{id}/status` | Update status |
| DELETE | `/api/v1/tasks/{id}` | Delete task |
| GET | `/api/v1/tasks/report` | Daily report |

## Development

### Running Tests

**Backend:**
```bash
cd Server
php artisan test
```

**Frontend:**
```bash
cd frontend
npm run test:unit
npm run test:e2e
```

### Linting

**Backend:**
```bash
cd Server
composer run lint
```

**Frontend:**
```bash
cd frontend
npm run lint
```

## Deployment

The application can be deployed to various platforms:

- **Backend**: Render (Docker), AWS, DigitalOcean
- **Frontend**: Vercel, Netlify, or any static host

See backend and frontend README files for detailed deployment instructions.

## License

Open-sourced software.

## Support

For issues or questions:
1. Check the respective README files in `frontend/` and `Server/`
2. Review API documentation in `Server/API_DOCUMENTATION.md`

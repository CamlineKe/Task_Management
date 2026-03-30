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
- MySQL/SQLite
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

# Task Management Frontend

A modern, responsive Vue 3 frontend for the Task Management application. Features a clean UI with dark mode support, real-time updates, and intuitive task management.

## Tech Stack

- **Vue 3** - Progressive JavaScript framework with Composition API
- **Pinia** - State management for Vue
- **Vue Router 5** - Client-side routing
- **Vite 7** - Fast build tool and dev server
- **Axios** - HTTP client for API requests

## Prerequisites

- **Node.js**: `^20.19.0 || >=22.12.0`
- **npm**: Comes with Node.js

## Project Structure

```
frontend/
├── src/
│   ├── assets/
│   │   └── styles/           # Global CSS and variables
│   ├── components/
│   │   ├── common/           # Reusable UI components (Button, Input, etc.)
│   │   ├── layout/           # App layout components (Header, Sidebar)
│   │   ├── modals/           # Modal dialogs (Create, Update, Delete)
│   │   ├── report/           # Report-specific components
│   │   └── tasks/            # Task-related components (Table, Filters, Pagination)
│   ├── composables/          # Vue composables (useTasks, useForm)
│   ├── router/               # Vue Router configuration
│   ├── stores/               # Pinia stores (ui, task, report)
│   ├── utils/                # Utility functions and constants
│   └── views/                # Page components (Dashboard, Tasks, Report)
├── public/                   # Static assets
└── index.html                # Entry HTML file
```

## Installation

```bash
# Install dependencies
npm install
```

## Development

```bash
# Start development server
npm run dev
```

The dev server will start at `http://localhost:5173` (or another available port).

## Build for Production

```bash
# Create optimized production build
npm run build
```

Output will be in the `dist/` directory.

## Available Scripts

| Script | Description |
|--------|-------------|
| `npm run dev` | Start Vite dev server with hot reload |
| `npm run build` | Build for production |
| `npm run preview` | Preview production build locally |
| `npm run lint` | Run all linters (ESLint + Oxlint) |
| `npm run lint:eslint` | Run ESLint with auto-fix |
| `npm run lint:oxlint` | Run Oxlint with auto-fix |
| `npm run test:unit` | Run unit tests with Vitest |
| `npm run test:e2e` | Run end-to-end tests with Playwright |

## Features

- **Dashboard**: Quick stats, today's summary, and recent tasks
- **Task Management**: Create, read, update status, and delete tasks
- **Filtering**: Filter tasks by status (All, Pending, In Progress, Done)
- **Pagination**: 15 tasks per page for optimal performance
- **Reports**: Daily task reports with priority breakdown
- **Dark Mode**: Toggle between light and dark themes with persistence
- **Responsive Design**: Works on desktop and mobile devices
- **Real-time Updates**: Instant UI updates after CRUD operations

## API Configuration

The frontend connects to the backend API. Configure the base URL in `src/utils/api.js`:

```javascript
const API_BASE_URL = 'http://localhost:8000/api/v1'
```

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)

## Recommended IDE Setup

- [VS Code](https://code.visualstudio.com/) + [Vue (Official)](https://marketplace.visualstudio.com/items?itemName=Vue.volar)
- [Vue.js devtools](https://chromewebstore.google.com/detail/vuejs-devtools/nhdogjmejiglipccpnnnanhbledajbpd) browser extension

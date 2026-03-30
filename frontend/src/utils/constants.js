/**
 * Constants
 * Application-wide constants for tasks, priorities, and statuses
 */

// Priority levels with labels and colors
export const PRIORITIES = {
  HIGH: {
    value: 'high',
    label: 'High',
    color: '#dc2626',
    bgColor: '#fee2e2',
  },
  MEDIUM: {
    value: 'medium',
    label: 'Medium',
    color: '#f59e0b',
    bgColor: '#fef3c7',
  },
  LOW: {
    value: 'low',
    label: 'Low',
    color: '#22c55e',
    bgColor: '#dcfce7',
  },
}

// Status levels with labels and colors
export const STATUSES = {
  PENDING: {
    value: 'pending',
    label: 'Pending',
    color: '#6b7280',
    bgColor: '#f3f4f6',
  },
  IN_PROGRESS: {
    value: 'in_progress',
    label: 'In Progress',
    color: '#3b82f6',
    bgColor: '#dbeafe',
  },
  DONE: {
    value: 'done',
    label: 'Done',
    color: '#22c55e',
    bgColor: '#dcfce7',
  },
}

// Status transitions map
// Defines allowed next statuses for each current status
export const STATUS_TRANSITIONS = {
  pending: ['in_progress'],
  in_progress: ['done'],
  done: [],
}

// API endpoints
export const API_ENDPOINTS = {
  TASKS: '/tasks',
  TASK_STATUS: (id) => `/tasks/${id}/status`,
  REPORT: '/tasks/report',
}

// Date formats
export const DATE_FORMAT = 'YYYY-MM-DD'

// Pagination defaults
export const PAGINATION = {
  DEFAULT_PAGE: 1,
  DEFAULT_PER_PAGE: 15,
  PER_PAGE_OPTIONS: [10, 15, 25, 50],
}

// Local storage keys
export const STORAGE_KEYS = {
  THEME: 'task-manager-theme',
  SIDEBAR_COLLAPSED: 'task-manager-sidebar',
}

// Route names
export const ROUTES = {
  DASHBOARD: 'dashboard',
  TASKS: 'tasks',
  REPORT: 'report',
}

// Notification durations (ms)
export const NOTIFICATION_DURATION = {
  SHORT: 3000,
  DEFAULT: 5000,
  LONG: 8000,
}

// Cache durations (ms)
export const CACHE_DURATION = {
  REPORT: 5 * 60 * 1000, // 5 minutes
}
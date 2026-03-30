/**
 * API Index
 * Centralized export of all API endpoints
 */

// Tasks API
export {
  getTasks,
  createTask,
  updateTaskStatus,
  deleteTask,
} from './endpoints/tasks.js'

// Report API
export {
  getDailyReport,
} from './endpoints/report.js'

// Client (for direct access if needed)
export { default as apiClient } from './client.js'
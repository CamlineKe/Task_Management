import apiClient from '../client.js'

/**
 * Tasks API Endpoints
 * All task-related API calls
 */

// Get all tasks with optional status filter
export const getTasks = (status = null) => {
  const params = status ? { status } : {}
  return apiClient.get('/tasks', { params })
}

// Create new task
export const createTask = (taskData) => {
  return apiClient.post('/tasks', taskData)
}

// Update task status
export const updateTaskStatus = (id, status) => {
  return apiClient.patch(`/tasks/${id}/status`, { status })
}

// Delete task (only allowed if status is 'done')
export const deleteTask = (id) => {
  return apiClient.delete(`/tasks/${id}`)
}
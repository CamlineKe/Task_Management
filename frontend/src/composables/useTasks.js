import { ref } from 'vue'
import { getTasks, createTask as apiCreateTask, updateTaskStatus as apiUpdateTaskStatus, deleteTask as apiDeleteTask } from '@/api/endpoints/tasks.js'

/**
 * useTasks Composable
 * Manages task data and operations
 */
export function useTasks() {
  const tasks = ref([])
  const loading = ref(false)
  const error = ref(null)

  /**
   * Fetch all tasks
   * @param {string} status - Optional status filter
   */
  async function fetchTasks(status = null) {
    loading.value = true
    error.value = null
    try {
      const response = await getTasks(status)
      tasks.value = response.data.data || []
      return { success: true, data: response.data }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch tasks'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  /**
   * Create a new task
   * @param {Object} taskData - Task data to create
   */
  async function createTask(taskData) {
    loading.value = true
    error.value = null
    try {
      const response = await apiCreateTask(taskData)
      return { success: true, data: response.data }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to create task'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  /**
   * Update task status
   * @param {string|number} id - Task ID
   * @param {string} status - New status
   */
  async function updateTaskStatus(id, status) {
    loading.value = true
    error.value = null
    try {
      const response = await apiUpdateTaskStatus(id, status)
      return { success: true, data: response.data }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to update task'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  /**
   * Delete a task
   * @param {string|number} id - Task ID
   */
  async function deleteTask(id) {
    loading.value = true
    error.value = null
    try {
      await apiDeleteTask(id)
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to delete task'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  return {
    // State
    tasks,
    loading,
    error,
    // Actions
    fetchTasks,
    createTask,
    updateTaskStatus,
    deleteTask,
  }
}
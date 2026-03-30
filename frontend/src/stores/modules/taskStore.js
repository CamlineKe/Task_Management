import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useReportStore } from './reportStore.js'
import {
  getTasks,
  createTask,
  updateTaskStatus,
  deleteTask,
} from '@/api/index.js'

/**
 * Task Store
 * Manages all task data and operations
 */
export const useTaskStore = defineStore('tasks', () => {
  // State
  const tasks = ref([])
  const loading = ref(false)
  const creating = ref(false)
  const updating = ref(false)
  const deleting = ref(false)
  const error = ref(null)
  const filterStatus = ref(null)

  // Getters
  const allTasks = computed(() => tasks.value)
  
  const filteredTasks = computed(() => {
    if (!filterStatus.value) return tasks.value
    return tasks.value.filter(task => task.status === filterStatus.value)
  })

  const tasksByStatus = computed(() => ({
    pending: tasks.value.filter(t => t.status === 'pending'),
    in_progress: tasks.value.filter(t => t.status === 'in_progress'),
    done: tasks.value.filter(t => t.status === 'done'),
  }))

  const stats = computed(() => ({
    total: tasks.value.length,
    pending: tasks.value.filter(t => t.status === 'pending').length,
    in_progress: tasks.value.filter(t => t.status === 'in_progress').length,
    done: tasks.value.filter(t => t.status === 'done').length,
  }))

  // Actions
  async function fetchTasks(status = null) {
    loading.value = true
    error.value = null
    
    try {
      const response = await getTasks(status)
      tasks.value = response.data.data || []
      filterStatus.value = status
      return tasks.value
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch tasks'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function addTask(taskData) {
    creating.value = true
    error.value = null
    
    try {
      const response = await createTask(taskData)
      const newTask = response.data
      
      // Add to beginning of list
      tasks.value.unshift(newTask)
      
      // Invalidate report cache since task data changed
      const reportStore = useReportStore()
      reportStore.invalidateCache()
      
      return newTask
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create task'
      throw err
    } finally {
      creating.value = false
    }
  }

  async function changeStatus(id, newStatus) {
    updating.value = true
    error.value = null
    
    // Optimistic update
    const taskIndex = tasks.value.findIndex(t => t.id === id)
    const oldStatus = tasks.value[taskIndex]?.status
    
    if (taskIndex !== -1) {
      tasks.value[taskIndex].status = newStatus
    }
    
    try {
      const response = await updateTaskStatus(id, newStatus)
      const updatedTask = response.data
      
      // Confirm update with server data
      if (taskIndex !== -1) {
        tasks.value[taskIndex] = updatedTask
      }
      
      // Invalidate report cache since task status changed
      const reportStore = useReportStore()
      reportStore.invalidateCache()
      
      return updatedTask
    } catch (err) {
      // Revert on error
      if (taskIndex !== -1 && oldStatus) {
        tasks.value[taskIndex].status = oldStatus
      }
      
      error.value = err.response?.data?.message || 'Failed to update status'
      throw err
    } finally {
      updating.value = false
    }
  }

  async function removeTask(id) {
    deleting.value = true
    error.value = null
    
    // Optimistic removal
    const taskIndex = tasks.value.findIndex(t => t.id === id)
    const removedTask = tasks.value[taskIndex]
    
    if (taskIndex !== -1) {
      tasks.value.splice(taskIndex, 1)
    }
    
    try {
      await deleteTask(id)
      
      // Invalidate report cache since task was deleted
      const reportStore = useReportStore()
      reportStore.invalidateCache()
      
      return true
    } catch (err) {
      // Restore on error
      if (removedTask && taskIndex !== -1) {
        tasks.value.splice(taskIndex, 0, removedTask)
      }
      
      error.value = err.response?.data?.message || 'Failed to delete task'
      throw err
    } finally {
      deleting.value = false
    }
  }

  function setFilter(status) {
    filterStatus.value = status
  }

  function clearFilter() {
    filterStatus.value = null
  }

  function clearError() {
    error.value = null
  }

  return {
    // State
    tasks,
    loading,
    creating,
    updating,
    deleting,
    error,
    filterStatus,
    
    // Getters
    allTasks,
    filteredTasks,
    tasksByStatus,
    stats,
    
    // Actions
    fetchTasks,
    addTask,
    changeStatus,
    removeTask,
    setFilter,
    clearFilter,
    clearError,
  }
})
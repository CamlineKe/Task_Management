import { ref } from 'vue'

/**
 * useNotifications Composable
 * Manages notification queue with auto-dismiss timers
 * Supports different types: success, error, warning, info
 */
export function useNotifications(options = {}) {
  const {
    defaultDuration = 5000,
    maxNotifications = 5,
  } = options

  // State
  const notifications = ref([])

  /**
   * Add notification to queue
   * @param {Object} notification - Notification config
   * @returns {string} Notification ID
   */
  function add(notification) {
    const id = Date.now().toString()
    
    const newNotification = {
      id,
      type: notification.type || 'info',
      message: notification.message || '',
      title: notification.title || '',
      duration: notification.duration ?? defaultDuration,
      progress: 100,
      timestamp: Date.now(),
    }

    // Remove oldest if at max
    if (notifications.value.length >= maxNotifications) {
      notifications.value.shift()
    }

    notifications.value.push(newNotification)

    // Auto dismiss if duration > 0
    if (newNotification.duration > 0) {
      startTimer(id, newNotification.duration)
    }

    return id
  }

  /**
   * Start auto-dismiss timer with progress
   */
  function startTimer(id, duration) {
    const startTime = Date.now()
    const interval = 100 // Update every 100ms
    
    const timer = setInterval(() => {
      const elapsed = Date.now() - startTime
      const notification = notifications.value.find(n => n.id === id)
      
      if (!notification) {
        clearInterval(timer)
        return
      }

      // Update progress
      notification.progress = Math.max(0, 100 - (elapsed / duration) * 100)

      // Dismiss when complete
      if (elapsed >= duration) {
        clearInterval(timer)
        remove(id)
      }
    }, interval)
  }

  /**
   * Remove notification by ID
   */
  function remove(id) {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  /**
   * Clear all notifications
   */
  function clear() {
    notifications.value = []
  }

  // Shorthand methods
  function success(message, duration) {
    return add({ type: 'success', message, duration })
  }

  function error(message, duration) {
    return add({ type: 'error', message, duration })
  }

  function warning(message, duration) {
    return add({ type: 'warning', message, duration })
  }

  function info(message, duration) {
    return add({ type: 'info', message, duration })
  }

  return {
    // State
    notifications,
    
    // Actions
    add,
    remove,
    clear,
    success,
    error,
    warning,
    info,
  }
}
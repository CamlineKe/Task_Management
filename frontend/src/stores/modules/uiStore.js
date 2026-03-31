import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

/**
 * UI Store
 * Manages interface state, modals, notifications, and theme
 */
export const useUiStore = defineStore('ui', () => {
  // State
  const sidebarCollapsed = ref(false)
  const mobileSidebarOpen = ref(false)
  const theme = ref('dark')
  const online = ref(navigator.onLine)
  
  // Modal states
  const modals = ref({
    createTask: false,
    updateStatus: false,
    deleteConfirm: false,
  })
  
  // Current task for modals
  const selectedTask = ref(null)
  
  // Notifications queue
  const notifications = ref([])
  
  // Loading states
  const globalLoading = ref(false)

  // Getters
  const isSidebarCollapsed = computed(() => sidebarCollapsed.value)
  const isMobileSidebarOpen = computed(() => mobileSidebarOpen.value)
  const isDarkMode = computed(() => theme.value === 'dark')
  const isOnline = computed(() => online.value)
  const activeModal = computed(() => {
    return Object.entries(modals.value).find(([_, isOpen]) => isOpen)?.[0] || null
  })
  const hasNotifications = computed(() => notifications.value.length > 0)

  // Actions
  function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value
  }

  function toggleMobileSidebar() {
    mobileSidebarOpen.value = !mobileSidebarOpen.value
  }

  function closeMobileSidebar() {
    mobileSidebarOpen.value = false
  }

  function setSidebarCollapsed(collapsed) {
    sidebarCollapsed.value = collapsed
  }

  function toggleTheme() {
    theme.value = theme.value === 'light' ? 'dark' : 'light'
    // Apply theme to document
    document.documentElement.setAttribute('data-theme', theme.value)
    // Save to localStorage
    localStorage.setItem('theme', theme.value)
  }

  function setTheme(newTheme) {
    theme.value = newTheme
    document.documentElement.setAttribute('data-theme', newTheme)
    // Save to localStorage
    localStorage.setItem('theme', newTheme)
  }

  function setOnlineStatus(status) {
    online.value = status
  }

  // Modal actions
  function openModal(modalName, task = null) {
    if (selectedTask.value && task) {
      selectedTask.value = task
    } else if (task) {
      selectedTask.value = task
    }
    modals.value[modalName] = true
  }

  function closeModal(modalName) {
    modals.value[modalName] = false
    // Clear selected task when closing modals
    if (!Object.values(modals.value).some(v => v)) {
      selectedTask.value = null
    }
  }

  function closeAllModals() {
    Object.keys(modals.value).forEach(key => {
      modals.value[key] = false
    })
    selectedTask.value = null
  }

  function setSelectedTask(task) {
    selectedTask.value = task
  }

  // Notification actions
  function addNotification({ type = 'info', message, duration = 5000 }) {
    const id = Date.now()
    const notification = {
      id,
      type, // success, error, warning, info
      message,
      duration,
      progress: 100,
    }
    
    notifications.value.push(notification)
    
    // Auto remove after duration
    if (duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, duration)
    }
    
    return id
  }

  function removeNotification(id) {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  function clearNotifications() {
    notifications.value = []
  }

  // Shorthand notification methods
  function notifySuccess(message, duration) {
    return addNotification({ type: 'success', message, duration })
  }

  function notifyError(message, duration) {
    return addNotification({ type: 'error', message, duration })
  }

  function notifyWarning(message, duration) {
    return addNotification({ type: 'warning', message, duration })
  }

  function notifyInfo(message, duration) {
    return addNotification({ type: 'info', message, duration })
  }

  // Global loading
  function setGlobalLoading(loading) {
    globalLoading.value = loading
  }

  // Network status listeners
  function initNetworkListeners() {
    window.addEventListener('online', () => setOnlineStatus(true))
    window.addEventListener('offline', () => setOnlineStatus(false))
  }

  return {
    // State
    sidebarCollapsed,
    mobileSidebarOpen,
    theme,
    online,
    modals,
    selectedTask,
    notifications,
    globalLoading,
    
    // Getters
    isSidebarCollapsed,
    isMobileSidebarOpen,
    isDarkMode,
    isOnline,
    activeModal,
    hasNotifications,
    
    // Actions
    toggleSidebar,
    toggleMobileSidebar,
    closeMobileSidebar,
    setSidebarCollapsed,
    toggleTheme,
    setTheme,
    setOnlineStatus,
    openModal,
    closeModal,
    closeAllModals,
    setSelectedTask,
    addNotification,
    removeNotification,
    clearNotifications,
    notifySuccess,
    notifyError,
    notifyWarning,
    notifyInfo,
    setGlobalLoading,
    initNetworkListeners,
  }
})
/**
 * Stores Index
 * Centralized Pinia store exports
 */

import { createPinia } from 'pinia'

// Export store instances
export { useTaskStore } from './modules/taskStore.js'
export { useUiStore } from './modules/uiStore.js'
export { useReportStore } from './modules/reportStore.js'

// Export pinia instance for app initialization
export const pinia = createPinia()
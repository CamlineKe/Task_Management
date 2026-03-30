/**
 * Formatters
 * Utility functions for formatting dates, numbers, and text
 */

import { PRIORITIES, STATUSES } from './constants.js'

/**
 * Format date to display format
 * @param {string} dateString - Date in YYYY-MM-DD format
 * @returns {string} Formatted date string
 */
export function formatDate(dateString) {
  if (!dateString) return ''
  
  const date = new Date(dateString)
  const options = { year: 'numeric', month: 'short', day: 'numeric' }
  
  return date.toLocaleDateString('en-US', options)
}

/**
 * Format date to relative time (today, tomorrow, yesterday, or date)
 * @param {string} dateString - Date in YYYY-MM-DD format
 * @returns {string} Relative time string
 */
export function formatRelativeDate(dateString) {
  if (!dateString) return ''
  
  const date = new Date(dateString)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  const targetDate = new Date(date)
  targetDate.setHours(0, 0, 0, 0)
  
  const diffTime = targetDate.getTime() - today.getTime()
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 0) return 'Today'
  if (diffDays === 1) return 'Tomorrow'
  if (diffDays === -1) return 'Yesterday'
  
  return formatDate(dateString)
}

/**
 * Get priority display config
 * @param {string} priority - Priority value
 * @returns {Object} Priority config object
 */
export function getPriorityConfig(priority) {
  return Object.values(PRIORITIES).find(p => p.value === priority) || PRIORITIES.MEDIUM
}

/**
 * Get status display config
 * @param {string} status - Status value
 * @returns {Object} Status config object
 */
export function getStatusConfig(status) {
  return Object.values(STATUSES).find(s => s.value === status) || STATUSES.PENDING
}

/**
 * Format priority for display
 * @param {string} priority - Priority value
 * @returns {string} Formatted priority label
 */
export function formatPriority(priority) {
  const config = getPriorityConfig(priority)
  return config.label
}

/**
 * Format status for display
 * @param {string} status - Status value
 * @returns {string} Formatted status label
 */
export function formatStatus(status) {
  const config = getStatusConfig(status)
  return config.label
}

/**
 * Capitalize first letter of string
 * @param {string} str - Input string
 * @returns {string} Capitalized string
 */
export function capitalize(str) {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}

/**
 * Format number with commas
 * @param {number} num - Number to format
 * @returns {string} Formatted number string
 */
export function formatNumber(num) {
  if (num === null || num === undefined) return '0'
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

/**
 * Truncate text with ellipsis
 * @param {string} text - Text to truncate
 * @param {number} length - Max length
 * @returns {string} Truncated text
 */
export function truncate(text, length = 50) {
  if (!text) return ''
  if (text.length <= length) return text
  return text.substring(0, length) + '...'
}
/**
 * Validators
 * Form validation utility functions
 */

/**
 * Validate required field
 * @param {*} value - Field value
 * @returns {string|null} Error message or null if valid
 */
export function required(value) {
  if (value === null || value === undefined || value === '') {
    return 'This field is required'
  }
  return null
}

/**
 * Validate string minimum length
 * @param {number} min - Minimum length
 * @returns {Function} Validator function
 */
export function minLength(min) {
  return (value) => {
    if (!value || value.length < min) {
      return `Must be at least ${min} characters`
    }
    return null
  }
}

/**
 * Validate string maximum length
 * @param {number} max - Maximum length
 * @returns {Function} Validator function
 */
export function maxLength(max) {
  return (value) => {
    if (value && value.length > max) {
      return `Must be at most ${max} characters`
    }
    return null
  }
}

/**
 * Validate date is today or future
 * @param {string} dateString - Date in YYYY-MM-DD format
 * @returns {string|null} Error message or null if valid
 */
export function futureDate(dateString) {
  if (!dateString) return 'Date is required'
  
  const inputDate = new Date(dateString)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  if (inputDate < today) {
    return 'Date must be today or in the future'
  }
  return null
}

/**
 * Validate date format (YYYY-MM-DD)
 * @param {string} dateString - Date string to validate
 * @returns {string|null} Error message or null if valid
 */
export function dateFormat(dateString) {
  if (!dateString) return 'Date is required'
  
  const regex = /^\d{4}-\d{2}-\d{2}$/
  if (!regex.test(dateString)) {
    return 'Invalid date format. Use YYYY-MM-DD'
  }
  
  const date = new Date(dateString)
  if (isNaN(date.getTime())) {
    return 'Invalid date'
  }
  
  return null
}

/**
 * Validate priority value
 * @param {string} value - Priority value
 * @returns {string|null} Error message or null if valid
 */
export function validPriority(value) {
  const valid = ['low', 'medium', 'high']
  if (!valid.includes(value)) {
    return 'Priority must be low, medium, or high'
  }
  return null
}

/**
 * Validate status value
 * @param {string} value - Status value
 * @returns {string|null} Error message or null if valid
 */
export function validStatus(value) {
  const valid = ['pending', 'in_progress', 'done']
  if (!valid.includes(value)) {
    return 'Status must be pending, in_progress, or done'
  }
  return null
}

/**
 * Compose multiple validators
 * @param {...Function} validators - Validator functions
 * @returns {Function} Composed validator
 */
export function compose(...validators) {
  return (value, formValues) => {
    for (const validator of validators) {
      const error = validator(value, formValues)
      if (error) return error
    }
    return null
  }
}

/**
 * Create form validation schema
 * @param {Object} schema - Field validation rules
 * @returns {Object} Validation schema
 */
export function createSchema(schema) {
  return schema
}
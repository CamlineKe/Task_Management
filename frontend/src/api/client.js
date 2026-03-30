import axios from 'axios'

/**
 * API Client Configuration
 * Axios instance with base URL and default headers
 */

// Create axios instance with base configuration
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 30000, // 30 seconds timeout
})

// Request interceptor - add auth token if needed
apiClient.interceptors.request.use(
  (config) => {
    // Log requests in development
    if (import.meta.env.DEV) {
      console.log(`[API Request] ${config.method?.toUpperCase()} ${config.url}`, config.params || config.data)
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor - handle errors globally
apiClient.interceptors.response.use(
  (response) => {
    // Log responses in development
    if (import.meta.env.DEV) {
      console.log(`[API Response] ${response.config.url}`, response.data)
    }
    return response
  },
  (error) => {
    // Handle common errors
    if (error.response) {
      // Server responded with error status
      console.error(`[API Error] ${error.config?.url}:`, error.response.status, error.response.data)
      
      // Handle 422 validation errors
      if (error.response.status === 422) {
        // Validation errors will be handled by components
      }
      
      // Handle 404 not found
      if (error.response.status === 404) {
        console.error('Resource not found')
      }
      
      // Handle 403 forbidden
      if (error.response.status === 403) {
        console.error('Action not allowed')
      }
    } else if (error.request) {
      // Request made but no response received
      console.error('[API Error] No response received:', error.request)
    } else {
      // Error in setting up request
      console.error('[API Error] Request setup failed:', error.message)
    }
    
    return Promise.reject(error)
  }
)

export default apiClient
import { ref } from 'vue'

/**
 * useApi Composable
 * Generic API call handler with loading and error states
 * Provides request cancellation and retry logic
 */
export function useApi() {
  const loading = ref(false)
  const error = ref(null)
  const data = ref(null)
  
  // Abort controller for request cancellation
  let abortController = null

  /**
   * Execute API call with loading state management
   * @param {Function} apiFunction - Async API function to call
   * @param {Object} options - Options for the call
   * @returns {Promise} - Resolves with response data
   */
  async function execute(apiFunction, options = {}) {
    const { 
      onSuccess = null, 
      onError = null,
      resetOnExecute = true,
      silent = false 
    } = options

    // Cancel previous request if exists
    if (abortController) {
      abortController.abort()
    }
    
    // Create new abort controller
    abortController = new AbortController()

    // Reset state
    if (resetOnExecute) {
      error.value = null
      data.value = null
    }
    
    if (!silent) {
      loading.value = true
    }

    try {
      const response = await apiFunction()
      data.value = response.data
      
      // Call success callback if provided
      if (onSuccess) {
        onSuccess(response.data)
      }
      
      return response
    } catch (err) {
      // Don't set error if request was cancelled
      if (err.name === 'AbortError' || err.code === 'ERR_CANCELED') {
        return null
      }
      
      error.value = err.response?.data?.message || err.message || 'An error occurred'
      
      // Call error callback if provided
      if (onError) {
        onError(err)
      }
      
      throw err
    } finally {
      if (!silent) {
        loading.value = false
      }
      abortController = null
    }
  }

  /**
   * Cancel current request
   */
  function cancel() {
    if (abortController) {
      abortController.abort()
      abortController = null
      loading.value = false
    }
  }

  /**
   * Reset all states
   */
  function reset() {
    loading.value = false
    error.value = null
    data.value = null
    abortController = null
  }

  return {
    loading,
    error,
    data,
    execute,
    cancel,
    reset,
  }
}
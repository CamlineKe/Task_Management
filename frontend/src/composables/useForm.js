import { ref, computed } from 'vue'

/**
 * useForm Composable
 * Manages form state, validation, and submission
 * Compatible with any form component
 */
export function useForm(initialValues = {}, options = {}) {
  const {
    validationRules = {},
    onSubmit = null,
    resetAfterSubmit = false,
  } = options

  // Form state
  const values = ref({ ...initialValues })
  const errors = ref({})
  const touched = ref({})
  const dirty = ref(false)
  const submitting = ref(false)
  const submitError = ref(null)

  // Track original values for dirty checking
  const originalValues = ref({ ...initialValues })

  // Computed
  const isDirty = computed(() => {
    return JSON.stringify(values.value) !== JSON.stringify(originalValues.value)
  })

  const isValid = computed(() => {
    return Object.keys(errors.value).length === 0
  })

  const hasErrors = computed(() => {
    return Object.keys(errors.value).length > 0
  })

  const isTouched = computed(() => {
    return Object.values(touched.value).some(v => v)
  })

  const canSubmit = computed(() => {
    return isValid.value && !submitting.value
  })

  // Actions
  function setValue(field, value) {
    values.value[field] = value
    dirty.value = true
    
    // Clear error when field is modified
    if (errors.value[field]) {
      delete errors.value[field]
    }
  }

  function getValue(field) {
    return values.value[field]
  }

  function touch(field) {
    touched.value[field] = true
    validateField(field)
  }

  function validateField(field) {
    const rule = validationRules[field]
    if (!rule) return true

    const value = values.value[field]
    const error = rule(value, values.value)

    if (error) {
      errors.value[field] = error
      return false
    } else {
      delete errors.value[field]
      return true
    }
  }

  function validateAll() {
    let isFormValid = true
    
    for (const field of Object.keys(validationRules)) {
      const fieldValid = validateField(field)
      if (!fieldValid) {
        isFormValid = false
      }
      // Mark all fields as touched
      touched.value[field] = true
    }
    
    return isFormValid
  }

  function clearError(field) {
    if (field) {
      delete errors.value[field]
    } else {
      errors.value = {}
    }
  }

  function reset(newValues = null) {
    values.value = newValues ? { ...newValues } : { ...originalValues.value }
    errors.value = {}
    touched.value = {}
    dirty.value = false
    submitError.value = null
  }

  async function submit(submitFn = null) {
    const fn = submitFn || onSubmit
    
    if (!fn) {
      throw new Error('No submit function provided')
    }

    // Validate before submit
    if (!validateAll()) {
      return { success: false, errors: errors.value }
    }

    submitting.value = true
    submitError.value = null

    try {
      const result = await fn(values.value)
      
      if (resetAfterSubmit) {
        reset()
      }
      
      return { success: true, data: result }
    } catch (err) {
      submitError.value = err.response?.data?.message || err.message || 'Submit failed'
      
      // Handle validation errors from server
      if (err.response?.data?.errors) {
        errors.value = err.response.data.errors
      }
      
      return { success: false, error: submitError.value, errors: errors.value }
    } finally {
      submitting.value = false
    }
  }

  function handleSubmit(fn) {
    return (event) => {
      if (event) event.preventDefault()
      return submit(fn)
    }
  }

  return {
    // State
    values,
    errors,
    touched,
    dirty,
    submitting,
    submitError,
    
    // Computed
    isDirty,
    isValid,
    hasErrors,
    isTouched,
    canSubmit,
    
    // Actions
    setValue,
    getValue,
    touch,
    validateField,
    validateAll,
    clearError,
    reset,
    submit,
    handleSubmit,
  }
}
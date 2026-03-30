import { ref, nextTick } from 'vue'

/**
 * useModal Composable
 * Manages individual modal state with accessibility features
 * Handles focus trapping and keyboard navigation
 */
export function useModal(options = {}) {
  const {
    onOpen = null,
    onClose = null,
    returnFocus = true,
  } = options

  const isOpen = ref(false)
  const triggerElement = ref(null)

  /**
   * Open modal
   * @param {HTMLElement} trigger - Element that triggered modal (for focus return)
   */
  function open(trigger = null) {
    if (trigger) {
      triggerElement.value = trigger
    }
    
    isOpen.value = true
    
    // Call open callback
    if (onOpen) {
      onOpen()
    }
    
    // Focus modal after render
    nextTick(() => {
      const modal = document.querySelector('[role="dialog"]')
      if (modal) {
        const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
        if (focusable) {
          focusable.focus()
        }
      }
    })
  }

  /**
   * Close modal
   * @param {boolean} returnFocusToTrigger - Whether to return focus to trigger element
   */
  function close(returnFocusToTrigger = returnFocus) {
    isOpen.value = false
    
    // Call close callback
    if (onClose) {
      onClose()
    }
    
    // Return focus to trigger element
    if (returnFocusToTrigger && triggerElement.value) {
      nextTick(() => {
        triggerElement.value?.focus()
      })
    }
    
    triggerElement.value = null
  }

  /**
   * Toggle modal state
   */
  function toggle(trigger = null) {
    if (isOpen.value) {
      close()
    } else {
      open(trigger)
    }
  }

  /**
   * Handle escape key press
   */
  function handleEscape(event) {
    if (event.key === 'Escape' && isOpen.value) {
      event.stopPropagation()
      close()
    }
  }

  /**
   * Handle click outside modal content
   */
  function handleClickOutside(event, modalContentRef) {
    if (modalContentRef && !modalContentRef.contains(event.target)) {
      close()
    }
  }

  /**
   * Trap focus within modal
   */
  function trapFocus(event) {
    if (event.key !== 'Tab' || !isOpen.value) return
    
    const modal = document.querySelector('[role="dialog"]')
    if (!modal) return
    
    const focusableElements = modal.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"]):not([disabled])'
    )
    
    const firstElement = focusableElements[0]
    const lastElement = focusableElements[focusableElements.length - 1]
    
    if (event.shiftKey && document.activeElement === firstElement) {
      event.preventDefault()
      lastElement.focus()
    } else if (!event.shiftKey && document.activeElement === lastElement) {
      event.preventDefault()
      firstElement.focus()
    }
  }

  return {
    // State
    isOpen,
    
    // Actions
    open,
    close,
    toggle,
    handleEscape,
    handleClickOutside,
    trapFocus,
  }
}
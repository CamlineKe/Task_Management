import { ref, computed, watch } from 'vue'

/**
 * usePagination Composable
 * Manages pagination state and calculations
 * Generates page range for display
 */
export function usePagination(options = {}) {
  const {
    initialPage = 1,
    initialPerPage = 15,
    totalItems = ref(0),
  } = options

  // State
  const currentPage = ref(initialPage)
  const perPage = ref(initialPerPage)

  // Computed
  const totalPages = computed(() => {
    return Math.ceil(totalItems.value / perPage.value) || 1
  })

  const startIndex = computed(() => {
    return (currentPage.value - 1) * perPage.value
  })

  const endIndex = computed(() => {
    return Math.min(startIndex.value + perPage.value, totalItems.value)
  })

  const hasPrevious = computed(() => {
    return currentPage.value > 1
  })

  const hasNext = computed(() => {
    return currentPage.value < totalPages.value
  })

  const isFirstPage = computed(() => {
    return currentPage.value === 1
  })

  const isLastPage = computed(() => {
    return currentPage.value === totalPages.value
  })

  // Generate page range for display (e.g., [1, 2, 3, '...', 10])
  const pageRange = computed(() => {
    const total = totalPages.value
    const current = currentPage.value
    const delta = 2 // Number of pages to show on each side of current
    
    const range = []
    
    for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
      range.push(i)
    }
    
    if (current - delta > 2) {
      range.unshift('...')
    }
    
    if (current + delta < total - 1) {
      range.push('...')
    }
    
    range.unshift(1)
    
    if (total > 1) {
      range.push(total)
    }
    
    return range
  })

  // Actions
  function goToPage(page) {
    if (page >= 1 && page <= totalPages.value) {
      currentPage.value = page
    }
  }

  function nextPage() {
    if (hasNext.value) {
      currentPage.value++
    }
  }

  function previousPage() {
    if (hasPrevious.value) {
      currentPage.value--
    }
  }

  function firstPage() {
    currentPage.value = 1
  }

  function lastPage() {
    currentPage.value = totalPages.value
  }

  function setPerPage(newPerPage) {
    perPage.value = newPerPage
    currentPage.value = 1 // Reset to first page
  }

  function reset() {
    currentPage.value = initialPage
    perPage.value = initialPerPage
  }

  // Watch for total items changes and adjust current page if needed
  watch(totalPages, (newTotalPages) => {
    if (currentPage.value > newTotalPages) {
      currentPage.value = newTotalPages || 1
    }
  })

  return {
    // State
    currentPage,
    perPage,
    
    // Computed
    totalPages,
    startIndex,
    endIndex,
    hasPrevious,
    hasNext,
    isFirstPage,
    isLastPage,
    pageRange,
    
    // Actions
    goToPage,
    nextPage,
    previousPage,
    firstPage,
    lastPage,
    setPerPage,
    reset,
  }
}
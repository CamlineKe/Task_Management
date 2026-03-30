import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { getDailyReport } from '@/api/index.js'

/**
 * Report Store
 * Manages daily report data with caching
 */
export const useReportStore = defineStore('report', () => {
  // State
  const reports = ref(new Map()) // Cache reports by date
  const currentDate = ref(new Date().toISOString().split('T')[0])
  const loading = ref(false)
  const error = ref(null)
  const lastFetchTime = ref(new Map())

  // Cache duration: 5 minutes
  const CACHE_DURATION = 5 * 60 * 1000

  // Getters
  const currentReport = computed(() => {
    return reports.value.get(currentDate.value) || null
  })

  const summary = computed(() => {
    return currentReport.value?.summary || {
      high: { pending: 0, in_progress: 0, done: 0 },
      medium: { pending: 0, in_progress: 0, done: 0 },
      low: { pending: 0, in_progress: 0, done: 0 },
    }
  })

  const totalTasksForDate = computed(() => {
    let total = 0
    Object.values(summary.value).forEach(priority => {
      Object.values(priority).forEach(count => {
        total += count
      })
    })
    return total
  })

  const hasReport = computed(() => {
    return reports.value.has(currentDate.value)
  })

  const isStale = computed(() => {
    const lastFetch = lastFetchTime.value.get(currentDate.value)
    if (!lastFetch) return true
    return Date.now() - lastFetch > CACHE_DURATION
  })

  // Actions
  async function fetchReport(date = null, forceRefresh = false) {
    const targetDate = date || currentDate.value
    
    // Check cache first
    if (!forceRefresh && reports.value.has(targetDate) && !isStale.value) {
      currentDate.value = targetDate
      return reports.value.get(targetDate)
    }

    loading.value = true
    error.value = null

    try {
      const response = await getDailyReport(targetDate)
      const reportData = response.data

      // Store in cache
      reports.value.set(targetDate, reportData)
      lastFetchTime.value.set(targetDate, Date.now())
      currentDate.value = targetDate

      return reportData
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch report'
      throw err
    } finally {
      loading.value = false
    }
  }

  function setDate(date) {
    currentDate.value = date
  }

  function clearCache() {
    reports.value.clear()
    lastFetchTime.value.clear()
  }

  function clearCacheForDate(date) {
    reports.value.delete(date)
    lastFetchTime.value.delete(date)
  }

  function invalidateCache() {
    // Clear all cache (useful after task mutations)
    clearCache()
  }

  // Get report for specific date without setting as current
  function getCachedReport(date) {
    return reports.value.get(date) || null
  }

  // Check if date has cached report
  function hasCachedReport(date) {
    return reports.value.has(date)
  }

  return {
    // State
    reports,
    currentDate,
    loading,
    error,
    lastFetchTime,
    
    // Getters
    currentReport,
    summary,
    totalTasksForDate,
    hasReport,
    isStale,
    
    // Actions
    fetchReport,
    setDate,
    clearCache,
    clearCacheForDate,
    invalidateCache,
    getCachedReport,
    hasCachedReport,
  }
})
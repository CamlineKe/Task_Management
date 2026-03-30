import apiClient from '../client.js'

/**
 * Report API Endpoints
 * Daily report generation API calls
 */

// Get daily report for specific date
// Date format: YYYY-MM-DD
export const getDailyReport = (date) => {
  return apiClient.get('/tasks/report', {
    params: { date },
  })
}
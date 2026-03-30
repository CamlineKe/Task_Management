<script setup>
import { onMounted, ref, computed } from "vue";
import { useReportStore } from "@/stores/index.js";
import { formatDate, formatRelativeDate } from "@/utils/formatters.js";

const reportStore = useReportStore();

const selectedDate = ref(new Date().toISOString().split("T")[0]);

const summary = computed(() => reportStore.summary);

const totalTasks = computed(() => reportStore.totalTasksForDate);

const priorityTotals = computed(() => {
  const result = {};
  for (const [priority, statuses] of Object.entries(summary.value)) {
    result[priority] = Object.values(statuses).reduce((a, b) => a + b, 0);
  }
  return result;
});

const statusTotals = computed(() => {
  const result = { pending: 0, in_progress: 0, done: 0 };
  for (const statuses of Object.values(summary.value)) {
    result.pending += statuses.pending;
    result.in_progress += statuses.in_progress;
    result.done += statuses.done;
  }
  return result;
});

onMounted(() => {
  reportStore.fetchReport(selectedDate.value);
});

function handleDateChange(event) {
  selectedDate.value = event.target.value;
  reportStore.fetchReport(selectedDate.value);
}

function refreshReport() {
  reportStore.fetchReport(selectedDate.value, true);
}

function getPriorityColor(priority) {
  const colors = {
    high: "var(--priority-high)",
    medium: "var(--priority-medium)",
    low: "var(--priority-low)",
  };
  return colors[priority] || "var(--gray-400)";
}

function getStatusColor(status) {
  const colors = {
    pending: "var(--status-pending)",
    in_progress: "var(--status-in-progress)",
    done: "var(--status-done)",
  };
  return colors[status] || "var(--gray-400)";
}

function getStatusLabel(status) {
  const labels = {
    pending: "Pending",
    in_progress: "In Progress",
    done: "Done",
  };
  return labels[status] || status;
}
</script>

<template>
  <div class="report-page">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1>Daily Report</h1>
        <p class="subtitle">Task summary by priority and status</p>
      </div>

      <div class="header-actions">
        <!-- Date Picker -->
        <div class="date-picker-wrapper">
          <label for="report-date">Date:</label>
          <input
            id="report-date"
            type="date"
            v-model="selectedDate"
            @change="handleDateChange"
            class="date-input"
          />
        </div>

        <!-- Refresh Button -->
        <button class="btn-secondary" @click="refreshReport" :disabled="reportStore.loading">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            :class="{ spinning: reportStore.loading }"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Date Display -->
    <div class="date-display">
      <span class="date-label">{{ formatRelativeDate(selectedDate) }}</span>
      <span v-if="reportStore.isStale" class="cache-badge">Cached</span>
    </div>

    <!-- Loading State -->
    <div v-if="reportStore.loading" class="loading-state">
      <div class="spinner"></div>
      <p>Generating report...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="totalTasks === 0" class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
        />
      </svg>
      <h3>No tasks for this date</h3>
      <p>There are no tasks due on {{ formatDate(selectedDate) }}</p>
    </div>

    <!-- Report Content -->
    <template v-else>
      <!-- Overview Cards -->
      <div class="overview-grid">
        <div class="overview-card total">
          <span class="overview-value">{{ totalTasks }}</span>
          <span class="overview-label">Total Tasks</span>
        </div>

        <div
          v-for="(count, status) in statusTotals"
          :key="status"
          class="overview-card"
          :class="`status-${status}`"
        >
          <span class="overview-value">{{ count }}</span>
          <span class="overview-label">{{ getStatusLabel(status) }}</span>
        </div>
      </div>

      <!-- Priority Summary Cards -->
      <div class="priority-grid">
        <div v-for="priority in ['high', 'medium', 'low']" :key="priority" class="priority-card">
          <div class="priority-header" :style="{ borderColor: getPriorityColor(priority) }">
            <h3>{{ priority.charAt(0).toUpperCase() + priority.slice(1) }} Priority</h3>
            <span class="priority-total">{{ priorityTotals[priority] }}</span>
          </div>

          <div class="status-breakdown">
            <div v-for="(count, status) in summary[priority]" :key="status" class="status-row">
              <div class="status-info">
                <span class="status-dot" :style="{ backgroundColor: getStatusColor(status) }" />
                <span class="status-name">{{ getStatusLabel(status) }}</span>
              </div>
              <span class="status-count" :class="{ zero: count === 0 }">
                {{ count }}
              </span>
            </div>
          </div>

          <!-- Mini Bar Chart -->
          <div class="mini-chart">
            <div
              v-for="(count, status) in summary[priority]"
              :key="status"
              class="chart-bar"
              :style="{
                width:
                  priorityTotals[priority] > 0
                    ? `${(count / priorityTotals[priority]) * 100}%`
                    : '0%',
                backgroundColor: getStatusColor(status),
              }"
              :title="`${getStatusLabel(status)}: ${count}`"
            />
          </div>
        </div>
      </div>

      <!-- Detailed Table -->
      <div class="details-card">
        <h3>Detailed Breakdown</h3>
        <table class="details-table">
          <thead>
            <tr>
              <th>Priority</th>
              <th>Pending</th>
              <th>In Progress</th>
              <th>Done</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="priority in ['high', 'medium', 'low']" :key="priority">
              <td>
                <span
                  class="priority-badge"
                  :style="{
                    backgroundColor: getPriorityColor(priority) + '20',
                    color: getPriorityColor(priority),
                  }"
                >
                  {{ priority.charAt(0).toUpperCase() + priority.slice(1) }}
                </span>
              </td>
              <td>{{ summary[priority].pending }}</td>
              <td>{{ summary[priority].in_progress }}</td>
              <td>{{ summary[priority].done }}</td>
              <td class="total-cell">{{ priorityTotals[priority] }}</td>
            </tr>
            <tr class="totals-row">
              <td><strong>Total</strong></td>
              <td>
                <strong>{{ statusTotals.pending }}</strong>
              </td>
              <td>
                <strong>{{ statusTotals.in_progress }}</strong>
              </td>
              <td>
                <strong>{{ statusTotals.done }}</strong>
              </td>
              <td class="total-cell">
                <strong>{{ totalTasks }}</strong>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </div>
</template>

<style scoped>
.report-page {
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
}

.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: var(--space-4);
}

.page-header h1 {
  font-size: var(--text-2xl);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0;
}

.subtitle {
  color: var(--gray-500);
  margin: var(--space-1) 0 0;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: var(--space-4);
}

.date-picker-wrapper {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.date-picker-wrapper label {
  font-size: var(--text-sm);
  color: var(--gray-600);
  font-weight: var(--font-medium);
}

.date-input {
  padding: var(--space-2) var(--space-3);
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  color: var(--gray-700);
  background-color: white;
}

.btn-secondary {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  background-color: white;
  color: var(--gray-700);
  border: 1px solid var(--gray-300);
  padding: var(--space-2) var(--space-4);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--duration-fast);
}

.btn-secondary:hover:not(:disabled) {
  background-color: var(--gray-50);
  border-color: var(--gray-400);
}

.btn-secondary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary svg {
  width: 18px;
  height: 18px;
}

.btn-secondary svg.spinning {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Date Display */
.date-display {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.date-label {
  font-size: var(--text-lg);
  font-weight: var(--font-medium);
  color: var(--gray-700);
}

.cache-badge {
  font-size: var(--text-xs);
  color: var(--gray-500);
  background-color: var(--gray-100);
  padding: var(--space-1) var(--space-2);
  border-radius: var(--radius-md);
}

/* Loading & Empty States */
.loading-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--space-16);
  color: var(--gray-500);
  gap: var(--space-4);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--gray-200);
  border-top-color: var(--primary-600);
  border-radius: var(--radius-full);
  animation: spin 1s linear infinite;
}

.empty-state svg {
  width: 64px;
  height: 64px;
  color: var(--gray-300);
}

.empty-state h3 {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--gray-700);
  margin: 0;
}

/* Overview Grid */
.overview-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-4);
}

.overview-card {
  background: white;
  border-radius: var(--radius-xl);
  padding: var(--space-5);
  text-align: center;
  box-shadow: var(--shadow-sm);
  border-top: 4px solid var(--gray-300);
}

.overview-card.total {
  border-top-color: var(--primary-500);
}

.overview-card.status-pending {
  border-top-color: var(--status-pending);
}

.overview-card.status-in_progress {
  border-top-color: var(--status-in-progress);
}

.overview-card.status-done {
  border-top-color: var(--status-done);
}

.overview-value {
  display: block;
  font-size: var(--text-3xl);
  font-weight: var(--font-bold);
  color: var(--gray-800);
  line-height: 1;
}

.overview-label {
  display: block;
  font-size: var(--text-sm);
  color: var(--gray-500);
  margin-top: var(--space-2);
}

/* Priority Grid */
.priority-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--space-4);
}

.priority-card {
  background: var(--color-bg-primary);
  border-radius: var(--radius-xl);
  padding: var(--space-5);
  box-shadow: var(--shadow-card);
  border: 1px solid var(--gray-200);
}

.priority-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: var(--space-4);
  border-bottom: 2px solid;
  margin-bottom: var(--space-4);
}

.priority-header h3 {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0;
  text-transform: capitalize;
}

.priority-total {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--gray-800);
}

.status-breakdown {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  margin-bottom: var(--space-4);
}

.status-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.status-info {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: var(--radius-full);
}

.status-name {
  font-size: var(--text-sm);
  color: var(--gray-600);
}

.status-count {
  font-size: var(--text-sm);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  min-width: 24px;
  text-align: center;
}

.status-count.zero {
  color: var(--gray-400);
}

/* Mini Chart */
.mini-chart {
  display: flex;
  height: 8px;
  border-radius: var(--radius-full);
  overflow: hidden;
  background-color: var(--gray-100);
}

.chart-bar {
  height: 100%;
  transition: width var(--duration-normal);
}

/* Details Card */
.details-card {
  background: var(--color-bg-primary);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  box-shadow: var(--shadow-card);
  border: 1px solid var(--gray-200);
}

.details-card h3 {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0 0 var(--space-5);
}

.details-table {
  width: 100%;
  border-collapse: collapse;
}

.details-table th,
.details-table td {
  padding: var(--space-3) var(--space-4);
  text-align: left;
  border-bottom: 1px solid var(--gray-200);
}

.details-table th {
  font-size: var(--text-xs);
  font-weight: var(--font-semibold);
  color: var(--gray-500);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.details-table td {
  font-size: var(--text-sm);
  color: var(--gray-700);
}

.priority-badge {
  display: inline-block;
  padding: var(--space-1) var(--space-3);
  border-radius: var(--radius-md);
  font-size: var(--text-xs);
  font-weight: var(--font-semibold);
  text-transform: capitalize;
}

.total-cell {
  font-weight: var(--font-semibold);
  color: var(--gray-900);
}

.totals-row td {
  border-top: 2px solid var(--gray-300);
  border-bottom: none;
  padding-top: var(--space-4);
}

/* Responsive */
@media (max-width: 1024px) {
  .overview-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .priority-grid {
    grid-template-columns: 1fr;
  }

  .page-header {
    flex-direction: column;
  }

  .header-actions {
    width: 100%;
    justify-content: space-between;
  }
}

@media (max-width: 640px) {
  .overview-grid {
    grid-template-columns: 1fr;
  }

  .details-table {
    font-size: var(--text-xs);
  }

  .details-table th,
  .details-table td {
    padding: var(--space-2);
  }
}
</style>

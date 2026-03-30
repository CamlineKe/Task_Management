<script setup>
import { computed } from "vue";
import { formatDate, getPriorityConfig, getStatusConfig } from "@/utils/formatters.js";

const props = defineProps({
  date: {
    type: String,
    required: true,
  },
  summary: {
    type: Object,
    required: true,
  },
});

const totalTasks = computed(() => {
  let total = 0;
  Object.values(props.summary).forEach((priority) => {
    Object.values(priority).forEach((count) => {
      total += count;
    });
  });
  return total;
});

const priorityTotals = computed(() => {
  const result = {};
  for (const [priority, statuses] of Object.entries(props.summary)) {
    result[priority] = Object.values(statuses).reduce((a, b) => a + b, 0);
  }
  return result;
});

const statusTotals = computed(() => {
  const result = { pending: 0, in_progress: 0, done: 0 };
  for (const statuses of Object.values(props.summary)) {
    result.pending += statuses.pending;
    result.in_progress += statuses.in_progress;
    result.done += statuses.done;
  }
  return result;
});

function getPriorityStyle(priority) {
  const config = getPriorityConfig(priority);
  return {
    backgroundColor: config.bgColor,
    color: config.color,
  };
}

function getStatusStyle(status) {
  const config = getStatusConfig(status);
  return {
    backgroundColor: config.bgColor,
    color: config.color,
  };
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
  <div class="report-summary">
    <!-- Header -->
    <div class="summary-header">
      <h3>Report for {{ formatDate(date) }}</h3>
      <span class="total-badge">{{ totalTasks }} tasks</span>
    </div>

    <!-- Priority Cards -->
    <div class="priority-cards">
      <div v-for="priority in ['high', 'medium', 'low']" :key="priority" class="priority-card">
        <div class="card-header" :style="getPriorityStyle(priority)">
          <span class="priority-name">{{
            priority.charAt(0).toUpperCase() + priority.slice(1)
          }}</span>
          <span class="priority-count">{{ priorityTotals[priority] }}</span>
        </div>

        <div class="status-list">
          <div v-for="(count, status) in summary[priority]" :key="status" class="status-item">
            <div class="status-info">
              <span
                class="status-dot"
                :style="{ backgroundColor: getStatusStyle(status).color }"
              ></span>
              <span class="status-name">{{ getStatusLabel(status) }}</span>
            </div>
            <span class="status-value" :class="{ zero: count === 0 }">{{ count }}</span>
          </div>
        </div>

        <!-- Mini bar chart -->
        <div class="progress-bar">
          <div
            v-for="(count, status) in summary[priority]"
            :key="status"
            class="progress-segment"
            :style="{
              width:
                priorityTotals[priority] > 0
                  ? `${(count / priorityTotals[priority]) * 100}%`
                  : '0%',
              backgroundColor: getStatusStyle(status).color,
            }"
            :title="`${getStatusLabel(status)}: ${count}`"
          ></div>
        </div>
      </div>
    </div>

    <!-- Status Overview -->
    <div class="status-overview">
      <h4>Status Overview</h4>
      <div class="overview-grid">
        <div
          v-for="(count, status) in statusTotals"
          :key="status"
          class="overview-item"
          :style="getStatusStyle(status)"
        >
          <span class="overview-count">{{ count }}</span>
          <span class="overview-label">{{ getStatusLabel(status) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.report-summary {
  background: white;
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
}

.summary-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--space-6);
  padding-bottom: var(--space-4);
  border-bottom: 1px solid var(--gray-200);
}

.summary-header h3 {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0;
}

.total-badge {
  background-color: var(--primary-100);
  color: var(--primary-700);
  padding: var(--space-1) var(--space-3);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
}

/* Priority Cards */
.priority-cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--space-4);
  margin-bottom: var(--space-6);
}

.priority-card {
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-3) var(--space-4);
  font-weight: var(--font-semibold);
  text-transform: capitalize;
}

.priority-name {
  font-size: var(--text-sm);
}

.priority-count {
  font-size: var(--text-lg);
}

.status-list {
  padding: var(--space-3) var(--space-4);
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.status-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: var(--text-sm);
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
  color: var(--gray-600);
}

.status-value {
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  min-width: 24px;
  text-align: right;
}

.status-value.zero {
  color: var(--gray-400);
}

.progress-bar {
  display: flex;
  height: 4px;
  background-color: var(--gray-100);
}

.progress-segment {
  height: 100%;
  transition: width var(--duration-normal);
}

/* Status Overview */
.status-overview {
  border-top: 1px solid var(--gray-200);
  padding-top: var(--space-5);
}

.status-overview h4 {
  font-size: var(--text-sm);
  font-weight: var(--font-semibold);
  color: var(--gray-700);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0 0 var(--space-4);
}

.overview-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--space-3);
}

.overview-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: var(--space-4);
  border-radius: var(--radius-lg);
  text-align: center;
}

.overview-count {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  line-height: 1;
}

.overview-label {
  font-size: var(--text-xs);
  font-weight: var(--font-medium);
  margin-top: var(--space-1);
  opacity: 0.9;
}

/* Responsive */
@media (max-width: 768px) {
  .priority-cards {
    grid-template-columns: 1fr;
  }

  .overview-grid {
    grid-template-columns: 1fr;
  }
}
</style>

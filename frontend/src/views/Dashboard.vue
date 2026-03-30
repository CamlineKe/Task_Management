<script setup>
import { onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useTaskStore, useReportStore, useUiStore } from "@/stores/index.js";
import { formatRelativeDate } from "@/utils/formatters.js";
import { STATUSES, PRIORITIES } from "@/utils/constants.js";
import CreateTaskModal from "@/components/modals/CreateTaskModal.vue";

const router = useRouter();
const taskStore = useTaskStore();
const reportStore = useReportStore();
const uiStore = useUiStore();

const today = new Date().toISOString().split("T")[0];

// Today's stats from report
const todayStats = computed(() => {
  return reportStore.summary;
});

const totalToday = computed(() => reportStore.totalTasksForDate);

// Recent tasks (last 5)
const recentTasks = computed(() => {
  return taskStore.allTasks.slice(0, 5);
});

// Quick stats
const quickStats = computed(() => [
  {
    label: "Total Tasks",
    value: taskStore.stats.total,
    color: "indigo",
    icon: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
    bgColor: "#eef2ff",
    iconColor: "#4f46e5",
  },
  {
    label: "Pending",
    value: taskStore.stats.pending,
    color: "amber",
    icon: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
    bgColor: "#fef3c7",
    iconColor: "#d97706",
  },
  {
    label: "In Progress",
    value: taskStore.stats.in_progress,
    color: "blue",
    icon: "M13 10V3L4 14h7v7l9-11h-7z",
    bgColor: "#dbeafe",
    iconColor: "#2563eb",
  },
  {
    label: "Completed",
    value: taskStore.stats.done,
    color: "emerald",
    icon: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
    bgColor: "#d1fae5",
    iconColor: "#059669",
  },
]);

onMounted(async () => {
  await Promise.all([taskStore.fetchTasks(), reportStore.fetchReport(today)]);
});

function navigateToTasks() {
  router.push("/tasks");
}

function navigateToReport() {
  router.push("/report");
}

function handleTaskCreated() {
  taskStore.fetchTasks();
  uiStore.closeModal("createTask");
}

function getPriorityColor(priority) {
  const colors = {
    high: "red",
    medium: "yellow",
    low: "green",
  };
  return colors[priority] || "gray";
}

function getStatusColor(status) {
  const colors = {
    pending: "gray",
    in_progress: "blue",
    done: "green",
  };
  return colors[status] || "gray";
}
</script>

<template>
  <div class="dashboard">
    <!-- Quick Stats Cards -->
    <div class="stats-grid">
      <div
        v-for="stat in quickStats"
        :key="stat.label"
        class="stat-card"
        :class="`stat-${stat.color}`"
      >
        <div class="stat-icon" :style="{ backgroundColor: stat.bgColor, color: stat.iconColor }">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-value">{{ stat.value }}</span>
          <span class="stat-label">{{ stat.label }}</span>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
      <!-- Today's Summary -->
      <div class="dashboard-card today-summary">
        <div class="card-header">
          <h2>Today's Summary</h2>
          <span class="date-badge">{{ formatRelativeDate(today) }}</span>
        </div>

        <div v-if="reportStore.loading" class="loading-state">Loading...</div>

        <div v-else-if="totalToday === 0" class="empty-state">
          <p>No tasks due today</p>
          <button class="btn-primary" @click="navigateToTasks">Create Task</button>
        </div>

        <div v-else class="priority-summary">
          <div v-for="priority in ['high', 'medium', 'low']" :key="priority" class="priority-row">
            <div class="priority-label" :class="`priority-${priority}`">
              {{ priority.charAt(0).toUpperCase() + priority.slice(1) }}
            </div>
            <div class="status-counts">
              <span class="count pending">{{ todayStats[priority].pending }}</span>
              <span class="count in-progress">{{ todayStats[priority].in_progress }}</span>
              <span class="count done">{{ todayStats[priority].done }}</span>
            </div>
          </div>
        </div>

        <button class="btn-link" @click="navigateToReport">View Full Report</button>
      </div>

      <!-- Create Task Modal -->
      <CreateTaskModal
        :is-open="uiStore.modals.createTask"
        @close="uiStore.closeModal('createTask')"
        @success="handleTaskCreated"
      />

      <!-- Recent Tasks -->
      <div class="dashboard-card recent-tasks">
        <div class="card-header">
          <h2>Recent Tasks</h2>
          <button class="btn-link" @click="navigateToTasks">View All</button>
        </div>

        <div v-if="taskStore.loading" class="loading-state">Loading...</div>

        <div v-else-if="recentTasks.length === 0" class="empty-state">
          <p>No tasks yet</p>
          <button class="btn-primary" @click="uiStore.openModal('createTask')">
            Create First Task
          </button>
        </div>

        <ul v-else class="task-list">
          <li v-for="task in recentTasks" :key="task.id" class="task-item">
            <div class="task-info">
              <span class="priority-dot" :class="`priority-${task.priority}`" />
              <span class="task-title">{{ task.title }}</span>
            </div>
            <span class="status-badge" :class="`status-${task.status}`">
              {{ task.status.replace("_", " ") }}
            </span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-4);
}

.stat-card {
  background: var(--color-bg-primary);
  border-radius: var(--radius-xl);
  padding: var(--space-5);
  display: flex;
  align-items: center;
  gap: var(--space-4);
  box-shadow: var(--shadow-card);
  border: 1px solid var(--gray-200);
  transition: all var(--duration-fast);
  cursor: pointer;
}

.stat-card:hover {
  box-shadow: var(--shadow-card-hover);
  transform: translateY(-2px);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: var(--radius-xl);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform var(--duration-fast);
}

.stat-card:hover .stat-icon {
  transform: scale(1.1);
}

.stat-icon svg {
  width: 28px;
  height: 28px;
}

.stat-content {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--gray-900);
  line-height: 1;
}

.stat-label {
  font-size: var(--text-sm);
  color: var(--gray-500);
  margin-top: var(--space-1);
  font-weight: var(--font-medium);
}

/* Dashboard Grid */
.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-6);
}

/* Cards */
.dashboard-card {
  background: var(--color-bg-primary);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  box-shadow: var(--shadow-card);
  border: 1px solid var(--gray-200);
  transition: box-shadow var(--duration-fast);
}

.dashboard-card:hover {
  box-shadow: var(--shadow-card-hover);
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--space-5);
}

.card-header h2 {
  font-size: var(--text-xl);
  font-weight: var(--font-bold);
  color: var(--gray-900);
  margin: 0;
}

.date-badge {
  background-color: var(--primary-100);
  color: var(--primary-700);
  padding: var(--space-1) var(--space-3);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
}

/* Today Summary */
.priority-summary {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  margin-bottom: var(--space-5);
}

.priority-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-3);
  background-color: var(--gray-50);
  border-radius: var(--radius-lg);
}

.priority-label {
  font-weight: var(--font-medium);
  font-size: var(--text-sm);
}

.priority-label.priority-high {
  color: var(--priority-high);
}
.priority-label.priority-medium {
  color: var(--priority-medium);
}
.priority-label.priority-low {
  color: var(--priority-low);
}

.status-counts {
  display: flex;
  gap: var(--space-3);
}

.count {
  min-width: 24px;
  height: 24px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--text-xs);
  font-weight: var(--font-semibold);
}

.count.pending {
  background-color: var(--status-pending-bg);
  color: var(--status-pending);
}

.count.in-progress {
  background-color: var(--status-in-progress-bg);
  color: var(--status-in-progress);
}

.count.done {
  background-color: var(--status-done-bg);
  color: var(--status-done);
}

/* Task List */
.task-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.task-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-3);
  background-color: var(--gray-50);
  border-radius: var(--radius-lg);
}

.task-info {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.priority-dot {
  width: 8px;
  height: 8px;
  border-radius: var(--radius-full);
}

.priority-dot.priority-high {
  background-color: var(--priority-high);
}
.priority-dot.priority-medium {
  background-color: var(--priority-medium);
}
.priority-dot.priority-low {
  background-color: var(--priority-low);
}

.task-title {
  font-size: var(--text-sm);
  color: var(--gray-700);
}

.status-badge {
  font-size: var(--text-xs);
  font-weight: var(--font-medium);
  padding: var(--space-1) var(--space-2);
  border-radius: var(--radius-md);
  text-transform: capitalize;
}

.status-badge.status-pending {
  background-color: var(--status-pending-bg);
  color: var(--status-pending);
}

.status-badge.status-in_progress {
  background-color: var(--status-in-progress-bg);
  color: var(--status-in-progress);
}

.status-badge.status-done {
  background-color: var(--status-done-bg);
  color: var(--status-done);
}

/* Buttons */
.btn-primary {
  background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
  color: var(--gray-100);
  border: none;
  padding: var(--space-2) var(--space-4);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  font-weight: var(--font-semibold);
  cursor: pointer;
  transition: all var(--duration-fast);
  box-shadow: 0 1px 2px 0 rgb(37 99 235 / 0.3);
}

.btn-primary:hover {
  background: linear-gradient(135deg, var(--primary-700) 0%, var(--primary-800) 100%);
  box-shadow: 0 4px 6px -1px rgb(37 99 235 / 0.4);
  transform: translateY(-1px);
}

.btn-primary:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px 0 rgb(37 99 235 / 0.3);
}

.btn-link {
  background: none;
  border: none;
  color: var(--primary-600);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  padding: 0;
}

.btn-link:hover {
  color: var(--primary-700);
  text-decoration: underline;
}

/* States */
.loading-state,
.empty-state {
  text-align: center;
  padding: var(--space-8);
  color: var(--gray-500);
}

.empty-state p {
  margin-bottom: var(--space-4);
}

/* Responsive */
@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>

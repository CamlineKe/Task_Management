<script setup>
import { computed } from "vue";
import { formatDate, formatPriority, formatStatus } from "@/utils/formatters.js";
import { getPriorityConfig, getStatusConfig } from "@/utils/formatters.js";
import { STATUS_TRANSITIONS } from "@/utils/constants.js";

const props = defineProps({
  tasks: {
    type: Array,
    required: true,
  },
});

const emit = defineEmits(["update-status", "delete"]);

const hasTasks = computed(() => props.tasks.length > 0);

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

function canDelete(status) {
  return status === "done";
}

function getAllowedNextStatuses(currentStatus) {
  return STATUS_TRANSITIONS[currentStatus] || [];
}

function hasTransitions(currentStatus) {
  return getAllowedNextStatuses(currentStatus).length > 0;
}
</script>

<template>
  <div class="task-table-container">
    <table v-if="hasTasks" class="task-table">
      <thead>
        <tr>
          <th>Task</th>
          <th>Due Date</th>
          <th>Priority</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="task in tasks" :key="task.id">
          <!-- Title -->
          <td class="title-cell">
            <span class="task-title">{{ task.title }}</span>
          </td>

          <!-- Due Date -->
          <td class="date-cell">
            {{ formatDate(task.due_date) }}
          </td>

          <!-- Priority -->
          <td class="priority-cell">
            <span class="badge" :style="getPriorityStyle(task.priority)">
              {{ formatPriority(task.priority) }}
            </span>
          </td>

          <!-- Status -->
          <td class="status-cell">
            <span class="badge" :style="getStatusStyle(task.status)">
              {{ formatStatus(task.status) }}
            </span>
          </td>

          <!-- Actions -->
          <td class="actions-cell">
            <!-- Update Status Button -->
            <button
              v-if="hasTransitions(task.status)"
              class="action-btn update-btn"
              @click="emit('update-status', task)"
              title="Update Status"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                />
              </svg>
            </button>

            <!-- Delete Button -->
            <button
              class="action-btn delete-btn"
              :class="{ disabled: !canDelete(task.status) }"
              :disabled="!canDelete(task.status)"
              @click="canDelete(task.status) && emit('delete', task)"
              :title="canDelete(task.status) ? 'Delete' : 'Only completed tasks can be deleted'"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                />
              </svg>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Empty State -->
    <div v-else class="table-empty">
      <p>No tasks match the current filter</p>
    </div>
  </div>
</template>

<style scoped>
.task-table-container {
  background: var(--color-bg-primary);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-card);
  border: 1px solid var(--gray-200);
  overflow: hidden;
  transition: box-shadow var(--duration-fast);
}

.task-table {
  width: 100%;
  border-collapse: collapse;
}

.task-table th {
  background-color: var(--gray-50);
  padding: var(--space-4) var(--space-5);
  text-align: left;
  font-size: var(--text-xs);
  font-weight: var(--font-bold);
  color: var(--gray-600);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  border-bottom: 2px solid var(--gray-200);
}

.task-table td {
  padding: var(--space-4) var(--space-5);
  border-bottom: 1px solid var(--gray-100);
  font-size: var(--text-sm);
  color: var(--gray-700);
  transition: background-color var(--duration-fast);
}

.task-table tr:last-child td {
  border-bottom: none;
}

.task-table tr:hover td {
  background-color: var(--primary-50);
}

.task-table tr {
  transition: transform var(--duration-fast);
}

/* Cells */
.title-cell {
  max-width: 300px;
}

.task-title {
  font-weight: var(--font-semibold);
  color: var(--gray-900);
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.date-cell {
  white-space: nowrap;
  color: var(--gray-500);
}

/* Badges */
.badge {
  display: inline-block;
  padding: var(--space-1) var(--space-3);
  border-radius: var(--radius-md);
  font-size: var(--text-xs);
  font-weight: var(--font-semibold);
  text-transform: capitalize;
}

/* Actions */
.actions-cell {
  display: flex;
  gap: var(--space-2);
}

.action-btn {
  width: 36px;
  height: 36px;
  border-radius: var(--radius-lg);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all var(--duration-fast);
  box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}

.action-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}

.action-btn:active {
  transform: translateY(0);
}

.action-btn svg {
  width: 18px;
  height: 18px;
  stroke-width: 2.5px;
}

.update-btn {
  background-color: var(--primary-100);
  color: var(--primary-600);
}

.update-btn:hover {
  background-color: var(--primary-200);
}

.delete-btn {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
}

.delete-btn:hover:not(:disabled) {
  background-color: #fecaca;
}

.delete-btn.disabled {
  background-color: var(--gray-100);
  color: var(--gray-400);
  cursor: not-allowed;
}

/* Empty State */
.table-empty {
  padding: var(--space-12);
  text-align: center;
  color: var(--gray-500);
}

/* Dark Theme */
[data-theme="dark"] .task-table tr:hover td {
  background-color: var(--gray-300);
}

[data-theme="dark"] .update-btn {
  background-color: var(--primary-900);
  color: var(--primary-400);
}

[data-theme="dark"] .update-btn:hover {
  background-color: var(--primary-800);
}

[data-theme="dark"] .delete-btn {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
}

[data-theme="dark"] .delete-btn:hover:not(:disabled) {
  background-color: #7f1d1d;
}

[data-theme="dark"] .delete-btn.disabled {
  background-color: var(--gray-800);
  color: var(--gray-500);
}

/* Responsive */
@media (max-width: 768px) {
  .task-table {
    display: block;
    overflow-x: auto;
  }

  .task-table th,
  .task-table td {
    padding: var(--space-3);
  }

  .title-cell {
    max-width: 150px;
  }
}
</style>

<script setup>
import {
  formatDate,
  formatPriority,
  formatStatus,
  getPriorityConfig,
  getStatusConfig,
} from "@/utils/formatters.js";
import { STATUS_TRANSITIONS } from "@/utils/constants.js";

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["update-status", "delete"]);

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

function hasTransitions(currentStatus) {
  return (STATUS_TRANSITIONS[currentStatus] || []).length > 0;
}
</script>

<template>
  <div class="task-card">
    <!-- Header: Title and Priority -->
    <div class="card-header">
      <h3 class="task-title">{{ task.title }}</h3>
      <span class="badge priority-badge" :style="getPriorityStyle(task.priority)">
        {{ formatPriority(task.priority) }}
      </span>
    </div>

    <!-- Body: Due Date and Status -->
    <div class="card-body">
      <div class="info-row">
        <span class="info-label">Due:</span>
        <span class="info-value">{{ formatDate(task.due_date) }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Status:</span>
        <span class="badge status-badge" :style="getStatusStyle(task.status)">
          {{ formatStatus(task.status) }}
        </span>
      </div>
    </div>

    <!-- Footer: Actions -->
    <div class="card-footer">
      <button
        v-if="hasTransitions(task.status)"
        class="btn-action btn-update"
        @click="emit('update-status', task)"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
          />
        </svg>
        Update Status
      </button>

      <button
        class="btn-action btn-delete"
        :class="{ disabled: !canDelete(task.status) }"
        :disabled="!canDelete(task.status)"
        @click="canDelete(task.status) && emit('delete', task)"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
          />
        </svg>
        Delete
      </button>
    </div>
  </div>
</template>

<style scoped>
.task-card {
  background: white;
  border-radius: var(--radius-xl);
  padding: var(--space-4);
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.card-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: var(--space-3);
}

.task-title {
  font-size: var(--text-base);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0;
  flex: 1;
  line-height: 1.4;
}

.badge {
  display: inline-block;
  padding: var(--space-1) var(--space-2);
  border-radius: var(--radius-md);
  font-size: var(--text-xs);
  font-weight: var(--font-semibold);
  text-transform: capitalize;
  flex-shrink: 0;
}

.card-body {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.info-row {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.info-label {
  font-size: var(--text-sm);
  color: var(--gray-500);
  min-width: 40px;
}

.info-value {
  font-size: var(--text-sm);
  color: var(--gray-700);
}

.card-footer {
  display: flex;
  gap: var(--space-2);
  padding-top: var(--space-3);
  border-top: 1px solid var(--gray-100);
}

.btn-action {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  border: none;
  cursor: pointer;
  transition: all var(--duration-fast);
}

.btn-action svg {
  width: 16px;
  height: 16px;
}

.btn-update {
  background-color: var(--primary-100);
  color: var(--primary-600);
}

.btn-update:hover {
  background-color: var(--primary-200);
}

.btn-delete {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
}

.btn-delete:hover:not(:disabled) {
  background-color: #fecaca;
}

.btn-delete.disabled {
  background-color: var(--gray-100);
  color: var(--gray-400);
  cursor: not-allowed;
}
</style>

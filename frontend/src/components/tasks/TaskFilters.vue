<script setup>
import { STATUSES } from "@/utils/constants.js";

const props = defineProps({
  currentFilter: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["filter-change"]);

const filters = [
  { value: null, label: "All Tasks" },
  { value: "pending", label: "Pending" },
  { value: "in_progress", label: "In Progress" },
  { value: "done", label: "Done" },
];

function isActive(value) {
  return props.currentFilter === value;
}

function clearFilter() {
  emit("filter-change", null);
}
</script>

<template>
  <div class="task-filters">
    <div class="filter-group">
      <button
        v-for="filter in filters"
        :key="filter.value ?? 'all'"
        class="filter-btn"
        :class="{ active: isActive(filter.value) }"
        @click="emit('filter-change', filter.value)"
      >
        {{ filter.label }}
      </button>
    </div>

    <button v-if="currentFilter" class="clear-btn" @click="clearFilter">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
      Clear
    </button>
  </div>
</template>

<style scoped>
.task-filters {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-4);
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  gap: var(--space-2);
  flex-wrap: wrap;
}

.filter-btn {
  padding: var(--space-2) var(--space-4);
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-300);
  background-color: var(--color-bg-primary);
  color: var(--gray-600);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--duration-fast);
}

.filter-btn:hover {
  border-color: var(--gray-400);
  color: var(--gray-800);
}

.filter-btn.active {
  background-color: var(--primary-600);
  border-color: var(--primary-600);
  color: var(--gray-100);
}

.clear-btn {
  display: flex;
  align-items: center;
  gap: var(--space-1);
  padding: var(--space-2) var(--space-3);
  border-radius: var(--radius-lg);
  border: none;
  background-color: var(--gray-100);
  color: var(--gray-600);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--duration-fast);
}

.clear-btn:hover {
  background-color: var(--gray-200);
  color: var(--gray-800);
}

.clear-btn svg {
  width: 16px;
  height: 16px;
}

/* Responsive */
@media (max-width: 640px) {
  .task-filters {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-group {
    justify-content: center;
  }

  .filter-btn {
    flex: 1;
    text-align: center;
  }
}
</style>

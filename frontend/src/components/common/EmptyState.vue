<script setup>
const props = defineProps({
  title: {
    type: String,
    default: "No items found",
  },
  description: {
    type: String,
    default: "",
  },
  icon: {
    type: String,
    default: "inbox",
  },
  actionLabel: {
    type: String,
    default: "",
  },
});

const emit = defineEmits(["action"]);

const icons = {
  inbox:
    "M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4",
  search: "M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z",
  task: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
  folder: "M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z",
};
</script>

<template>
  <div class="empty-state">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.5"
          :d="icons[icon] || icons.inbox"
        />
      </svg>
    </div>

    <h3 class="empty-title">{{ title }}</h3>

    <p v-if="description" class="empty-description">
      {{ description }}
    </p>

    <button v-if="actionLabel" class="btn-primary" @click="emit('action')">
      {{ actionLabel }}
    </button>
  </div>
</template>

<style scoped>
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--space-12) var(--space-6);
  text-align: center;
}

.empty-icon {
  width: 80px;
  height: 80px;
  border-radius: var(--radius-xl);
  background-color: var(--gray-100);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: var(--space-5);
  color: var(--gray-400);
}

.empty-icon svg {
  width: 40px;
  height: 40px;
}

.empty-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0 0 var(--space-2);
}

.empty-description {
  font-size: var(--text-base);
  color: var(--gray-500);
  margin: 0 0 var(--space-6);
  max-width: 400px;
  line-height: 1.5;
}

.btn-primary {
  padding: var(--space-3) var(--space-5);
  border-radius: var(--radius-lg);
  border: none;
  background-color: var(--primary-600);
  color: white;
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: background-color var(--duration-fast);
}

.btn-primary:hover {
  background-color: var(--primary-700);
}
</style>

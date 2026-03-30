<script setup>
const props = defineProps({
  type: {
    type: String,
    default: "text",
    validator: (value) => ["text", "card", "table", "circle"].includes(value),
  },
  lines: {
    type: Number,
    default: 3,
  },
  rows: {
    type: Number,
    default: 4,
  },
});
</script>

<template>
  <!-- Text Lines Skeleton -->
  <div v-if="type === 'text'" class="skeleton-text">
    <div
      v-for="i in lines"
      :key="i"
      class="skeleton-line"
      :style="{ width: i === lines ? '60%' : '100%' }"
    />
  </div>

  <!-- Card Skeleton -->
  <div v-else-if="type === 'card'" class="skeleton-card">
    <div class="skeleton-header">
      <div class="skeleton-circle" style="width: 40px; height: 40px" />
      <div class="skeleton-line" style="width: 60%" />
    </div>
    <div class="skeleton-body">
      <div v-for="i in 3" :key="i" class="skeleton-line" />
    </div>
  </div>

  <!-- Table Skeleton -->
  <div v-else-if="type === 'table'" class="skeleton-table">
    <div class="skeleton-row header">
      <div v-for="i in 4" :key="i" class="skeleton-cell" />
    </div>
    <div v-for="r in rows" :key="r" class="skeleton-row">
      <div v-for="c in 4" :key="c" class="skeleton-cell" />
    </div>
  </div>

  <!-- Circle Skeleton -->
  <div v-else-if="type === 'circle'" class="skeleton-circle" />
</template>

<style scoped>
.skeleton-text,
.skeleton-card,
.skeleton-table {
  width: 100%;
}

/* Base shimmer animation */
.skeleton-line,
.skeleton-cell,
.skeleton-circle {
  background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-100) 50%, var(--gray-200) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: var(--radius-md);
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Text Lines */
.skeleton-text {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.skeleton-line {
  height: 16px;
}

/* Card */
.skeleton-card {
  background: white;
  border-radius: var(--radius-xl);
  padding: var(--space-5);
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.skeleton-header {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.skeleton-body {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

/* Circle */
.skeleton-circle {
  border-radius: 50%;
  flex-shrink: 0;
}

/* Table */
.skeleton-table {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.skeleton-row {
  display: flex;
  gap: var(--space-3);
  padding: var(--space-3);
  background: white;
  border-radius: var(--radius-lg);
}

.skeleton-row.header .skeleton-cell {
  height: 12px;
  background: var(--gray-300);
}

.skeleton-cell {
  flex: 1;
  height: 16px;
}
</style>

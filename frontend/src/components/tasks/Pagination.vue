<script setup>
const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  totalPages: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(["page-change"]);

function goToPage(page) {
  if (page >= 1 && page <= props.totalPages) {
    emit("page-change", page);
  }
}

function previousPage() {
  goToPage(props.currentPage - 1);
}

function nextPage() {
  goToPage(props.currentPage + 1);
}

function generatePageRange() {
  const pages = [];
  const delta = 2;

  for (
    let i = Math.max(2, props.currentPage - delta);
    i <= Math.min(props.totalPages - 1, props.currentPage + delta);
    i++
  ) {
    pages.push(i);
  }

  if (props.currentPage - delta > 2) {
    pages.unshift("...");
  }

  if (props.currentPage + delta < props.totalPages - 1) {
    pages.push("...");
  }

  pages.unshift(1);

  if (props.totalPages > 1) {
    pages.push(props.totalPages);
  }

  return pages;
}

const pageRange = generatePageRange();
</script>

<template>
  <div v-if="totalPages > 1" class="pagination">
    <!-- Previous Button -->
    <button class="page-btn nav-btn" :disabled="currentPage === 1" @click="previousPage">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <!-- Page Numbers -->
    <template v-for="(page, index) in pageRange" :key="index">
      <span v-if="page === '...'" class="ellipsis"> ... </span>
      <button
        v-else
        class="page-btn"
        :class="{ active: currentPage === page }"
        @click="goToPage(page)"
      >
        {{ page }}
      </button>
    </template>

    <!-- Next Button -->
    <button class="page-btn nav-btn" :disabled="currentPage === totalPages" @click="nextPage">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </div>
</template>

<style scoped>
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  padding: var(--space-4);
}

.page-btn {
  min-width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 var(--space-3);
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-300);
  background-color: var(--color-bg-primary);
  color: var(--gray-600);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--duration-fast);
}

.page-btn:hover:not(:disabled) {
  border-color: var(--gray-400);
  color: var(--gray-800);
}

.page-btn.active {
  background-color: var(--primary-600);
  border-color: var(--primary-600);
  color: var(--gray-100);
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.nav-btn svg {
  width: 20px;
  height: 20px;
}

.ellipsis {
  color: var(--gray-400);
  font-size: var(--text-sm);
  padding: 0 var(--space-2);
}

/* Responsive */
@media (max-width: 640px) {
  .pagination {
    gap: var(--space-1);
  }

  .page-btn {
    min-width: 36px;
    height: 36px;
    padding: 0 var(--space-2);
    font-size: var(--text-xs);
  }
}
</style>

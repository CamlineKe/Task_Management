<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useUiStore } from "@/stores/index.js";

const uiStore = useUiStore();
const route = useRoute();

const pageTitle = computed(() => {
  return route.meta?.title || "Task Manager";
});

function toggleTheme() {
  uiStore.toggleTheme();
}
</script>

<template>
  <header class="app-header">
    <!-- Left: Page Title -->
    <div class="header-left">
      <h1 class="page-title">{{ pageTitle }}</h1>
    </div>

    <!-- Right: Actions -->
    <div class="header-right">
      <!-- Theme Toggle -->
      <button
        class="header-btn"
        @click="toggleTheme"
        :title="uiStore.isDarkMode ? 'Light Mode' : 'Dark Mode'"
      >
        <svg v-if="uiStore.isDarkMode" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
          />
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
          />
        </svg>
      </button>

      <!-- Mobile Menu Toggle -->
      <button class="header-btn mobile-only" @click="uiStore.toggleMobileSidebar" title="Menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6h16M4 12h16M4 18h16"
          />
        </svg>
      </button>
    </div>
  </header>
</template>

<style scoped>
.app-header {
  height: var(--header-height);
  background-color: var(--color-bg-primary);
  border-bottom: 1px solid var(--gray-200);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 var(--space-6);
  position: sticky;
  top: 0;
  z-index: var(--z-sticky);
}

.page-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0;
}

.header-right {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.header-btn {
  background: none;
  border: none;
  padding: var(--space-2);
  border-radius: var(--radius-lg);
  color: var(--gray-500);
  cursor: pointer;
  transition: all var(--duration-fast);
  display: flex;
  align-items: center;
  justify-content: center;
}

.header-btn:hover {
  background-color: var(--gray-100);
  color: var(--gray-700);
}

.header-btn svg {
  width: 24px;
  height: 24px;
}

.mobile-only {
  display: none;
}

/* Responsive */
@media (max-width: 768px) {
  .app-header {
    padding: 0 var(--space-4);
  }

  .mobile-only {
    display: flex;
  }

  .page-title {
    font-size: var(--text-lg);
  }
}
</style>

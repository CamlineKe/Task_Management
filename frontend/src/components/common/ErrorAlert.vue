<script setup>
const props = defineProps({
  title: {
    type: String,
    default: "Error",
  },
  message: {
    type: String,
    required: true,
  },
  retryable: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["retry", "dismiss"]);
</script>

<template>
  <div class="error-alert" role="alert">
    <div class="error-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
    </div>

    <div class="error-content">
      <h4 class="error-title">{{ title }}</h4>
      <p class="error-message">{{ message }}</p>

      <div class="error-actions">
        <button v-if="retryable" class="btn-retry" @click="emit('retry')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          Try Again
        </button>
      </div>
    </div>

    <button class="error-dismiss" @click="emit('dismiss')" aria-label="Dismiss error">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>
  </div>
</template>

<style scoped>
.error-alert {
  display: flex;
  align-items: flex-start;
  gap: var(--space-4);
  padding: var(--space-5);
  background-color: var(--color-danger-light);
  border: 1px solid #fecaca;
  border-radius: var(--radius-lg);
  position: relative;
}

.error-icon {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  border-radius: var(--radius-lg);
  background-color: var(--color-danger);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.error-icon svg {
  width: 24px;
  height: 24px;
}

.error-content {
  flex: 1;
  min-width: 0;
}

.error-title {
  font-size: var(--text-base);
  font-weight: var(--font-semibold);
  color: #991b1b;
  margin: 0 0 var(--space-1);
}

.error-message {
  font-size: var(--text-sm);
  color: #b91c1c;
  margin: 0;
  line-height: 1.5;
}

.error-actions {
  margin-top: var(--space-3);
}

.btn-retry {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  border-radius: var(--radius-md);
  border: 1px solid #f87171;
  background-color: white;
  color: #dc2626;
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--duration-fast);
}

.btn-retry:hover {
  background-color: #fef2f2;
  border-color: #ef4444;
}

.btn-retry svg {
  width: 16px;
  height: 16px;
}

.error-dismiss {
  flex-shrink: 0;
  background: none;
  border: none;
  padding: var(--space-1);
  border-radius: var(--radius-md);
  cursor: pointer;
  color: #f87171;
  transition: all var(--duration-fast);
}

.error-dismiss:hover {
  background-color: #fecaca;
  color: #dc2626;
}

.error-dismiss svg {
  width: 20px;
  height: 20px;
}
</style>

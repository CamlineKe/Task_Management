<script setup>
const props = defineProps({
  type: {
    type: String,
    default: "info",
    validator: (value) => ["success", "error", "warning", "info"].includes(value),
  },
  message: {
    type: String,
    required: true,
  },
  progress: {
    type: Number,
    default: 100,
  },
});

const emit = defineEmits(["close"]);

const icons = {
  success: "M5 13l4 4L19 7",
  error: "M6 18L18 6M6 6l12 12",
  warning:
    "M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z",
  info: "M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
};

const colors = {
  success: {
    bg: "var(--color-success-light)",
    border: "var(--color-success)",
    icon: "var(--color-success)",
  },
  error: {
    bg: "var(--color-danger-light)",
    border: "var(--color-danger)",
    icon: "var(--color-danger)",
  },
  warning: {
    bg: "var(--color-warning-light)",
    border: "var(--color-warning)",
    icon: "var(--color-warning)",
  },
  info: {
    bg: "var(--color-info-light)",
    border: "var(--color-info)",
    icon: "var(--color-info)",
  },
};

const color = colors[props.type];
</script>

<template>
  <div
    class="toast"
    :style="{
      backgroundColor: color.bg,
      borderLeftColor: color.border,
    }"
    role="alert"
  >
    <div class="toast-icon" :style="{ color: color.icon }">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons[type]" />
      </svg>
    </div>

    <p class="toast-message">{{ message }}</p>

    <button class="toast-close" @click="emit('close')" aria-label="Close notification">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>

    <div
      class="toast-progress"
      :style="{
        width: `${progress}%`,
        backgroundColor: color.border,
      }"
    />
  </div>
</template>

<style scoped>
.toast {
  position: relative;
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-4);
  border-radius: var(--radius-lg);
  border-left: 4px solid;
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  min-width: 300px;
  max-width: 400px;
}

.toast-icon {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
}

.toast-message {
  flex: 1;
  margin: 0;
  font-size: var(--text-sm);
  color: var(--gray-800);
  line-height: 1.5;
}

.toast-close {
  flex-shrink: 0;
  background: none;
  border: none;
  padding: var(--space-1);
  border-radius: var(--radius-md);
  cursor: pointer;
  color: var(--gray-400);
  transition: all var(--duration-fast);
}

.toast-close:hover {
  background-color: rgba(0, 0, 0, 0.05);
  color: var(--gray-600);
}

.toast-close svg {
  width: 18px;
  height: 18px;
}

.toast-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  opacity: 0.3;
  transition: width linear 100ms;
}
</style>

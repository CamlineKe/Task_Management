<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: "primary",
    validator: (value) => ["primary", "secondary", "danger", "ghost", "link"].includes(value),
  },
  size: {
    type: String,
    default: "md",
    validator: (value) => ["sm", "md", "lg"].includes(value),
  },
  loading: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  block: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String,
    default: "button",
  },
});

const emit = defineEmits(["click"]);

const variantClasses = {
  primary: "btn-primary",
  secondary: "btn-secondary",
  danger: "btn-danger",
  ghost: "btn-ghost",
  link: "btn-link",
};

const sizeClasses = {
  sm: "btn-sm",
  md: "btn-md",
  lg: "btn-lg",
};

function handleClick(event) {
  if (!props.loading && !props.disabled) {
    emit("click", event);
  }
}
</script>

<template>
  <button
    :type="type"
    class="btn"
    :class="[
      variantClasses[variant],
      sizeClasses[size],
      { 'btn-block': block, 'btn-loading': loading },
    ]"
    :disabled="disabled || loading"
    @click="handleClick"
  >
    <span v-if="loading" class="btn-spinner"></span>
    <span v-else class="btn-content">
      <slot />
    </span>
  </button>
</template>

<style scoped>
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  font-weight: var(--font-medium);
  border-radius: var(--radius-lg);
  cursor: pointer;
  transition: all var(--duration-fast);
  border: none;
  white-space: nowrap;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Sizes */
.btn-sm {
  padding: var(--space-2) var(--space-3);
  font-size: var(--text-xs);
}

.btn-md {
  padding: var(--space-3) var(--space-4);
  font-size: var(--text-sm);
}

.btn-lg {
  padding: var(--space-4) var(--space-6);
  font-size: var(--text-base);
}

/* Block */
.btn-block {
  width: 100%;
}

/* Variants */
.btn-primary {
  background-color: var(--primary-600);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: var(--primary-700);
}

.btn-secondary {
  background-color: white;
  color: var(--gray-700);
  border: 1px solid var(--gray-300);
}

.btn-secondary:hover:not(:disabled) {
  background-color: var(--gray-50);
  border-color: var(--gray-400);
}

.btn-danger {
  background-color: var(--color-danger);
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background-color: #dc2626;
}

.btn-ghost {
  background-color: transparent;
  color: var(--gray-600);
}

.btn-ghost:hover:not(:disabled) {
  background-color: var(--gray-100);
  color: var(--gray-800);
}

.btn-link {
  background-color: transparent;
  color: var(--primary-600);
  padding: 0;
}

.btn-link:hover:not(:disabled) {
  color: var(--primary-700);
  text-decoration: underline;
}

/* Loading */
.btn-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: currentColor;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.btn-content {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}
</style>

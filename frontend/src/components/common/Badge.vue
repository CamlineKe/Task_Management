<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: "default",
    validator: (value) =>
      ["default", "primary", "success", "warning", "danger", "info"].includes(value),
  },
  size: {
    type: String,
    default: "md",
    validator: (value) => ["sm", "md", "lg"].includes(value),
  },
  rounded: {
    type: Boolean,
    default: false,
  },
});

const variantClasses = {
  default: "badge-default",
  primary: "badge-primary",
  success: "badge-success",
  warning: "badge-warning",
  danger: "badge-danger",
  info: "badge-info",
};

const sizeClasses = {
  sm: "badge-sm",
  md: "badge-md",
  lg: "badge-lg",
};
</script>

<template>
  <span
    class="badge"
    :class="[variantClasses[variant], sizeClasses[size], { 'badge-rounded': rounded }]"
  >
    <slot />
  </span>
</template>

<style scoped>
.badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: var(--font-medium);
  line-height: 1;
  white-space: nowrap;
  text-transform: capitalize;
}

/* Sizes */
.badge-sm {
  padding: var(--space-1) var(--space-2);
  font-size: var(--text-xs);
  border-radius: var(--radius-md);
}

.badge-md {
  padding: var(--space-1) var(--space-3);
  font-size: var(--text-sm);
  border-radius: var(--radius-md);
}

.badge-lg {
  padding: var(--space-2) var(--space-4);
  font-size: var(--text-base);
  border-radius: var(--radius-lg);
}

/* Rounded variant */
.badge-rounded {
  border-radius: var(--radius-full);
}

/* Variants */
.badge-default {
  background-color: var(--gray-100);
  color: var(--gray-700);
}

.badge-primary {
  background-color: var(--primary-100);
  color: var(--primary-700);
}

.badge-success {
  background-color: var(--color-success-light);
  color: var(--color-success);
}

.badge-warning {
  background-color: var(--color-warning-light);
  color: var(--color-warning);
}

.badge-danger {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
}

.badge-info {
  background-color: var(--color-info-light);
  color: var(--color-info);
}
</style>

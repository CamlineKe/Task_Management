<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: "",
  },
  options: {
    type: Array,
    required: true,
    // Array of { value: any, label: string }
  },
  label: {
    type: String,
    default: "",
  },
  placeholder: {
    type: String,
    default: "Select an option",
  },
  error: {
    type: String,
    default: "",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
  id: {
    type: String,
    default: "",
  },
});

const emit = defineEmits(["update:modelValue", "change"]);

function onChange(event) {
  emit("update:modelValue", event.target.value);
  emit("change", event.target.value);
}
</script>

<template>
  <div class="select-wrapper">
    <label v-if="label" :for="id" class="select-label">
      {{ label }}
      <span v-if="required" class="required">*</span>
    </label>

    <div class="select-container">
      <select
        :id="id"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        class="select"
        :class="{ 'select-error': error }"
        @change="onChange"
      >
        <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
        <option v-for="option in options" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>

      <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>

    <span v-if="error" class="error-message">{{ error }}</span>
  </div>
</template>

<style scoped>
.select-wrapper {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.select-label {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--gray-700);
}

.required {
  color: var(--color-danger);
  margin-left: var(--space-1);
}

.select-container {
  position: relative;
}

.select {
  width: 100%;
  padding: var(--space-3) var(--space-10) var(--space-3) var(--space-3);
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  color: var(--gray-800);
  background-color: white;
  cursor: pointer;
  appearance: none;
  transition:
    border-color var(--duration-fast),
    box-shadow var(--duration-fast);
}

.select:focus {
  outline: none;
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.select:disabled {
  background-color: var(--gray-100);
  color: var(--gray-500);
  cursor: not-allowed;
}

.select-error {
  border-color: var(--color-danger);
}

.select-error:focus {
  border-color: var(--color-danger);
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.select-arrow {
  position: absolute;
  right: var(--space-3);
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: var(--gray-500);
  pointer-events: none;
}

.error-message {
  font-size: var(--text-xs);
  color: var(--color-danger);
}
</style>

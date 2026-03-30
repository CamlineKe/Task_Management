<script setup>
const props = defineProps({
  modelValue: {
    type: String,
    default: "",
  },
  label: {
    type: String,
    default: "",
  },
  min: {
    type: String,
    default: "",
  },
  max: {
    type: String,
    default: "",
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
  <div class="datepicker-wrapper">
    <label v-if="label" :for="id" class="datepicker-label">
      {{ label }}
      <span v-if="required" class="required">*</span>
    </label>

    <div class="datepicker-container">
      <input
        :id="id"
        type="date"
        :value="modelValue"
        :min="min"
        :max="max"
        :disabled="disabled"
        :required="required"
        class="datepicker-input"
        :class="{ 'datepicker-error': error }"
        @change="onChange"
      />

      <svg class="datepicker-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
        />
      </svg>
    </div>

    <span v-if="error" class="error-message">{{ error }}</span>
  </div>
</template>

<style scoped>
.datepicker-wrapper {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.datepicker-label {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--gray-700);
}

.required {
  color: var(--color-danger);
  margin-left: var(--space-1);
}

.datepicker-container {
  position: relative;
}

.datepicker-input {
  width: 100%;
  padding: var(--space-3) var(--space-10) var(--space-3) var(--space-3);
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  color: var(--gray-800);
  background-color: white;
  cursor: pointer;
  transition:
    border-color var(--duration-fast),
    box-shadow var(--duration-fast);
}

.datepicker-input:focus {
  outline: none;
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.datepicker-input:disabled {
  background-color: var(--gray-100);
  color: var(--gray-500);
  cursor: not-allowed;
}

.datepicker-error {
  border-color: var(--color-danger);
}

.datepicker-error:focus {
  border-color: var(--color-danger);
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.datepicker-icon {
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

/* Custom date picker icon styling for webkit browsers */
.datepicker-input::-webkit-calendar-picker-indicator {
  position: absolute;
  right: var(--space-3);
  width: 20px;
  height: 20px;
  opacity: 0;
  cursor: pointer;
}
</style>

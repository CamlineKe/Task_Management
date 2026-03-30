<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: "",
  },
  type: {
    type: String,
    default: "text",
  },
  label: {
    type: String,
    default: "",
  },
  placeholder: {
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

const emit = defineEmits(["update:modelValue", "blur", "focus"]);

function onInput(event) {
  emit("update:modelValue", event.target.value);
}

function onBlur(event) {
  emit("blur", event);
}

function onFocus(event) {
  emit("focus", event);
}
</script>

<template>
  <div class="input-wrapper">
    <label v-if="label" :for="id" class="input-label">
      {{ label }}
      <span v-if="required" class="required">*</span>
    </label>

    <input
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="required"
      class="input"
      :class="{ 'input-error': error }"
      @input="onInput"
      @blur="onBlur"
      @focus="onFocus"
    />

    <span v-if="error" class="error-message">{{ error }}</span>
  </div>
</template>

<style scoped>
.input-wrapper {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.input-label {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--gray-700);
}

.required {
  color: var(--color-danger);
  margin-left: var(--space-1);
}

.input {
  padding: var(--space-3);
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  color: var(--gray-800);
  background-color: white;
  transition:
    border-color var(--duration-fast),
    box-shadow var(--duration-fast);
  width: 100%;
}

.input:focus {
  outline: none;
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input:disabled {
  background-color: var(--gray-100);
  color: var(--gray-500);
  cursor: not-allowed;
}

.input::placeholder {
  color: var(--gray-400);
}

.input-error {
  border-color: var(--color-danger);
}

.input-error:focus {
  border-color: var(--color-danger);
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.error-message {
  font-size: var(--text-xs);
  color: var(--color-danger);
}
</style>

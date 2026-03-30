<script setup>
import { ref, watch } from "vue";
import { useForm } from "@/composables/useForm.js";
import { useTasks } from "@/composables/useTasks.js";
import { useUiStore } from "@/stores/index.js";
import { required, futureDate, validPriority } from "@/utils/validators.js";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close", "success"]);

const uiStore = useUiStore();
const { createTask } = useTasks();

const today = new Date().toISOString().split("T")[0];

const { values, errors, submitting, setValue, validateAll, reset, handleSubmit } = useForm(
  {
    title: "",
    due_date: today,
    priority: "medium",
  },
  {
    validationRules: {
      title: required,
      due_date: (value) => required(value) || futureDate(value),
      priority: validPriority,
    },
    resetAfterSubmit: true,
  },
);

// Reset form when modal opens
watch(
  () => props.isOpen,
  (isOpen) => {
    if (isOpen) {
      reset();
    }
  },
);

async function onSubmit() {
  if (!validateAll()) return;

  try {
    const result = await createTask(values.value);

    if (result.success) {
      uiStore.notifySuccess("Task created successfully");
      emit("success");
    } else {
      uiStore.notifyError(result.error || "Failed to create task");
    }
  } catch (err) {
    uiStore.notifyError(err.message || "An error occurred");
  }
}

function close() {
  emit("close");
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="modal-overlay" @click.self="close">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modal-title">
          <!-- Header -->
          <div class="modal-header">
            <h2 id="modal-title">Create New Task</h2>
            <button class="close-btn" @click="close" aria-label="Close modal">
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

          <!-- Form -->
          <form @submit.prevent="onSubmit" class="modal-form">
            <!-- Title -->
            <div class="form-group">
              <label for="task-title">Title <span class="required">*</span></label>
              <input
                id="task-title"
                v-model="values.title"
                type="text"
                placeholder="Enter task title"
                :class="{ error: errors.title }"
                @blur="() => {}"
              />
              <span v-if="errors.title" class="error-message">{{ errors.title }}</span>
            </div>

            <!-- Due Date -->
            <div class="form-group">
              <label for="task-due-date">Due Date <span class="required">*</span></label>
              <input
                id="task-due-date"
                v-model="values.due_date"
                type="date"
                :min="today"
                :class="{ error: errors.due_date }"
              />
              <span v-if="errors.due_date" class="error-message">{{ errors.due_date }}</span>
            </div>

            <!-- Priority -->
            <div class="form-group">
              <label for="task-priority">Priority <span class="required">*</span></label>
              <select
                id="task-priority"
                v-model="values.priority"
                :class="{ error: errors.priority }"
              >
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
              </select>
              <span v-if="errors.priority" class="error-message">{{ errors.priority }}</span>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
              <button type="button" class="btn-secondary" @click="close" :disabled="submitting">
                Cancel
              </button>
              <button type="submit" class="btn-primary" :disabled="submitting">
                <span v-if="submitting" class="spinner-small"></span>
                <span v-else>Create Task</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: var(--z-modal-backdrop);
  padding: var(--space-4);
}

.modal-content {
  background: var(--color-bg-primary);
  border-radius: var(--radius-xl);
  width: 100%;
  max-width: 480px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
  border: 1px solid var(--gray-200);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-5);
  border-bottom: 1px solid var(--gray-200);
}

.modal-header h2 {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  padding: var(--space-2);
  border-radius: var(--radius-md);
  cursor: pointer;
  color: var(--gray-500);
  transition: all var(--duration-fast);
}

.close-btn:hover {
  background-color: var(--gray-100);
  color: var(--gray-700);
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.modal-form {
  padding: var(--space-5);
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.form-group label {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--gray-700);
}

.required {
  color: var(--color-danger);
}

.form-group input,
.form-group select {
  padding: var(--space-3);
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  color: var(--gray-800);
  background-color: var(--color-bg-primary);
  transition: border-color var(--duration-fast);
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: var(--primary-500);
}

.form-group input.error,
.form-group select.error {
  border-color: var(--color-danger);
}

.error-message {
  font-size: var(--text-xs);
  color: var(--color-danger);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: var(--space-3);
  padding-top: var(--space-4);
  border-top: 1px solid var(--gray-200);
  margin-top: var(--space-2);
}

.btn-secondary {
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-300);
  background-color: var(--color-bg-primary);
  color: var(--gray-700);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--duration-fast);
}

.btn-secondary:hover:not(:disabled) {
  background-color: var(--gray-50);
  border-color: var(--gray-400);
}

.btn-primary {
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-lg);
  border: none;
  background-color: var(--primary-600);
  color: white;
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: background-color var(--duration-fast);
  display: flex;
  align-items: center;
  gap: var(--space-2);
  min-width: 120px;
  justify-content: center;
}

.btn-primary:hover:not(:disabled) {
  background-color: var(--primary-700);
}

.btn-primary:disabled,
.btn-secondary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spinner-small {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity var(--duration-normal);
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
  transition: transform var(--duration-normal) var(--ease-out);
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.95);
}

/* Responsive */
@media (max-width: 640px) {
  .modal-content {
    max-height: 100vh;
    border-radius: var(--radius-xl) var(--radius-xl) 0 0;
    margin-top: auto;
  }

  .modal-overlay {
    align-items: flex-end;
    padding: 0;
  }
}
</style>

<script setup>
import { computed } from "vue";
import { useUiStore } from "@/stores/index.js";
import { useTasks } from "@/composables/useTasks.js";
import { formatStatus, getStatusConfig } from "@/utils/formatters.js";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  task: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(["close", "success"]);

const uiStore = useUiStore();
const { deleteTask } = useTasks();

const canDelete = computed(() => {
  return props.task?.status === "done";
});

const statusText = computed(() => {
  if (!props.task) return "";
  return formatStatus(props.task.status);
});

const statusStyle = computed(() => {
  if (!props.task) return {};
  return getStatusConfig(props.task.status);
});

async function confirmDelete() {
  if (!props.task || !canDelete.value) return;

  try {
    const result = await deleteTask(props.task.id);

    if (result.success) {
      uiStore.notifySuccess("Task deleted successfully");
      emit("success");
    } else {
      uiStore.notifyError(result.error || "Failed to delete task");
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
            <h2 id="modal-title">Delete Task</h2>
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

          <!-- Content -->
          <div class="modal-body">
            <!-- Warning Icon -->
            <div class="warning-icon" :class="{ error: !canDelete }">
              <svg v-if="canDelete" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
              <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                />
              </svg>
            </div>

            <!-- Task Info -->
            <div v-if="task" class="task-info">
              <p class="task-title">{{ task.title }}</p>
              <div class="task-meta">
                <span
                  class="status-badge"
                  :style="{ backgroundColor: statusStyle.bgColor, color: statusStyle.color }"
                >
                  {{ statusText }}
                </span>
              </div>
            </div>

            <!-- Message -->
            <div v-if="canDelete" class="message">
              <p class="message-title">Are you sure?</p>
              <p class="message-text">
                This action cannot be undone. The task will be permanently deleted.
              </p>
            </div>

            <div v-else class="message error">
              <p class="message-title">Cannot Delete</p>
              <p class="message-text">
                Only completed tasks can be deleted. Please mark this task as done first.
              </p>
            </div>
          </div>

          <!-- Footer -->
          <div class="modal-footer">
            <button class="btn-secondary" @click="close">
              {{ canDelete ? "Cancel" : "Close" }}
            </button>
            <button v-if="canDelete" class="btn-danger" @click="confirmDelete">Delete</button>
          </div>
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
  max-width: 400px;
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

.modal-body {
  padding: var(--space-6);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.warning-icon {
  width: 64px;
  height: 64px;
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--color-warning-light);
  color: var(--color-warning);
  margin-bottom: var(--space-5);
}

.warning-icon.error {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
}

.warning-icon svg {
  width: 32px;
  height: 32px;
}

.task-info {
  margin-bottom: var(--space-4);
}

.task-title {
  font-size: var(--text-base);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0 0 var(--space-2);
  line-height: 1.4;
}

.task-meta {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
}

.status-badge {
  display: inline-block;
  padding: var(--space-1) var(--space-3);
  border-radius: var(--radius-md);
  font-size: var(--text-xs);
  font-weight: var(--font-semibold);
  text-transform: capitalize;
}

.message {
  max-width: 280px;
}

.message-title {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
  margin: 0 0 var(--space-2);
}

.message-text {
  font-size: var(--text-sm);
  color: var(--gray-500);
  margin: 0;
  line-height: 1.5;
}

.message.error .message-title {
  color: var(--color-danger);
}

.modal-footer {
  display: flex;
  justify-content: center;
  gap: var(--space-3);
  padding: var(--space-4) var(--space-5);
  border-top: 1px solid var(--gray-200);
}

.btn-secondary {
  padding: var(--space-3) var(--space-5);
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-300);
  background-color: var(--color-bg-primary);
  color: var(--gray-700);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: all var(--duration-fast);
}

.btn-secondary:hover {
  background-color: var(--gray-50);
  border-color: var(--gray-400);
}

.btn-danger {
  padding: var(--space-3) var(--space-5);
  border-radius: var(--radius-lg);
  border: none;
  background-color: var(--color-danger);
  color: white;
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  cursor: pointer;
  transition: background-color var(--duration-fast);
}

.btn-danger:hover {
  background-color: #dc2626;
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

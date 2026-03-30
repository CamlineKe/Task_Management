<script setup>
import { computed } from "vue";
import { useUiStore } from "@/stores/index.js";
import { useTasks } from "@/composables/useTasks.js";
import { STATUS_TRANSITIONS } from "@/utils/constants.js";
import { getStatusConfig, formatStatus } from "@/utils/formatters.js";

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
const { updateTaskStatus: updateStatus } = useTasks();

const currentStatus = computed(() => props.task?.status || "pending");

const allowedTransitions = computed(() => {
  return STATUS_TRANSITIONS[currentStatus.value] || [];
});

const canTransition = computed(() => allowedTransitions.value.length > 0);

async function transitionTo(newStatus) {
  if (!props.task) return;

  try {
    const result = await updateStatus(props.task.id, newStatus);

    if (result.success) {
      uiStore.notifySuccess(`Status updated to ${formatStatus(newStatus)}`);
      emit("success");
    } else {
      uiStore.notifyError(result.error || "Failed to update status");
    }
  } catch (err) {
    uiStore.notifyError(err.message || "An error occurred");
  }
}

function close() {
  emit("close");
}

function getStatusStyle(status) {
  const config = getStatusConfig(status);
  return {
    backgroundColor: config.bgColor,
    color: config.color,
    borderColor: config.color,
  };
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="modal-overlay" @click.self="close">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modal-title">
          <!-- Header -->
          <div class="modal-header">
            <h2 id="modal-title">Update Task Status</h2>
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
            <!-- Task Info -->
            <div v-if="task" class="task-info">
              <p class="task-label">Task:</p>
              <p class="task-title">{{ task.title }}</p>
              <div class="current-status">
                <span class="status-label">Current:</span>
                <span class="status-badge" :style="getStatusStyle(currentStatus)">
                  {{ formatStatus(currentStatus) }}
                </span>
              </div>
            </div>

            <!-- Transition Options -->
            <div v-if="canTransition" class="transition-options">
              <p class="options-label">Change to:</p>
              <div class="options-grid">
                <button
                  v-for="status in allowedTransitions"
                  :key="status"
                  class="status-option"
                  :style="getStatusStyle(status)"
                  @click="transitionTo(status)"
                >
                  {{ formatStatus(status) }}
                  <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M13 7l5 5m0 0l-5 5m5-5H6"
                    />
                  </svg>
                </button>
              </div>
            </div>

            <!-- No Transitions Message -->
            <div v-else class="no-transitions">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                />
              </svg>
              <p>No status changes available</p>
              <span class="subtext">This task is in a terminal state</span>
            </div>
          </div>

          <!-- Footer -->
          <div class="modal-footer">
            <button class="btn-secondary" @click="close">Cancel</button>
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
  max-width: 420px;
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
  padding: var(--space-5);
}

.task-info {
  margin-bottom: var(--space-5);
  padding-bottom: var(--space-5);
  border-bottom: 1px solid var(--gray-200);
}

.task-label {
  font-size: var(--text-xs);
  color: var(--gray-500);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0 0 var(--space-2);
}

.task-title {
  font-size: var(--text-base);
  font-weight: var(--font-medium);
  color: var(--gray-800);
  margin: 0 0 var(--space-3);
  line-height: 1.4;
}

.current-status {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.status-label {
  font-size: var(--text-sm);
  color: var(--gray-500);
}

.status-badge {
  display: inline-block;
  padding: var(--space-1) var(--space-3);
  border-radius: var(--radius-md);
  font-size: var(--text-xs);
  font-weight: var(--font-semibold);
  text-transform: capitalize;
}

.options-label {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--gray-700);
  margin: 0 0 var(--space-3);
}

.options-grid {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.status-option {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-4);
  border-radius: var(--radius-lg);
  border: 2px solid;
  background: var(--color-bg-primary);
  font-size: var(--text-base);
  font-weight: var(--font-semibold);
  text-transform: capitalize;
  cursor: pointer;
  transition: all var(--duration-fast);
}

.status-option:hover {
  transform: translateX(4px);
  filter: brightness(0.95);
}

.arrow-icon {
  width: 20px;
  height: 20px;
}

.no-transitions {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: var(--space-8) var(--space-4);
  color: var(--gray-500);
}

.no-transitions svg {
  width: 48px;
  height: 48px;
  margin-bottom: var(--space-4);
  color: var(--gray-400);
}

.no-transitions p {
  font-size: var(--text-base);
  font-weight: var(--font-medium);
  color: var(--gray-700);
  margin: 0 0 var(--space-2);
}

.subtext {
  font-size: var(--text-sm);
  color: var(--gray-500);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  padding: var(--space-4) var(--space-5);
  border-top: 1px solid var(--gray-200);
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

.btn-secondary:hover {
  background-color: var(--gray-50);
  border-color: var(--gray-400);
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

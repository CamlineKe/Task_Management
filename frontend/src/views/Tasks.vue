<script setup>
import { onMounted, ref, computed } from "vue";
import { useTaskStore, useUiStore } from "@/stores/index.js";
import TaskTable from "@/components/tasks/TaskTable.vue";
import TaskFilters from "@/components/tasks/TaskFilters.vue";
import Pagination from "@/components/tasks/Pagination.vue";
import CreateTaskModal from "@/components/modals/CreateTaskModal.vue";
import UpdateStatusModal from "@/components/modals/UpdateStatusModal.vue";
import DeleteConfirmationModal from "@/components/modals/DeleteConfirmationModal.vue";

const taskStore = useTaskStore();
const uiStore = useUiStore();

const currentPage = ref(1);
const perPage = ref(15);

// Paginated tasks
const paginatedTasks = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return taskStore.filteredTasks.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(taskStore.filteredTasks.length / perPage.value) || 1;
});

onMounted(() => {
  taskStore.fetchTasks();
});

function handlePageChange(page) {
  currentPage.value = page;
}

function handleFilterChange(status) {
  taskStore.setFilter(status);
  currentPage.value = 1;
}

function openCreateModal() {
  uiStore.openModal("createTask");
}

function handleTaskCreated() {
  taskStore.fetchTasks();
  uiStore.closeModal("createTask");
}

function handleTaskUpdated() {
  taskStore.fetchTasks();
  uiStore.closeModal("updateStatus");
}

function handleTaskDeleted() {
  taskStore.fetchTasks();
  uiStore.closeModal("deleteConfirm");
}

function openStatusModal(task) {
  uiStore.setSelectedTask(task);
  uiStore.openModal("updateStatus");
}

function openDeleteModal(task) {
  uiStore.setSelectedTask(task);
  uiStore.openModal("deleteConfirm");
}
</script>

<template>
  <div class="tasks-page">
    <!-- Header -->
    <div class="page-header">
      <h1>Tasks</h1>
      <button class="btn-primary" @click="openCreateModal">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4v16m8-8H4"
          />
        </svg>
        Create Task
      </button>
    </div>

    <!-- Filters -->
    <TaskFilters :current-filter="taskStore.filterStatus" @filter-change="handleFilterChange" />

    <!-- Loading State -->
    <div v-if="taskStore.loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading tasks...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="taskStore.allTasks.length === 0" class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
        />
      </svg>
      <h3>No tasks yet</h3>
      <p>Create your first task to get started</p>
      <button class="btn-primary" @click="openCreateModal">Create Task</button>
    </div>

    <!-- Task Table -->
    <template v-else>
      <TaskTable
        :tasks="paginatedTasks"
        @update-status="openStatusModal"
        @delete="openDeleteModal"
      />

      <!-- Pagination -->
      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        @page-change="handlePageChange"
      />
    </template>

    <!-- Modals -->
    <CreateTaskModal
      :is-open="uiStore.modals.createTask"
      @close="uiStore.closeModal('createTask')"
      @success="handleTaskCreated"
    />

    <UpdateStatusModal
      :is-open="uiStore.modals.updateStatus"
      :task="uiStore.selectedTask"
      @close="uiStore.closeModal('updateStatus')"
      @success="handleTaskUpdated"
    />

    <DeleteConfirmationModal
      :is-open="uiStore.modals.deleteConfirm"
      :task="uiStore.selectedTask"
      @close="uiStore.closeModal('deleteConfirm')"
      @success="handleTaskDeleted"
    />
  </div>
</template>

<style scoped>
.tasks-page {
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.page-header h1 {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--gray-900);
  margin: 0;
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
  color: white;
  border: none;
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  font-weight: var(--font-semibold);
  cursor: pointer;
  transition: all var(--duration-fast);
  box-shadow: 0 1px 2px 0 rgb(37 99 235 / 0.3);
}

.btn-primary:hover {
  background: linear-gradient(135deg, var(--primary-700) 0%, var(--primary-800) 100%);
  box-shadow: 0 4px 6px -1px rgb(37 99 235 / 0.4);
  transform: translateY(-1px);
}

.btn-primary:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px 0 rgb(37 99 235 / 0.3);
}

.btn-primary svg {
  width: 20px;
  height: 20px;
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--space-16);
  color: var(--gray-500);
  gap: var(--space-4);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--gray-200);
  border-top-color: var(--primary-600);
  border-radius: var(--radius-full);
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--space-16);
  text-align: center;
  color: var(--gray-500);
}

.empty-state svg {
  width: 64px;
  height: 64px;
  color: var(--gray-300);
  margin-bottom: var(--space-4);
}

.empty-state h3 {
  font-size: var(--text-xl);
  font-weight: var(--font-bold);
  color: var(--gray-900);
  margin: 0 0 var(--space-2);
}

.empty-state p {
  margin: 0 0 var(--space-6);
}

/* Responsive */
@media (max-width: 640px) {
  .page-header {
    flex-direction: column;
    gap: var(--space-4);
    align-items: flex-start;
  }
}
</style>

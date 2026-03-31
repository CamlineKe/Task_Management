<script setup>
import { onMounted } from 'vue'
import AppSidebar from './AppSidebar.vue'
import AppHeader from './AppHeader.vue'
import { useUiStore } from '@/stores/index.js'

const uiStore = useUiStore()

onMounted(() => {
  // Initialize network status listeners
  uiStore.initNetworkListeners()
})
</script>

<template>
  <div class="app-layout" :class="{ 'sidebar-collapsed': uiStore.isSidebarCollapsed }">
    <!-- Sidebar Navigation -->
    <AppSidebar />
    
    <!-- Mobile Sidebar Backdrop -->
    <div 
      v-if="uiStore.isMobileSidebarOpen" 
      class="sidebar-backdrop"
      @click="uiStore.closeMobileSidebar"
    />
    
    <!-- Main Content Area -->
    <div class="main-content">
      <!-- Top Header -->
      <AppHeader />
      
      <!-- Page Content -->
      <main class="page-content">
        <slot />
      </main>
    </div>
    
    <!-- Toast Notifications Container -->
    <div class="notifications-container">
      <TransitionGroup name="notification">
        <div
          v-for="notification in uiStore.notifications"
          :key="notification.id"
          class="notification"
          :class="`notification-${notification.type}`"
        >
          <span class="notification-message">{{ notification.message }}</span>
          <button 
            class="notification-close"
            @click="uiStore.removeNotification(notification.id)"
          >
            ×
          </button>
          <div 
            class="notification-progress"
            :style="{ width: `${notification.progress}%` }"
          />
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background-color: var(--gray-100);
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  margin-left: var(--sidebar-width);
  transition: margin-left var(--duration-normal);
}

.sidebar-collapsed .main-content {
  margin-left: var(--sidebar-width-collapsed);
}

.page-content {
  flex: 1;
  padding: var(--space-6);
  overflow-y: auto;
}

/* Mobile Sidebar Backdrop */
.sidebar-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: calc(var(--z-fixed) - 10);
}

/* Notifications */
.notifications-container {
  position: fixed;
  top: var(--space-4);
  right: var(--space-4);
  z-index: var(--z-toast);
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  max-width: 400px;
}

.notification {
  position: relative;
  padding: var(--space-4);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-3);
}

.notification-success {
  background-color: var(--color-success-light);
  border-left: 4px solid var(--color-success);
}

.notification-error {
  background-color: var(--color-danger-light);
  border-left: 4px solid var(--color-danger);
}

.notification-warning {
  background-color: var(--color-warning-light);
  border-left: 4px solid var(--color-warning);
}

.notification-info {
  background-color: var(--color-info-light);
  border-left: 4px solid var(--color-info);
}

.notification-message {
  flex: 1;
  font-size: var(--text-sm);
  color: var(--gray-800);
}

.notification-close {
  background: none;
  border: none;
  font-size: var(--text-xl);
  color: var(--gray-500);
  cursor: pointer;
  padding: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-sm);
  transition: background-color var(--duration-fast);
}

.notification-close:hover {
  background-color: rgba(0, 0, 0, 0.1);
}

.notification-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  background-color: currentColor;
  opacity: 0.3;
  transition: width linear 100ms;
}

/* Transitions */
.notification-enter-active,
.notification-leave-active {
  transition: all var(--duration-normal) var(--ease-out);
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

/* Responsive */
@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }
  
  .sidebar-collapsed .main-content {
    margin-left: 0;
  }
  
  .notifications-container {
    left: var(--space-4);
    right: var(--space-4);
    max-width: none;
  }
}

/* Dark Theme */
[data-theme="dark"] .notification-message {
  color: var(--gray-900);
}

[data-theme="dark"] .notification-close {
  color: var(--gray-600);
}

[data-theme="dark"] .notification-close:hover {
  background-color: rgba(0, 0, 0, 0.15);
}
</style>
<script setup>
import { computed } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useUiStore } from '@/stores/index.js'

const uiStore = useUiStore()
const route = useRoute()

const isCollapsed = computed(() => uiStore.isSidebarCollapsed)

const menuItems = [
  {
    name: 'Dashboard',
    path: '/',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
  },
  {
    name: 'Tasks',
    path: '/tasks',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
  },
  {
    name: 'Report',
    path: '/report',
    icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
  },
]

function isActive(path) {
  return route.path === path
}

function handleNavClick() {
  // Close mobile sidebar after navigation
  if (uiStore.isMobileSidebarOpen) {
    uiStore.closeMobileSidebar()
  }
}
</script>

<template>
  <aside class="sidebar" :class="{ 'collapsed': isCollapsed, 'mobile-open': uiStore.isMobileSidebarOpen }">
    <!-- Logo -->
    <div class="sidebar-header">
      <div class="logo">
        <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span v-if="!isCollapsed" class="logo-text">Task Manager</span>
      </div>
      
      <!-- Toggle Button -->
      <button 
        class="toggle-btn desktop-only"
        @click="uiStore.toggleSidebar"
        :title="isCollapsed ? 'Expand' : 'Collapse'"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path 
            v-if="isCollapsed"
            stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M13 5l7 7-7 7M5 5l7 7-7 7" 
          />
          <path 
            v-else
            stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" 
          />
        </svg>
      </button>

      <!-- Mobile Close Button -->
      <button 
        class="toggle-btn mobile-only"
        @click="uiStore.closeMobileSidebar"
        title="Close menu"
      >
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

    <!-- Navigation -->
    <nav class="sidebar-nav">
      <ul class="nav-list">
        <li v-for="item in menuItems" :key="item.path">
          <RouterLink 
            :to="item.path"
            class="nav-link"
            :class="{ 'active': isActive(item.path) }"
            @click="handleNavClick"
          >
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
            </svg>
            <span v-if="!isCollapsed" class="nav-text">{{ item.name }}</span>
          </RouterLink>
        </li>
      </ul>
    </nav>

    <!-- Footer -->
    <div v-if="!isCollapsed" class="sidebar-footer">
      <div class="network-status" :class="{ 'online': uiStore.isOnline }">
        <span class="status-dot"></span>
        <span class="status-text">{{ uiStore.isOnline ? 'Online' : 'Offline' }}</span>
      </div>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: var(--sidebar-width);
  background-color: var(--gray-900);
  color: white;
  display: flex;
  flex-direction: column;
  transition: width var(--duration-normal), transform var(--duration-normal), visibility 0s;
  z-index: var(--z-fixed);
  overflow-x: hidden;
}

.sidebar.collapsed {
  width: var(--sidebar-width-collapsed);
}

/* Header */
.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-4);
  border-bottom: 1px solid var(--gray-800);
}

.logo {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  overflow: hidden;
}

.logo-icon {
  width: 32px;
  height: 32px;
  flex-shrink: 0;
  color: var(--primary-400);
}

.logo-text {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  white-space: nowrap;
  color: var(--gray-100);
}

.toggle-btn {
  background: none;
  border: none;
  color: var(--gray-400);
  cursor: pointer;
  padding: var(--space-2);
  border-radius: var(--radius-md);
  transition: all var(--duration-fast);
}

.toggle-btn:hover {
  background-color: var(--gray-800);
  color: var(--gray-100);
}

.toggle-btn svg {
  width: 20px;
  height: 20px;
}

/* Navigation */
.sidebar-nav {
  flex: 1;
  padding: var(--space-4) 0;
  overflow-y: auto;
}

.nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-3) var(--space-4);
  color: var(--gray-400);
  text-decoration: none;
  transition: all var(--duration-fast);
  margin: var(--space-1) var(--space-3);
  border-radius: var(--radius-lg);
}

.nav-link:hover {
  background-color: var(--gray-800);
  color: var(--gray-100);
}

.nav-link.active {
  background-color: var(--primary-600);
  color: var(--gray-100);
}

.nav-icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
}

.nav-text {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  white-space: nowrap;
}

/* Footer */
.sidebar-footer {
  padding: var(--space-4);
  border-top: 1px solid var(--gray-800);
}

.network-status {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  font-size: var(--text-xs);
  color: var(--gray-500);
}

.network-status.online {
  color: var(--color-success);
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: var(--radius-full);
  background-color: var(--gray-500);
}

.network-status.online .status-dot {
  background-color: var(--color-success);
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar.collapsed {
    width: var(--sidebar-width);
  }
  
  .desktop-only {
    display: none !important;
  }
  
  .mobile-only {
    display: flex !important;
  }
}

/* Visibility utilities */
.desktop-only {
  display: flex;
}

.mobile-only {
  display: none;
}

@media (max-width: 768px) {
  .desktop-only {
    display: none;
  }
  .mobile-only {
    display: flex;
  }
}
</style>
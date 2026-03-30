import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(createPinia())
app.use(router)

// Initialize theme before mounting from localStorage or system preference
const savedTheme = localStorage.getItem('theme')
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches

if (savedTheme) {
  document.documentElement.setAttribute('data-theme', savedTheme)
} else if (prefersDark) {
  document.documentElement.setAttribute('data-theme', 'dark')
}

app.mount('#app')

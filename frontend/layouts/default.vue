<template>
  <div>
    <!-- Loading screen while auth is being initialized -->
    <div v-if="isAuthLoading" class="min-h-screen bg-white dark:bg-gray-900 flex items-center justify-center">
      <div class="text-center">
        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-600 dark:text-gray-300">読み込み中...</p>
      </div>
    </div>
    
    <!-- Main content when auth is ready -->
    <div v-else>
      <!-- Header (全ページ共通) -->
      <AppHeader />
      <slot />
      <!-- Floating Footer (ログインページ以外で表示) -->
      <FloatingFooter v-if="!isLoginPage" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useRoute } from 'vue-router'
import AppHeader from '~/components/AppHeader.vue'
import FloatingFooter from '~/components/FloatingFooter.vue'

const authStore = useAuthStore()
const route = useRoute()
const isAuthLoading = ref(true)

// ログインページかどうかを判定
const isLoginPage = computed(() => route.path === '/login')

onMounted(async () => {
  // Initialize auth state
  authStore.initializeAuth()
  
  // Wait a bit for auth state to be ready
  await nextTick()
  await new Promise(resolve => setTimeout(resolve, 50))
  
  isAuthLoading.value = false
})
</script>
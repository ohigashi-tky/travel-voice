<template>
  <header class="fixed top-0 left-0 right-0 z-40 h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 flex items-center justify-between transition-colors duration-300">
    <!-- User Icon / Profile Button -->
    <button 
      @click="authStore.isAuthenticated ? showProfileModal = true : navigateTo('/login')" 
      class="w-10 h-10 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-full flex items-center justify-center"
    >
      <User class="w-6 h-6 text-white" />
    </button>
    
    <!-- Settings Icon -->
    <button @click="showSettingsModal = true" class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
      <Settings class="w-6 h-6 text-gray-600 dark:text-gray-300" />
    </button>
  </header>
  
  <!-- Profile Modal -->
  <div v-if="showProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="showProfileModal = false">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 m-4 max-w-sm w-full transition-colors duration-300" @click.stop>
      <div class="text-center">
        <div class="w-20 h-20 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <User class="w-10 h-10 text-white" />
        </div>
        <h3 class="text-xl font-light text-gray-800 dark:text-white mb-2 tracking-wide transition-colors duration-300">{{ authStore.user?.name || 'ゲスト' }}</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm font-thin mb-6 transition-colors duration-300">{{ authStore.user?.email || '' }}</p>
        
        <button 
          @click="logout"
          class="w-full bg-red-500 hover:bg-red-600 text-white py-3 px-4 rounded-lg font-thin tracking-wide transition-colors"
        >
          {{ t('ログアウト') }}
        </button>
      </div>
    </div>
  </div>
  
  <!-- Settings Modal -->
  <div v-if="showSettingsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="showSettingsModal = false">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 m-4 max-w-sm w-full transition-colors duration-300" @click.stop>
      <h3 class="text-xl font-light text-gray-800 dark:text-white mb-6 tracking-wide text-center transition-colors duration-300">{{ t('設定') }}</h3>
      
      <div class="space-y-4">
        <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
          <span class="text-gray-700 dark:text-gray-300 font-thin tracking-wide transition-colors duration-300">{{ t('ダークモード') }}</span>
          <button 
            @click="toggleDarkMode"
            :class="[
              'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
              isDark ? 'bg-blue-600' : 'bg-gray-300'
            ]"
          >
            <span 
              :class="[
                'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                isDark ? 'translate-x-6' : 'translate-x-1'
              ]"
            />
          </button>
        </div>
        
        <div class="py-3 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between mb-3">
            <span class="text-gray-700 dark:text-gray-300 font-thin tracking-wide transition-colors duration-300">{{ t('言語') }}</span>
          </div>
          <div class="flex gap-2">
            <button
              v-for="locale in availableLocales"
              :key="locale.code"
              @click="selectLanguage(locale.code)"
              :class="[
                'flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                currentLocale === locale.code
                  ? 'bg-blue-600 text-white'
                  : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
              ]"
            >
              {{ t(locale.name) }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useDark, useToggle } from '@vueuse/core'
import { User, Settings } from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'
import { useLanguage } from '~/composables/useLanguage'

// Dark mode setup
const isDark = useDark()
const toggleDark = useToggle(isDark)

// Language setup
const { t, availableLocales, currentLocale, setLocale } = useLanguage()

// Auth store
const authStore = useAuthStore()

// Reactive variables
const showProfileModal = ref(false)
const showSettingsModal = ref(false)

// Methods
const logout = async () => {
  authStore.logout()
  showProfileModal.value = false
  await navigateTo('/login')
}

const toggleDarkMode = () => {
  toggleDark()
}

const selectLanguage = (localeCode) => {
  currentLocale.value = localeCode
  setLocale(localeCode)
}

const changeLanguage = () => {
  setLocale(currentLocale.value)
  // 設定モーダルを閉じる
  showSettingsModal.value = false
}
</script>
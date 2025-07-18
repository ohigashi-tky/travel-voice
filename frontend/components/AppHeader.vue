<template>
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
    <div class="h-16 px-4 flex items-center justify-between">
      <!-- User Icon / Profile Button -->
      <button 
        @click="authStore.isAuthenticated ? showProfileModal = true : navigateTo('/login')" 
        class="w-10 h-10 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-full flex items-center justify-center"
      >
        <User class="w-6 h-6 text-white" />
      </button>
      
      <!-- Center Title -->
      <div class="flex-1 flex flex-col items-center justify-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white tracking-wider transition-colors duration-300">
          <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent font-extrabold">
            おうち旅行
          </span>
        </h1>
        <p class="text-xs text-gray-600 dark:text-gray-300 font-medium tracking-wide transition-colors duration-300">
          音声ガイドで日本を巡ろう
        </p>
      </div>
      
      <!-- Right side icons -->
      <div class="flex items-center space-x-3">
        <!-- Settings Gear Icon with Dropdown -->
        <div class="relative">
          <button 
            @click="toggleSettingsDropdown" 
            data-settings-trigger
            class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
          >
            <Settings class="w-5 h-5 text-gray-600 dark:text-gray-300" />
          </button>
          
          <!-- Settings Dropdown -->
          <div 
            v-if="showSettingsDropdown"
            ref="settingsDropdown"
            class="absolute right-0 top-12 w-72 sm:w-80 max-w-[calc(100vw-2rem)] max-h-96 overflow-y-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 transition-colors duration-300 transform-gpu"
          >
            <div class="p-4">
              <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 tracking-wide transition-colors duration-300">{{ t('設定') }}</h3>
              
              <!-- Voice Settings Section -->
              <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-3">
                <VoiceSettings />
              </div>
              
              <!-- Dark Mode Section -->
              <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-3">
                <div class="flex items-center justify-between">
                  <span class="text-gray-700 dark:text-gray-300 font-medium tracking-wide transition-colors duration-300">{{ t('ダークモード') }}</span>
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
              </div>
              
              <!-- Language Section -->
              <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                <div class="flex items-center justify-between">
                  <span class="text-gray-700 dark:text-gray-300 font-medium tracking-wide transition-colors duration-300">{{ t('言語') }}</span>
                  <div class="flex gap-2">
                    <button
                      v-for="locale in availableLocales"
                      :key="locale.code"
                      @click="selectLanguage(locale.code)"
                      :class="[
                        'px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200',
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
        </div>
      </div>
    </div>
  </header>
  
  <!-- Profile Modal -->
  <div v-if="showProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="showProfileModal = false">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 m-4 max-w-sm w-full transition-colors duration-300" @click.stop>
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
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useDark, useToggle } from '@vueuse/core'
import { User, Settings } from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'
import { useLanguage } from '~/composables/useLanguage'
import VoiceSettings from '~/components/VoiceSettings.vue'

// Dark mode setup
const isDark = useDark()
const toggleDark = useToggle(isDark)

// Language setup
const { t, availableLocales, currentLocale, setLocale } = useLanguage()

// Auth store
const authStore = useAuthStore()

// Reactive variables
const showProfileModal = ref(false)
const showSettingsDropdown = ref(false)
const settingsDropdown = ref(null)

// 外側クリック検知
const handleClickOutside = (event) => {
  if (settingsDropdown.value && !settingsDropdown.value.contains(event.target) && !event.target.closest('button[data-settings-trigger]')) {
    showSettingsDropdown.value = false
  }
}

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
  setLocale(localeCode)
}

const toggleSettingsDropdown = () => {
  showSettingsDropdown.value = !showSettingsDropdown.value
}

// ライフサイクル
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
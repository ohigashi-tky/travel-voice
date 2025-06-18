<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex items-center justify-center px-6 transition-colors duration-300">
    <!-- Settings Icon (Fixed Position) -->
    <button 
      @click="showSettingsModal = true" 
      class="fixed top-6 right-6 w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors z-50"
    >
      <Settings class="w-6 h-6 text-gray-600 dark:text-gray-300" />
    </button>
    
    <!-- Main Content -->
      <div class="w-full max-w-md">
        <!-- App Title -->
        <div class="text-center mb-8">
          <h1 class="text-6xl font-bold text-gray-800 dark:text-white mb-4 tracking-wider transition-colors duration-300">
            <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent font-extrabold">
              Travel Voice
            </span>
          </h1>
          <p class="text-lg text-gray-600 dark:text-gray-300 font-medium tracking-wide transition-colors duration-300">
            音声ガイドで観光を楽しもう
          </p>
        </div>

        <!-- Login Form -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-3xl p-8 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Name Field (only for register) -->
            <div v-if="isRegisterMode">
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                お名前
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-300"
                placeholder="田中太郎"
              />
            </div>

            <!-- Email Field -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                メールアドレス
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-300"
                placeholder="demo@example.com"
              />
            </div>

            <!-- Password Field -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                パスワード
              </label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-300"
                placeholder="password"
              />
            </div>

            <!-- Error Message -->
            <div v-if="authStore.error" class="p-3 bg-red-100 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded-lg">
              <p class="text-red-700 dark:text-red-400 text-sm">{{ authStore.error }}</p>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="authStore.isLoading"
              class="w-full bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 text-white py-4 px-6 rounded-xl font-medium text-lg tracking-wide hover:from-cyan-700 hover:via-blue-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105"
            >
              <span v-if="authStore.isLoading" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                処理中...
              </span>
              <span v-else>
                {{ isRegisterMode ? 'アカウント登録' : 'ログイン' }}
              </span>
            </button>

            <!-- Switch Mode Button -->
            <button
              type="button"
              @click="toggleMode"
              class="w-full text-blue-600 dark:text-blue-400 py-2 px-4 rounded-lg font-medium hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-300"
            >
              {{ isRegisterMode ? 'ログインはこちら' : 'アカウント登録はこちら' }}
            </button>
          </form>
        </div>

        <!-- Demo Credentials -->
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
          <p class="text-blue-700 dark:text-blue-300 text-sm text-center">
            <strong>デモ用アカウント:</strong><br>
            メール: demo@example.com<br>
            パスワード: password
          </p>
        </div>
      </div>

    <!-- Settings Modal -->
    <div v-if="showSettingsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="showSettingsModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 m-4 max-w-sm w-full transition-colors duration-300" @click.stop>
        <h3 class="text-xl font-light text-gray-800 dark:text-white mb-6 tracking-wide text-center transition-colors duration-300">設定</h3>
        
        <div class="space-y-4">
          <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
            <span class="text-gray-700 dark:text-gray-300 font-thin tracking-wide transition-colors duration-300">ダークモード</span>
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
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useDark, useToggle } from '@vueuse/core'
import { Settings } from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'

// Dark mode setup
const isDark = useDark()
const toggleDark = useToggle(isDark)

// Auth store
const authStore = useAuthStore()

// Reactive variables
const showSettingsModal = ref(false)
const isRegisterMode = ref(false)

// Form data
const form = reactive({
  name: '',
  email: '',
  password: ''
})

// Initialize on mount (middleware already handles redirection)
onMounted(() => {
  // Middleware already handles authentication check
})

// Methods
const toggleDarkMode = () => {
  toggleDark()
}

const toggleMode = () => {
  isRegisterMode.value = !isRegisterMode.value
  // Clear form
  form.name = ''
  form.email = ''
  form.password = ''
}

const handleSubmit = async () => {
  let result

  if (isRegisterMode.value) {
    result = await authStore.register(form.name, form.email, form.password)
  } else {
    result = await authStore.login(form.email, form.password)
  }

  if (result.success) {
    // Redirect to home page
    await navigateTo('/')
  }
}

// Page meta
definePageMeta({
  middleware: 'guest'
})

// SEO
useHead({
  title: 'ログイン - Travel Voice',
  meta: [
    { name: 'description', content: 'Travel Voiceにログインして、音声ガイド付きの観光体験をお楽しみください。' }
  ]
})
</script>
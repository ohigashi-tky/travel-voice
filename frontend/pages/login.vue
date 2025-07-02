<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex items-center justify-center px-6 transition-colors duration-300">
    
    <!-- Main Content -->
      <div class="w-full max-w-md">
        <!-- App Title -->
        <div class="text-center mb-8">
          <h1 class="text-5xl font-bold text-gray-800 dark:text-white mb-4 tracking-wider transition-colors duration-300">
            <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent font-extrabold">
              おうち旅行
            </span>
          </h1>
          <p class="text-lg text-gray-600 dark:text-gray-300 font-medium tracking-wide transition-colors duration-300">
            穏やかな音声で日本を巡ろう
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
                placeholder="TravelGuide2024!"
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
            パスワード: TravelGuide2024!
          </p>
        </div>
      </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '~/stores/auth'

// Auth store
const authStore = useAuthStore()

// Reactive variables
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
  title: 'ログイン - おうち旅行',
  meta: [
    { name: 'description', content: 'おうち旅行にログインして、穏やかな音声で日本を巡る旅行体験をお楽しみください。' }
  ]
})
</script>
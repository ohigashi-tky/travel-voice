<template>
  <header class="glass sticky top-0 z-50 border-b border-white/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <NuxtLink
          to="/"
          class="flex items-center space-x-3 hover:opacity-80 transition-opacity"
        >
          <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
            <MapPin class="w-5 h-5 text-white" />
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-900">観光ガイド</h1>
            <p class="text-xs text-gray-600 hidden sm:block">日本の美しい観光地を発見</p>
          </div>
        </NuxtLink>

        <!-- Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <NuxtLink
            to="/"
            class="text-gray-700 hover:text-primary-600 font-medium transition-colors"
          >
            ホーム
          </NuxtLink>
          <NuxtLink
            to="/spots"
            class="text-gray-700 hover:text-primary-600 font-medium transition-colors"
          >
            観光スポット
          </NuxtLink>
          <NuxtLink
            to="/guides"
            class="text-gray-700 hover:text-primary-600 font-medium transition-colors"
          >
            ガイド
          </NuxtLink>
        </nav>

        <!-- User Menu -->
        <div class="flex items-center space-x-4">
          <template v-if="isLoggedIn">
            <div class="hidden sm:flex items-center space-x-3">
              <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                <User class="w-4 h-4 text-primary-600" />
              </div>
              <span class="text-sm font-medium text-gray-700">{{ user?.name }}</span>
            </div>
            <BaseButton
              variant="ghost"
              size="sm"
              icon="lucide:log-out"
              @click="handleLogout"
            >
              ログアウト
            </BaseButton>
          </template>
          <template v-else>
            <BaseButton
              variant="ghost"
              size="sm"
              @click="$router.push('/login')"
            >
              ログイン
            </BaseButton>
            <BaseButton
              size="sm"
              @click="$router.push('/register')"
            >
              新規登録
            </BaseButton>
          </template>

          <!-- Mobile Menu Button -->
          <button
            class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
            @click="mobileMenuOpen = !mobileMenuOpen"
          >
            <X v-if="mobileMenuOpen" class="w-5 h-5 text-gray-700" />
            <Menu v-else class="w-5 h-5 text-gray-700" />
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div
        v-if="mobileMenuOpen"
        class="md:hidden py-4 border-t border-gray-200 mt-4"
      >
        <nav class="flex flex-col space-y-3">
          <NuxtLink
            to="/"
            class="text-gray-700 hover:text-primary-600 font-medium transition-colors py-2"
            @click="mobileMenuOpen = false"
          >
            ホーム
          </NuxtLink>
          <NuxtLink
            to="/spots"
            class="text-gray-700 hover:text-primary-600 font-medium transition-colors py-2"
            @click="mobileMenuOpen = false"
          >
            観光スポット
          </NuxtLink>
          <NuxtLink
            to="/guides"
            class="text-gray-700 hover:text-primary-600 font-medium transition-colors py-2"
            @click="mobileMenuOpen = false"
          >
            ガイド
          </NuxtLink>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { MapPin, User, X, Menu } from 'lucide-vue-next'

const { user, isLoggedIn, logout } = useAuth()
const mobileMenuOpen = ref(false)

const handleLogout = async () => {
  await logout()
  mobileMenuOpen.value = false
}
</script>
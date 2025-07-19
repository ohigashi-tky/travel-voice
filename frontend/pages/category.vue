<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">

    <!-- Page Title -->
    <div class="bg-white dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
          <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-wide transition-colors duration-300">
              <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent">
                {{ categoryName }}
              </span>
            </h1>
            <p class="text-gray-600 dark:text-gray-300 text-sm mt-2">{{ filteredSpots.length }}件の観光地</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 pb-24">
      <div class="max-w-7xl mx-auto py-6">
        
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="text-gray-600 dark:text-gray-300 mt-4">読み込み中...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <div class="text-6xl mb-4">⚠️</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            データの読み込みに失敗しました
          </h3>
          <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300 mb-6">
            {{ error }}
          </p>
          <button 
            @click="fetchSpots"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors mr-4"
          >
            再試行
          </button>
          <button 
            @click="goHome"
            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          >
            ホームに戻る
          </button>
        </div>

        <!-- Tourist Spots Grid -->
        <div v-else-if="filteredSpots.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <TouristSpotCard 
            v-for="spot in filteredSpots" 
            :key="spot.id"
            :spot="spot"
            :show-prefecture="true"
            :show-tags="true"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            {{ categoryName }}の観光地が見つかりません
          </h3>
          <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300 mb-6">
            現在、このカテゴリの観光地は準備中です
          </p>
          <button 
            @click="goHome"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          >
            ホームに戻る
          </button>
        </div>

      </div>
    </main>
    
    <!-- Footer -->
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'
import { useTouristSpots } from '~/composables/useTouristSpots'

// Page meta
definePageMeta({
  middleware: 'auth'
})

// Reactive variables
const categoryName = ref('')

// 観光地データをAPIから取得
const { spots, loading, error, fetchSpots, getSpotsByCategory } = useTouristSpots()

// Get category from query params
const route = useRoute()


// カテゴリでフィルタリング（APIから取得したデータを使用）
const filteredSpots = computed(() => {
  if (!categoryName.value) return []
  return getSpotsByCategory(categoryName.value)
})


// Set page title dynamically
useHead(() => ({
  title: `${categoryName.value} - Travel Voice`
}))

// Navigation functions

const goHome = () => {
  navigateTo('/')
}

// Load category data on mount
onMounted(async () => {
  try {
    const queryCategory = route.query.name
    if (queryCategory) {
      categoryName.value = queryCategory
      // APIから観光地データを取得
      await fetchSpots()
    } else {
      // If no category specified, redirect to home
      await navigateTo('/')
      return
    }
  } catch (error) {
    console.error('Error loading category:', error)
  }
})

// Watch for route changes
watch(() => route.query.name, (newCategory) => {
  if (newCategory) {
    categoryName.value = newCategory
  }
})
</script>

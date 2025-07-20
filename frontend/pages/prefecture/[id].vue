<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">

    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          :src="getPrefectureImagePath(prefecture?.id)"
          :alt="prefecture?.name || ''"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            {{ prefecture?.name || '読み込み中...' }}
          </h1>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 pb-24">
      <div class="max-w-7xl mx-auto py-6">
        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-3 text-gray-600 dark:text-gray-300">読み込み中...</span>
        </div>

        <!-- Tourist Spots Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <TouristSpotCard 
            v-for="spot in touristSpots" 
            :key="spot.id"
            :spot="spot"
            :show-tags="true"
          />
        </div>

        <!-- Empty State -->
        <div v-if="!loading && touristSpots.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">{{ getEmptyStateEmoji(prefecture?.name) }}</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            {{ prefecture?.name }}の観光地は準備中です
          </h3>
        </div>
      </div>

    </main>
    
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import type { TouristSpot } from '~/types'
import TouristSpotCard from '~/components/TouristSpotCard.vue'
import { usePrefectures } from '~/composables/usePrefectures'

// Page params
const route = useRoute()
const prefectureId = computed(() => Number(route.params.id))

// Page meta
definePageMeta({
  middleware: 'auth'
})

// Reactive variables
const prefecture = ref<any>(null)
const touristSpots = ref<TouristSpot[]>([])
const loading = ref(true)

// Composables
const { fetchPrefectureSpotsById } = usePrefectures()

// 都道府県の背景画像パスを生成
const getPrefectureImagePath = (prefectureId?: number) => {
  if (!prefectureId) return ''
  
  // ID 25, 33, 38, 44, 47は.jpg形式、その他は.jpeg形式
  const jpgIds = [25, 33, 38, 44, 47]
  const extension = jpgIds.includes(prefectureId) ? 'jpg' : 'jpeg'
  
  return `/prefectures_image/${prefectureId}.${extension}`
}

// 空状態の絵文字を取得
const getEmptyStateEmoji = (prefectureName?: string) => {
  const emojiMap: Record<string, string> = {
    '東京都': '🗼',
    '大阪府': '🏯',
    '京都府': '⛩️',
    '北海道': '🐻',
    '沖縄県': '🏝️',
    '福岡県': '🍜',
    '愛知県': '🏭',
    '神奈川県': '🌊',
    '広島県': '🕊️',
    '愛媛県': '🍊'
  }
  
  return emojiMap[prefectureName || ''] || '🏛️'
}

// データを取得
const fetchData = async () => {
  try {
    loading.value = true
    
    const data = await fetchPrefectureSpotsById(prefectureId.value.toString())
    if (data) {
      prefecture.value = data.prefecture
      touristSpots.value = data.spots || []
      
      // Update page title
      useHead({
        title: `${data.prefecture.name} - おうち旅行`
      })
    }
  } catch (error) {
    console.error('Failed to fetch prefecture data:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchData()
})
</script>
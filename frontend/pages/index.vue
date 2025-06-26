<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />
    
    <!-- Main Content -->
    <div class="flex-1 relative z-10 pb-24 pt-16">
      <div class="p-6">
        <div class="max-w-7xl mx-auto text-center">
          <h1 class="text-7xl font-bold text-gray-800 dark:text-white mb-4 tracking-wider text-center transition-colors duration-300">
            <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent font-extrabold">
              {{ t('Travel Voice') }}
            </span>
          </h1>
          <p class="text-xl text-gray-600 dark:text-gray-300 font-medium max-w-2xl mx-auto mb-6 tracking-wide transition-colors duration-300">
            {{ t('音声ガイドで観光を楽しもう') }}
          </p>
        </div>
        
        <div class="max-w-6xl mx-auto">
          <!-- Search Section -->
          <div class="mb-6">
            <!-- Search Input with Suggestions -->
            <div class="relative max-w-md mx-auto">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  @input="onSearchInput"
                  @focus="onInputFocus"
                  @blur="onInputBlur"
                  type="text"
                  placeholder=""
                  class="w-full px-4 py-3 pl-12 pr-20 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300"
                />
                <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                
                <!-- Animated Placeholder -->
                <div 
                  v-if="!searchQuery && !isInputFocused"
                  class="absolute left-12 top-1/2 transform -translate-y-1/2 pointer-events-none h-6 overflow-hidden w-64"
                >
                  <div class="relative h-6">
                    <div 
                      v-for="(spot, index) in placeholderSpots"
                      :key="index"
                      class="absolute inset-0 text-gray-400 dark:text-gray-500 whitespace-nowrap flex items-center transition-all duration-300 ease-out"
                      :style="getPlaceholderStyle(index)"
                    >
                      {{ spot }}
                    </div>
                  </div>
                </div>
                
                <button
                  @click="performSearch"
                  :disabled="!searchQuery.trim()"
                  class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-4 py-1.5 rounded-xl text-sm font-medium transition-colors"
                >
                  {{ t('検索') }}
                </button>
              </div>
              
              <!-- Search Suggestions -->
              <div 
                v-if="showSuggestions && searchSuggestions.length > 0"
                class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"
              >
                <button
                  v-for="suggestion in searchSuggestions"
                  :key="suggestion.id"
                  @mousedown="selectSuggestion(suggestion)"
                  class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                >
                  <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-lg flex-shrink-0 overflow-hidden">
                    <img 
                      :src="suggestion.imageUrl" 
                      :alt="suggestion.name"
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 dark:text-white text-sm">{{ suggestion.name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                      <span>{{ suggestion.prefecture }}</span>
                      <span>•</span>
                      <span>{{ suggestion.category }}</span>
                    </div>
                  </div>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Popular Spots Section -->
          <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-6 transition-colors duration-300">
            <div class="text-center mb-4">
              <div class="flex items-center justify-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg flex items-center justify-center">
                  <Sparkles class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-gray-800 dark:text-white text-xl font-light tracking-wide transition-colors duration-300">{{ t('人気スポット') }}</h3>
              </div>
            </div>
            
            <!-- Carousel Container -->
            <div class="relative">
              <!-- Spots Carousel -->
              <div 
                class="overflow-hidden"
                @mousedown="handleMouseDown"
                @mousemove="handleMouseMove"
                @mouseup="handleMouseUp"
                @mouseleave="handleMouseUp"
                @touchstart="handleTouchStart"
                @touchmove="handleTouchMove"
                @touchend="handleTouchEnd"
              >
                <div 
                  class="flex transition-transform duration-500 ease-in-out"
                  :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
                >
                  <div 
                    v-for="spot in recommendedSpots" 
                    :key="spot.id"
                    class="w-full flex-shrink-0 px-2"
                  >
                    <div 
                      @click="goToSpotDetail(spot.id)"
                      class="bg-white dark:bg-gray-700 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105"
                    >
                      <!-- Horizontal Layout -->
                      <div class="flex h-32">
                        <!-- Spot Image -->
                        <div class="w-48 bg-gradient-to-br from-blue-400 to-purple-500 relative flex-shrink-0">
                          <UnsplashImage 
                            :spot-name="spot.name"
                            :alt="spot.name"
                          >
                            <div class="absolute top-2 right-2">
                              <span class="bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white px-2 py-1 rounded text-xs font-medium">
                                {{ spot.prefecture }}
                              </span>
                            </div>
                          </UnsplashImage>
                        </div>
                        
                        <!-- Spot Info -->
                        <div class="flex-1 p-4 flex flex-col justify-between">
                          <div>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 transition-colors duration-300">
                              {{ spot.name }}
                            </h4>
                            <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 transition-colors duration-300">
                              {{ spot.description }}
                            </p>
                          </div>
                          
                          <!-- Bottom Row -->
                          <div class="flex justify-between items-center mt-2">
                            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded text-xs transition-colors duration-300">
                              {{ spot.category }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Dots Indicator -->
              <div class="flex justify-center mt-4 space-x-2">
                <div 
                  v-for="(spot, index) in recommendedSpots" 
                  :key="index"
                  :class="[
                    'w-2 h-2 rounded-full transition-colors',
                    currentIndex === index 
                      ? 'bg-blue-600' 
                      : 'bg-gray-300 dark:bg-gray-600'
                  ]"
                />
              </div>
            </div>
          </div>
          
          <!-- Prefecture Selection -->
          <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="text-center mb-4">
              <div class="flex items-center justify-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                  <MapPin class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-gray-800 dark:text-white text-xl font-light tracking-wide transition-colors duration-300">{{ t('都道府県から探す') }}</h3>
              </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <button
                v-for="prefecture in mainPrefectures"
                :key="prefecture.name"
                @click="selectPrefecture(prefecture)"
                :class="[
                  'group p-4 rounded-xl border transition-all duration-300',
                  prefecture.available 
                    ? 'bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600 transform hover:scale-105 cursor-pointer'
                    : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed'
                ]"
                :disabled="!prefecture.available"
              >
                <div class="text-3xl mb-2">{{ prefecture.emoji }}</div>
                <h4 :class="[
                  'font-light text-sm transition-colors tracking-wide',
                  prefecture.available 
                    ? 'text-gray-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400'
                    : 'text-gray-500 dark:text-gray-400'
                ]">
                  {{ prefecture.name }}
                </h4>
              </button>
            </div>
            
            <!-- All Prefectures Button -->
            <div class="mt-6 text-center">
              <button
                @click="showPrefectureModal = true"
                class="inline-flex items-center gap-2 px-6 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
              >
                <span>すべて表示</span>
                <span class="text-sm">→</span>
              </button>
            </div>
          </div>
          
          <!-- Category Selection -->
          <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 transition-colors duration-300 mt-8">
            <div class="text-center mb-4">
              <div class="flex items-center justify-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                  <Grid3X3 class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-gray-800 dark:text-white text-xl font-light tracking-wide transition-colors duration-300">{{ t('カテゴリから探す') }}</h3>
              </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <button
                v-for="category in categoryList"
                :key="category.name"
                @click="selectCategory(category)"
                class="group p-4 rounded-xl border bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600 transform hover:scale-105 cursor-pointer transition-all duration-300"
              >
                <div class="text-3xl mb-2">{{ category.emoji }}</div>
                <h4 class="font-light text-sm transition-colors tracking-wide text-gray-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                  {{ category.name }}
                </h4>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Prefecture Modal -->
    <div
      v-if="showPrefectureModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click="closePrefectureModal"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white">都道府県を選択</h2>
          <button
            @click="showPrefectureModal = false"
            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-2xl"
          >
            ×
          </button>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6">
          <div
            v-for="region in prefectureRegions"
            :key="region.name"
            class="mb-8 last:mb-0"
          >
            <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">{{ region.name }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
              <button
                v-for="prefecture in region.prefectures"
                :key="prefecture.name"
                @click="selectPrefectureFromModal(prefecture)"
                :disabled="!prefecture.available"
                :class="[
                  'text-left p-3 rounded-lg border transition-all duration-200',
                  prefecture.available
                    ? 'bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600 text-gray-800 dark:text-white cursor-pointer'
                    : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500 cursor-not-allowed'
                ]"
              >
                <div class="flex items-center gap-2">
                  <span class="text-lg">{{ prefecture.emoji }}</span>
                  <span class="text-sm font-medium">{{ prefecture.name }}</span>
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <AppFooter v-model="activeTab" />
    
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Sparkles, MapPin, Grid3X3, Search } from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'
import { useLanguage } from '~/composables/useLanguage'
import { useTouristSpots } from '~/composables/useTouristSpots'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import UnsplashImage from '~/components/UnsplashImage.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Travel Voice - 音声で楽しむ日本全国の魅力',
  meta: [
    { name: 'description', content: '日本全国47都道府県の観光スポットを音声ガイドで楽しもう。地図から簡単アクセス、プロのガイドで深く学べる日本の歴史と文化。' }
  ]
})

// Auth store
const authStore = useAuthStore()

// Language
const { t } = useLanguage()

// Tourist spots data
const { spots: allSpots, fetchSpots, getRandomSpots, searchSpots } = useTouristSpots()

// Reactive variables
const activeTab = ref('top')
const currentIndex = ref(0)
const recommendedSpots = ref([])
const showPrefectureModal = ref(false)
let carouselInterval = null

// Search related variables
const searchQuery = ref('')
const showSuggestions = ref(false)
const searchSuggestions = ref([])
const isInputFocused = ref(false)

// Placeholder rotation
const placeholderSpots = ['東京スカイツリー', '大阪城', '清水寺', '太宰府天満宮', '名古屋城']
const currentPlaceholderIndex = ref(0)
let placeholderInterval = null

// Drag/swipe functionality
const isDragging = ref(false)
const startPos = ref(0)
const currentPos = ref(0)
const dragThreshold = 50

// データは useTouristSpots コンポーザブルから取得

// Initialize auth state on mount (middleware already handles authentication)
onMounted(async () => {
  // Auth is already initialized by plugin and checked by middleware
  await fetchSpots() // データを取得
  selectRandomSpots()
  startCarousel()
  startPlaceholderRotation()
})

onUnmounted(() => {
  stopCarousel()
  stopPlaceholderRotation()
})

// Select 5 random spots for recommendations
const selectRandomSpots = () => {
  if (allSpots.value.length > 0) {
    recommendedSpots.value = getRandomSpots(5)
  }
}

// Auto carousel functions
const startCarousel = () => {
  carouselInterval = setInterval(() => {
    nextSpot()
  }, 4000) // 4秒ごとに切り替え
}

const stopCarousel = () => {
  if (carouselInterval) {
    clearInterval(carouselInterval)
    carouselInterval = null
  }
}

// Placeholder rotation functions
const startPlaceholderRotation = () => {
  placeholderInterval = setInterval(() => {
    currentPlaceholderIndex.value = (currentPlaceholderIndex.value + 1) % placeholderSpots.length
  }, 3000) // 3秒ごとに切り替え
}

const stopPlaceholderRotation = () => {
  if (placeholderInterval) {
    clearInterval(placeholderInterval)
    placeholderInterval = null
  }
}

// Carousel navigation (infinite loop)
const nextSpot = () => {
  currentIndex.value = (currentIndex.value + 1) % recommendedSpots.value.length
}

// Navigate to spot detail page
const goToSpotDetail = (spotId) => {
  console.log('Navigating to spot:', spotId) // Debug log
  // Only navigate if not dragging
  if (!isDragging.value) {
    stopCarousel() // Stop auto-scroll when navigating
    navigateTo(`/spots/${spotId}`)
  }
}

// Mouse drag handlers
const handleMouseDown = (e) => {
  isDragging.value = true
  startPos.value = e.clientX
  stopCarousel() // Stop auto-scroll during drag
}

const handleMouseMove = (e) => {
  if (!isDragging.value) return
  e.preventDefault()
  currentPos.value = e.clientX
}

const handleMouseUp = () => {
  if (!isDragging.value) return
  
  const deltaX = currentPos.value - startPos.value
  
  if (Math.abs(deltaX) > dragThreshold) {
    if (deltaX > 0) {
      // Dragged right, go to previous
      previousSpot()
    } else {
      // Dragged left, go to next
      nextSpot()
    }
  }
  
  isDragging.value = false
  startCarousel() // Resume auto-scroll
}

// Touch handlers for mobile
const handleTouchStart = (e) => {
  isDragging.value = true
  startPos.value = e.touches[0].clientX
  stopCarousel()
}

const handleTouchMove = (e) => {
  if (!isDragging.value) return
  currentPos.value = e.touches[0].clientX
}

const handleTouchEnd = () => {
  if (!isDragging.value) return
  
  const deltaX = currentPos.value - startPos.value
  
  if (Math.abs(deltaX) > dragThreshold) {
    if (deltaX > 0) {
      previousSpot()
    } else {
      nextSpot()
    }
  }
  
  isDragging.value = false
  startCarousel()
}

// Add previous function for drag navigation
const previousSpot = () => {
  currentIndex.value = currentIndex.value === 0 
    ? recommendedSpots.value.length - 1 
    : currentIndex.value - 1
}

const mainPrefectures = [
  { name: '東京都', emoji: '🗼', available: true },
  { name: '大阪府', emoji: '🏯', available: true },
  { name: '京都府', emoji: '⛩️', available: true },
  { name: '北海道', emoji: '🐄', available: true }
]

// カテゴリリスト
const categoryList = [
  { name: '寺院', emoji: '⛩️' },
  { name: '歴史建造物', emoji: '🏯' },
  { name: '神社', emoji: '🕊️' },
  { name: '展望台', emoji: '🗼' },
  { name: '博物館', emoji: '🏛️' },
  { name: '観光エリア', emoji: '🌆' }
]

// 地域別都道府県データ（人口順）
const prefectureRegions = [
  {
    name: '北海道・東北地方',
    prefectures: [
      { name: '北海道', emoji: '🐄', available: true },
      { name: '宮城県', emoji: '🌾', available: false },
      { name: '福島県', emoji: '🍑', available: true },
      { name: '青森県', emoji: '🍎', available: false },
      { name: '岩手県', emoji: '⛰️', available: false },
      { name: '山形県', emoji: '🍒', available: false },
      { name: '秋田県', emoji: '🌾', available: false }
    ]
  },
  {
    name: '関東地方',
    prefectures: [
      { name: '東京都', emoji: '🗼', available: true },
      { name: '神奈川県', emoji: '🗻', available: false },
      { name: '埼玉県', emoji: '🌸', available: true },
      { name: '千葉県', emoji: '🌊', available: false },
      { name: '茨城県', emoji: '🥔', available: false },
      { name: '栃木県', emoji: '🍓', available: false },
      { name: '群馬県', emoji: '🏔️', available: false }
    ]
  },
  {
    name: '中部地方',
    prefectures: [
      { name: '愛知県', emoji: '🏭', available: true },
      { name: '静岡県', emoji: '🗻', available: false },
      { name: '新潟県', emoji: '🍚', available: true },
      { name: '長野県', emoji: '🏔️', available: false },
      { name: '岐阜県', emoji: '🏯', available: false },
      { name: '山梨県', emoji: '🍇', available: false },
      { name: '富山県', emoji: '🏔️', available: false },
      { name: '石川県', emoji: '🦀', available: false },
      { name: '福井県', emoji: '🦕', available: false }
    ]
  },
  {
    name: '近畿地方',
    prefectures: [
      { name: '大阪府', emoji: '🏯', available: true },
      { name: '兵庫県', emoji: '🦌', available: false },
      { name: '京都府', emoji: '⛩️', available: true },
      { name: '三重県', emoji: '🦐', available: false },
      { name: '滋賀県', emoji: '🏞️', available: false },
      { name: '奈良県', emoji: '🦌', available: false },
      { name: '和歌山県', emoji: '🍊', available: false }
    ]
  },
  {
    name: '中国地方',
    prefectures: [
      { name: '広島県', emoji: '🕊️', available: true },
      { name: '岡山県', emoji: '🍑', available: false },
      { name: '山口県', emoji: '🌉', available: true },
      { name: '島根県', emoji: '⛩️', available: false },
      { name: '鳥取県', emoji: '🏜️', available: false }
    ]
  },
  {
    name: '四国地方',
    prefectures: [
      { name: '愛媛県', emoji: '🍊', available: true },
      { name: '香川県', emoji: '🍜', available: false },
      { name: '徳島県', emoji: '🌀', available: true },
      { name: '高知県', emoji: '🐟', available: false }
    ]
  },
  {
    name: '九州・沖縄地方',
    prefectures: [
      { name: '福岡県', emoji: '🏮', available: true },
      { name: '熊本県', emoji: '🐻', available: false },
      { name: '鹿児島県', emoji: '🌋', available: true },
      { name: '長崎県', emoji: '⛪', available: false },
      { name: '沖縄県', emoji: '🏖️', available: false },
      { name: '大分県', emoji: '♨️', available: false },
      { name: '宮崎県', emoji: '🌺', available: false },
      { name: '佐賀県', emoji: '🏺', available: false }
    ]
  }
]

// 都道府県とルートのマッピング
const prefectureRouteMap = {
  '東京都': '/tokyo',
  '大阪府': '/osaka', 
  '京都府': '/kyoto',
  '北海道': '/hokkaido',
  '愛知県': '/aichi',
  '福岡県': '/fukuoka',
  '広島県': '/hiroshima',
  '愛媛県': '/ehime',
  '福島県': '/fukushima',
  '埼玉県': '/saitama',
  '新潟県': '/niigata',
  '山口県': '/yamaguchi',
  '徳島県': '/tokushima',
  '鹿児島県': '/kagoshima'
}

const selectPrefecture = async (prefecture) => {
  console.log('Prefecture selected:', prefecture.name)
  
  if (!prefecture.available) {
    alert(`${prefecture.name}のガイドは準備中です。しばらくお待ちください。`)
    return
  }

  const route = prefectureRouteMap[prefecture.name]
  if (!route) {
    console.error('Route not found for prefecture:', prefecture.name)
    alert(`${prefecture.name}のページが見つかりません。`)
    return
  }

  console.log(`Navigating to ${prefecture.name} guide...`)
  try {
    await navigateTo(route)
  } catch (error) {
    console.error('Navigation error:', error)
    alert(`${prefecture.name}ガイドページに移動中です...`)
  }
}

// モーダル関連の関数
const closePrefectureModal = (event) => {
  if (event.target === event.currentTarget) {
    showPrefectureModal.value = false
  }
}

const selectPrefectureFromModal = async (prefecture) => {
  showPrefectureModal.value = false
  await selectPrefecture(prefecture)
}

const selectCategory = (category) => {
  console.log('Category selected:', category.name)
  navigateTo(`/category?name=${encodeURIComponent(category.name)}`)
}

// Search functionality
const onSearchInput = () => {
  if (searchQuery.value.trim().length > 0) {
    const results = searchSpots(searchQuery.value)
    searchSuggestions.value = results.slice(0, 5) // 最大5件まで表示
  } else {
    searchSuggestions.value = []
  }
}

const selectSuggestion = (suggestion) => {
  console.log('Suggestion selected:', suggestion.name)
  navigateTo(`/spots/${suggestion.id}`)
}

const onInputFocus = () => {
  isInputFocused.value = true
  showSuggestions.value = true
}

const onInputBlur = () => {
  // 少し遅延させてクリックイベントが発火するようにする
  setTimeout(() => {
    isInputFocused.value = false
    showSuggestions.value = false
  }, 200)
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    console.log('Performing search for:', searchQuery.value)
    navigateTo(`/search?q=${encodeURIComponent(searchQuery.value.trim())}`)
  }
}

// プレースホルダーの位置を計算
const getPlaceholderStyle = (index) => {
  const currentIndex = currentPlaceholderIndex.value
  const diff = index - currentIndex
  
  if (diff === 0) {
    // 現在表示中
    return {
      transform: 'translateY(0)',
      opacity: '1'
    }
  } else if (diff === 1 || (currentIndex === placeholderSpots.length - 1 && index === 0)) {
    // 次に表示される（下から来る）
    return {
      transform: 'translateY(24px)',
      opacity: '0'
    }
  } else if (diff === -1 || (currentIndex === 0 && index === placeholderSpots.length - 1)) {
    // 前に表示されていた（上に去る）
    return {
      transform: 'translateY(-24px)',
      opacity: '0'
    }
  } else {
    // その他（非表示）
    return {
      transform: 'translateY(24px)',
      opacity: '0'
    }
  }
}

</script>


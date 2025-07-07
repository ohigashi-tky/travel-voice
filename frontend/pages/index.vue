<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />
    
    <!-- Main Content (above the image with no gap) -->
    <div class="relative z-10">
        <div class="max-w-6xl mx-auto">
          <!-- Divider -->
          <div class="border-t border-gray-200 dark:border-gray-700 mb-3"></div>
          
          <!-- Navigation Links Section -->
          <div class="mb-3 overflow-x-auto scrollbar-hide">
            <ul class="list-none whitespace-nowrap">
              <li class="inline text-gray-400 dark:text-gray-500 mx-2">|</li>
              <li class="inline">
                <button
                  @click="navigateToEvents"
                  class="text-gray-800 dark:text-white text-sm"
                >
                  イベント情報
                </button>
              </li>
              <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
              <li class="inline">
                <span class="text-gray-400 dark:text-gray-500 cursor-default text-sm">特集</span>
              </li>
              <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
              <li class="inline">
                <span class="text-gray-400 dark:text-gray-500 cursor-default text-sm">穴場スポット</span>
              </li>
              <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
              <li class="inline">
                <span class="text-gray-400 dark:text-gray-500 cursor-default text-sm">天気</span>
              </li>
              <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
              <li class="inline">
                <span class="text-gray-400 dark:text-gray-500 cursor-default text-sm">クイズ</span>
              </li>
              <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
            </ul>
          </div>

        </div>
      
      <!-- Hero Image Fade Slider with Search Overlay -->
      <div class="w-full h-64 md:h-80 lg:h-96 relative overflow-visible mb-6 z-20">
        <!-- Background Images -->
        <div 
          v-for="(image, index) in heroImages" 
          :key="index"
          class="absolute inset-0 transition-opacity duration-[4000ms] ease-in-out"
          :style="{ opacity: currentHeroIndex === index ? 1 : 0 }"
        >
          <img 
            :src="image"
            :alt="`ヒーローイメージ ${index + 1}`"
            class="w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-black bg-opacity-30"></div>
        </div>
        
        <!-- Search Overlay (positioned at bottom of image) -->
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 w-full max-w-md px-4">
          <div class="relative">
            <input
              v-model="searchQuery"
              @input="onSearchInput"
              @focus="onInputFocus"
              @blur="onInputBlur"
              type="text"
              placeholder=""
              class="w-full px-4 py-3 pl-12 pr-20 border border-gray-300 dark:border-gray-600 rounded-lg bg-white/90 dark:bg-gray-700/90 backdrop-blur-sm text-gray-900 dark:text-white transition-all duration-300 focus:outline-none focus:border-gray-300 dark:focus:border-gray-600"
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
            class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-[9999] max-h-60 overflow-y-auto backdrop-blur-sm"
          >
            <button
              v-for="suggestion in searchSuggestions"
              :key="suggestion.id"
              @mousedown="selectSuggestion(suggestion)"
              class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left border-b border-gray-100 dark:border-gray-700 last:border-b-0"
            >
              <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-lg flex-shrink-0 overflow-hidden">
                <PlacePhotoImage 
                  :spot-name="suggestion.name"
                  :place-id="suggestion.place_id"
                  :alt="suggestion.name"
                  image-class="rounded-lg"
                  :width="32"
                  :height="32"
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
      
      <div class="px-4 -mt-3">
        <div class="max-w-6xl mx-auto">
          <!-- Popular Spots Section -->
          <div class="bg-white/40 dark:bg-gray-800/40 backdrop-blur-md border border-white/20 dark:border-gray-700/30 rounded-xl py-2 px-2 mb-4 transition-all duration-300 relative z-10 shadow-lg">
            <div class="text-center mb-2">
              <h3 class="text-gray-800 dark:text-white text-2xl font-bold tracking-wide transition-colors duration-300" style="font-family: 'Inter', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif; font-weight: 700; letter-spacing: 0.05em;">{{ t('人気スポット') }}</h3>
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
                    class="w-full flex-shrink-0 px-1"
                  >
                    <div 
                      @click="goToSpotDetail(spot.id)"
                      class="relative rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105 h-40"
                    >
                      <!-- Full Background Image -->
                      <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-500">
                        <PlacePhotoImage 
                          :spot-name="spot.name"
                          :place-id="spot.place_id"
                          :alt="spot.name"
                          image-class="w-full h-full object-cover"
                        />
                      </div>
                      
                      <!-- Gradient Overlay -->
                      <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                      
                      <!-- Spot Name Badge (top-left) -->
                      <div class="absolute top-3 left-3">
                        <div class="bg-black/10 backdrop-blur-sm rounded px-2 py-1">
                          <h4 class="text-white text-base font-bold">
                            {{ spot.name }}
                          </h4>
                        </div>
                      </div>
                      
                      <!-- Short Description Badge (bottom-right) -->
                      <div class="absolute bottom-2 right-2 max-w-[80%]">
                        <div class="bg-black/10 backdrop-blur-sm rounded px-2 py-1 inline-block">
                          <p class="text-white text-xs font-normal leading-tight text-left whitespace-nowrap">
                            {{ getFirstSentence(spot.description) }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
            </div>
          </div>
          
          <!-- Prefecture Selection -->
          <div class="bg-white/40 dark:bg-gray-800/40 backdrop-blur-md border border-white/20 dark:border-gray-700/30 rounded-xl py-2 px-2 mb-4 transition-all duration-300 relative z-10 shadow-lg">
            <div class="text-center mb-2">
              <h3 class="text-gray-800 dark:text-white text-2xl font-bold tracking-wide transition-colors duration-300" style="font-family: 'Inter', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif; font-weight: 700; letter-spacing: 0.05em;">{{ t('都道府県から探す') }}</h3>
            </div>
            <div class="grid grid-cols-3 gap-3">
              <button
                v-for="prefecture in mainPrefectures"
                :key="prefecture.name"
                @click="selectPrefecture(prefecture)"
                :class="[
                  'group px-2 py-1.5 rounded-lg border transition-all duration-300',
                  prefecture.available 
                    ? 'bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600 transform hover:scale-105 cursor-pointer'
                    : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed'
                ]"
                :disabled="!prefecture.available"
              >
                <div class="text-xl mb-0.5">{{ prefecture.emoji }}</div>
                <h4 :class="[
                  'font-light text-sm transition-colors tracking-wide leading-none',
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
                class="inline-flex items-center gap-1 px-4 py-1.5 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
              >
                <span class="text-sm">すべて表示</span>
                <span class="text-xs">→</span>
              </button>
            </div>
          </div>
          
          <!-- Category Selection -->
          <div class="bg-white/40 dark:bg-gray-800/40 backdrop-blur-md border border-white/20 dark:border-gray-700/30 rounded-xl py-2 px-2 mb-8 transition-all duration-300 relative z-10 shadow-lg">
            <div class="text-center mb-2">
              <h3 class="text-gray-800 dark:text-white text-2xl font-bold tracking-wide transition-colors duration-300" style="font-family: 'Inter', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif; font-weight: 700; letter-spacing: 0.05em;">{{ t('カテゴリから探す') }}</h3>
            </div>
            <div class="grid grid-cols-3 gap-3">
              <button
                v-for="category in categoryList"
                :key="category.name"
                @click="selectCategory(category)"
                class="group px-2 py-1.5 rounded-lg border bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600 transform hover:scale-105 cursor-pointer transition-all duration-300"
              >
                <div class="text-xl mb-0.5">{{ category.emoji }}</div>
                <h4 class="font-light text-sm transition-colors tracking-wide leading-none text-gray-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                  {{ category.name }}
                </h4>
              </button>
            </div>
          </div>
          
          <!-- Footer Links -->
          <div class="text-center mb-4 relative z-10">
            <div class="flex justify-center items-center gap-4">
              <button
                @click="scrollToTop"
                class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
              >
                トップに戻る
              </button>
              <span class="text-xs text-gray-400">|</span>
              <nuxt-link
                to="/terms"
                class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
              >
                利用規約
              </nuxt-link>
            </div>
          </div>
          
          <!-- Copyright -->
          <div class="text-center mb-24 relative z-10">
            <p class="text-xs text-gray-500 dark:text-gray-400">
              © 2025 takuya ohigashi. All rights reserved.
            </p>
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
    <AppFooter v-model="activeTab" :default-open="true" />
    
    <!-- PWA Install Prompt -->
    <PWAInstallPrompt />
    
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
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'
import PWAInstallPrompt from '~/components/PWAInstallPrompt.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'おうち旅行 - 穏やかな音声で日本全国を巡る旅行体験',
  meta: [
    { name: 'description', content: '穏やかな音声で日本全国を巡るおうち旅行。歴史やエピソードを聞きながら、家にいながら本格的な旅行気分を味わえます。' }
  ]
})

// Auth store
const authStore = useAuthStore()

// Language
const { t } = useLanguage()

// Tourist spots data
const { 
  spots: allSpots, 
  fetchSpots, 
  getRandomSpots, 
  searchSpots,
  popularSpots,
  popularSpotsLoading,
  fetchPopularSpots,
  getPopularSpots
} = useTouristSpots()

// Reactive variables
const activeTab = ref('top')
const currentIndex = ref(0)
const recommendedSpots = ref([])
const showPrefectureModal = ref(false)
let carouselInterval = null

// Hero image slider variables
const currentHeroIndex = ref(0)
const heroImages = [
  '/top_image_1.jpg',
  '/top_image_2.jpg', 
  '/top_image_3.jpg'
]
let heroInterval = null

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
  await selectPopularSpots() // AI分析による人気スポット選択
  startCarousel()
  startPlaceholderRotation()
  startHeroSlider()
})

onUnmounted(() => {
  stopCarousel()
  stopPlaceholderRotation()
  stopHeroSlider()
})

// Select popular spots using AI analysis
const selectPopularSpots = async () => {
  try {
    await fetchPopularSpots()
    if (popularSpots.value.length > 0) {
      recommendedSpots.value = popularSpots.value
    } else {
      // Fallback to random spots if AI analysis fails
      console.warn('No popular spots found, using random selection')
      if (allSpots.value.length > 0) {
        recommendedSpots.value = getRandomSpots(5)
      }
    }
  } catch (error) {
    console.error('Error fetching popular spots:', error)
    // Fallback to random spots
    if (allSpots.value.length > 0) {
      recommendedSpots.value = getRandomSpots(5)
    }
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

// Hero slider functions
const startHeroSlider = () => {
  heroInterval = setInterval(() => {
    currentHeroIndex.value = (currentHeroIndex.value + 1) % heroImages.length
  }, 10000) // 10秒ごとに切り替え（非常にゆっくり）
}

const stopHeroSlider = () => {
  if (heroInterval) {
    clearInterval(heroInterval)
    heroInterval = null
  }
}

// Carousel navigation (infinite loop)
const nextSpot = () => {
  currentIndex.value = (currentIndex.value + 1) % recommendedSpots.value.length
}

// Navigate to spot detail page
const goToSpotDetail = (spotId) => {
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
  { name: '東京都', emoji: '🏙️', available: true },
  { name: '大阪府', emoji: '🏯', available: true },
  { name: '京都府', emoji: '⛩️', available: true },
  { name: '北海道', emoji: '🐄', available: true },
  { name: '福岡県', emoji: '🏮', available: true },
  { name: '神奈川県', emoji: '🌊', available: false },
  { name: '愛知県', emoji: '🏭', available: true },
  { name: '埼玉県', emoji: '🌸', available: true },
  { name: '千葉県', emoji: '🏰', available: false },
  { name: '兵庫県', emoji: '🦌', available: false },
  { name: '静岡県', emoji: '🗼', available: false },
  { name: '広島県', emoji: '🕊️', available: true }
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
      { name: '千葉県', emoji: '🏰', available: false },
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

  try {
    await navigateTo(route)
  } catch (error) {
    console.error('Navigation error:', error)
    // Remove alert and use proper error handling
    console.warn(`Failed to navigate to ${prefecture.name}. Attempting fallback...`)
    // Fallback to prefecture page route
    await navigateTo(`/prefecture/${encodeURIComponent(prefecture.name)}`)
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
  navigateTo(`/category?name=${encodeURIComponent(category.name)}`)
}

const navigateToEvents = () => {
  navigateTo('/events')
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

// 説明文の最初の一文（句読点まで）を取得
const getFirstSentence = (description) => {
  if (!description) return ''
  
  // 「。」で最初の一文を取得
  const firstSentence = description.split('。')[0]
  return firstSentence
}

// トップにスクロールする関数
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

</script>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>

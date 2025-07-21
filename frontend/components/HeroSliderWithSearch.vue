<template>
  <!-- Hero Image Fade Slider with Search Overlay -->
  <div class="w-full h-64 md:h-80 lg:h-96 relative overflow-visible mb-6 z-20">
    <!-- Background Images -->
    <div 
      v-for="(image, index) in heroImages" 
      :key="index"
      class="absolute inset-0 transition-opacity duration-[5000ms] ease-in-out"
      :style="{ opacity: currentHeroIndex === index ? 1 : 0 }"
    >
      <!-- 読み込み中プレースホルダー -->
      <div 
        v-if="!imageLoaded[index]" 
        class="loading-placeholder animate-pulse bg-gradient-to-r from-gray-200 to-gray-300 w-full h-full flex items-center justify-center"
      >
        <span class="text-gray-500">読み込み中...</span>
      </div>
      
      <!-- 画像本体 -->
      <img 
        v-show="imageLoaded[index]"
        :src="image"
        :alt="`ヒーローイメージ ${index + 1}`"
        @load="imageLoaded[index] = true"
        @error="handleImageError(index, image)"
        loading="eager"
        decoding="async"
        class="w-full h-full object-cover transition-opacity duration-300"
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
          class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-4 py-1.5 rounded-lg text-sm font-medium transition-colors"
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
              :spot-images="suggestion.spot_images"
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
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Search } from 'lucide-vue-next'
import { useLanguage } from '~/composables/useLanguage'
import { useTouristSpots } from '~/composables/useTouristSpots'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'

// Language
const { t } = useLanguage()

// Tourist spots data for search
const { searchSpots } = useTouristSpots()

// Hero image slider variables
const currentHeroIndex = ref(0)
const heroImages = [
  '/top_image_1.jpg',
  '/top_image_2.jpg', 
  '/top_image_3.jpg'
]
let heroInterval = null

// 画像読み込み状態管理
const imageLoaded = ref({})

// 画像エラーハンドリング
const handleImageError = (index, imagePath) => {
  console.warn(`Failed to load hero image ${index + 1}: ${imagePath}`)
  imageLoaded.value[index] = false
  // 必要に応じてフォールバック画像を設定
}

// Search related variables
const searchQuery = ref('')
const showSuggestions = ref(false)
const searchSuggestions = ref([])
const isInputFocused = ref(false)

// Placeholder rotation
const placeholderSpots = ['東京スカイツリー', '大阪城', '清水寺', '太宰府天満宮', '名古屋城']
const currentPlaceholderIndex = ref(0)
let placeholderInterval = null

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

// Initialize on mount
onMounted(() => {
  startPlaceholderRotation()
  startHeroSlider()
})

onUnmounted(() => {
  stopPlaceholderRotation()
  stopHeroSlider()
})

// Expose functions for parent component
defineExpose({
  startHeroSlider,
  stopHeroSlider,
  startPlaceholderRotation,
  stopPlaceholderRotation
})
</script>
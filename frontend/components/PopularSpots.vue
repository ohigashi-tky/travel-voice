<template>
  <!-- Popular Spots Section -->
  <div class="bg-white/40 dark:bg-gray-800/40 backdrop-blur-md border border-white/20 dark:border-gray-700/30 rounded-lg py-2 px-2 mb-4 transition-all duration-300 relative z-10 shadow-lg">
    <div class="text-center mb-2">
      <h3 class="text-gray-800 dark:text-white text-2xl font-bold tracking-wide transition-colors duration-300" style="font-family: 'Inter', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif; font-weight: 700; letter-spacing: 0.05em;">{{ t('人気スポット') }}</h3>
    </div>
    
    <!-- Carousel Container -->
    <div class="relative">
      <!-- Spots Carousel -->
      <div 
        class="overflow-hidden"
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
                  :spot-images="spot.spot_images"
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
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useLanguage } from '~/composables/useLanguage'
import { useTouristSpots } from '~/composables/useTouristSpots'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'

// Language
const { t } = useLanguage()

// Tourist spots data
const { 
  spots: allSpots, 
  fetchSpots, 
  getRandomSpots, 
  popularSpots,
  popularSpotsLoading,
  fetchPopularSpots,
  getPopularSpots
} = useTouristSpots()

// Reactive variables
const currentIndex = ref(0)
const recommendedSpots = ref([])
let carouselInterval = null

// Touch swipe functionality (mobile only)
const isDragging = ref(false)
const startPos = ref(0)
const currentPos = ref(0)
const dragThreshold = 150 // Higher threshold to prevent accidental swipes
const minimumMoveDistance = 20 // Minimum movement to consider it a drag

// Touch handlers for mobile swipe (no mouse/tap handlers)
const handleTouchStart = (e) => {
  startPos.value = e.touches[0].clientX
  currentPos.value = e.touches[0].clientX
  isDragging.value = false // Don't set to true immediately
}

const handleTouchMove = (e) => {
  if (!startPos.value) return
  
  currentPos.value = e.touches[0].clientX
  const deltaX = Math.abs(currentPos.value - startPos.value)
  
  // Only start dragging if movement is significant
  if (deltaX > minimumMoveDistance && !isDragging.value) {
    isDragging.value = true
    stopCarousel()
  }
}

const handleTouchEnd = () => {
  if (!isDragging.value) {
    // If no significant movement, it's just a tap - restart carousel and return
    startCarousel()
    return
  }
  
  const deltaX = currentPos.value - startPos.value
  
  // Only trigger swipe if movement is significant (over threshold)
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

// Navigation functions for swipe
const previousSpot = () => {
  currentIndex.value = currentIndex.value === 0 
    ? recommendedSpots.value.length - 1 
    : currentIndex.value - 1
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
  }, 5000) // 5秒ごとに切り替え
}

const stopCarousel = () => {
  if (carouselInterval) {
    clearInterval(carouselInterval)
    carouselInterval = null
  }
}

// 説明文の最初の一文（句読点まで）を取得
const getFirstSentence = (description) => {
  if (!description) return ''
  
  // 「。」で最初の一文を取得
  const firstSentence = description.split('。')[0]
  return firstSentence
}

// Initialize on mount
onMounted(async () => {
  await fetchSpots() // データを取得
  await selectPopularSpots() // AI分析による人気スポット選択
  startCarousel()
})

onUnmounted(() => {
  stopCarousel()
})

// Expose functions for parent component
defineExpose({
  selectPopularSpots,
  startCarousel,
  stopCarousel
})
</script>
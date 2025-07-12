<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">観光スポット情報を読み込み中...</h3>
        <p class="text-gray-600 dark:text-gray-300">少々お待ちください</p>
      </div>
    </div>

    <!-- Content when loaded -->
    <div v-else-if="currentSpot && !error">
      <!-- Title Section -->
      <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
          <div class="flex items-center gap-2 mb-2">
            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-md text-xs">
              {{ currentSpot?.category }}
            </span>
            <span class="text-gray-500 dark:text-gray-400 text-xs">{{ currentSpot?.prefecture }}</span>
          </div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-2">
            {{ currentSpot?.name }}
          </h1>
          <p class="text-gray-600 dark:text-gray-300 text-sm">
            {{ currentSpot?.description }}
          </p>
        </div>
      </div>

      <!-- Main Content -->
      <main class="flex-1 relative z-10 pb-24">
        <div class="max-w-4xl mx-auto">
          
          <!-- Images Gallery -->
          <section class="mb-2 px-4">
            <div class="relative">
              <div class="flex gap-4 overflow-x-auto scroll-smooth gallery-scroll">
                <!-- Google Place Photo Main -->
                <div class="flex-shrink-0 w-full aspect-video rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                  <PlacePhotoImage 
                    :spot-name="currentSpot.name"
                    :place-id="currentSpot.place_id"
                    :spot-images="currentSpot.spot_images"
                    :alt="`${currentSpot.name} - メイン画像`"
                    image-class="hover:scale-105 transition-transform duration-300"
                  />
                </div>
                
                <!-- Additional images from spot_images or Google Place Photos -->
                <div 
                  v-for="(photo, index) in additionalImages" 
                  :key="index"
                  class="flex-shrink-0 w-full aspect-video rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300"
                >
                  <img 
                    :src="photo.url || photo.image_url" 
                    :alt="`${currentSpot.name} - 画像 ${index + 2}`"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                  />
                </div>
              </div>
              
              <!-- Image Indicators -->
              <div class="flex justify-center mt-4 space-x-2">
                <button
                  v-for="(_, index) in getTotalImageCount"
                  :key="index"
                  class="w-2 h-2 rounded-full transition-all duration-300"
                  :class="index === 0 ? 'bg-blue-600 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600'"
                />
              </div>
            </div>
          </section>

          <!-- Audio Guide Section -->
          <div class="px-4 sm:px-6 lg:px-8">
            <AudioGuideSimple 
              :spot-id="currentSpot.id"
              :spot-name="currentSpot.name"
              :audio-text="getAudioGuideText()"
            />

            <!-- Combined Overview and History Section -->
            <section class="mb-6">
              <div class="space-y-4">
                <p v-if="currentSpot.overview" class="text-gray-700 dark:text-gray-300 leading-relaxed">
                  {{ currentSpot.overview }}
                </p>
                <p v-if="currentSpot.history" class="text-gray-700 dark:text-gray-300 leading-relaxed">
                  {{ currentSpot.history }}
                </p>
              </div>
            </section>

            <!-- Highlights Section -->
            <section v-if="currentSpot.highlights && currentSpot.highlights.length > 0" class="mb-6">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">見どころ</h3>
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="highlight in currentSpot.highlights" 
                  :key="highlight"
                  class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-3 py-1 rounded-full text-sm"
                >
                  {{ highlight }}
                </span>
              </div>
            </section>
          </div>

          <!-- Google Map Section -->
          <section class="mb-8">
            <div class="px-4 sm:px-6 lg:px-8 mb-6">
              <GoogleMapEmbed 
                :spot-name="currentSpot.name"
                :place-id="currentSpot.place_id"
                :zoom="16"
              />
            </div>
            
            <!-- Unified Access Information Card -->
            <div class="mx-4 sm:mx-6 lg:mx-8 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
              <!-- Tab Navigation -->
              <div class="flex border-b border-gray-200 dark:border-gray-700">
                <button
                  v-for="(tab, index) in availableTabs"
                  :key="tab"
                  @click="setAccessTab(tab)"
                  :class="[
                    'flex-1 px-6 py-4 text-sm font-medium transition-colors duration-200 flex items-center justify-center',
                    accessTab === tab 
                      ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' 
                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                  ]"
                >
                  {{ getTabLabel(tab) }}
                </button>
              </div>

              <!-- Tab Content -->
              <div class="relative overflow-hidden">
                <div 
                  class="flex transition-transform duration-300 ease-out"
                  :style="{ transform: `translateX(-${currentTabIndex * 100}%)` }"
                >
                  <!-- Public Transportation Tab -->
                  <div v-if="hasPublicTransport" class="w-full flex-shrink-0 p-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                      <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span 
                          v-for="(transport, index) in currentSpot.public_transport" 
                          :key="index"
                          class="block mb-2 last:mb-0"
                        >
                          <span class="font-medium">{{ transport.route }}</span>「{{ transport.station }}」<span class="text-red-600 dark:text-red-400 font-medium">{{ transport.time }}</span>
                        </span>
                      </p>
                    </div>
                  </div>

                  <!-- Car Access Tab -->
                  <div v-if="hasCarAccess" class="w-full flex-shrink-0 p-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                      <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span 
                          v-for="(car, index) in currentSpot.car_access" 
                          :key="index"
                          class="block mb-2 last:mb-0"
                        >
                          {{ car.from }}「{{ car.exit }}」<span class="text-red-600 dark:text-red-400 font-medium">{{ car.time }}</span>
                        </span>
                      </p>
                    </div>
                  </div>

                  <!-- Parking Tab -->
                  <div v-if="hasParkingInfo" class="w-full flex-shrink-0 p-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                      <p class="text-sm text-gray-700 dark:text-gray-300" v-html="highlightTimes(currentSpot.parking_info)"></p>
                    </div>
                  </div>

                  <!-- Walking Tab -->
                  <div v-if="hasWalkingInfo" class="w-full flex-shrink-0 p-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                      <p class="text-sm text-gray-700 dark:text-gray-300" v-html="highlightTimes(currentSpot.walking_info)"></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

        </div>
      </main>
    </div>

    <!-- Error State -->
    <div v-else-if="error || (!currentSpot && !isLoading)" class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <div class="w-16 h-16 text-gray-300 mx-auto mb-4">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">
          {{ error || '観光スポットが見つかりませんでした' }}
        </h3>
        <p class="text-gray-600 dark:text-gray-300 mb-6">
          {{ error ? 'データの読み込みに失敗しました。' : '指定されたスポットは存在しないか、削除された可能性があります。' }}
        </p>
        <button 
          @click="goBack"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
        >
          ホームに戻る
        </button>
      </div>
    </div>
    
    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import AudioGuideSimple from '~/components/AudioGuideSimple.vue'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'
import GoogleMapEmbed from '~/components/GoogleMapEmbed.vue'
import { useTouristSpots } from '~/composables/useTouristSpots'
import { useGooglePlacePhotos } from '~/composables/useGooglePlacePhotos'

// Page meta
definePageMeta({
  middleware: 'auth'
})

const route = useRoute()
const config = useRuntimeConfig()
const spotId = computed(() => {
  const id = route.params.id as string
  return parseInt(id)
})

// Set page title
useHead({
  title: '観光地詳細 - Travel Voice'
})

// Reactive variables
const currentSpot = ref<any>(null)
const isLoading = ref(true)
const error = ref<string | null>(null)

// Access tab management
const accessTab = ref('transport')
const availableTabs = ref<string[]>([])
const currentTabIndex = ref(0)

// Tourist spots data
const { spots, fetchSpots, getSpotById } = useTouristSpots()

// Gallery photos management
const { getGalleryPhotos } = useGooglePlacePhotos()
const galleryPhotos = ref([])

// Additional images computed property
const additionalImages = computed(() => {
  // 1. spot_imagesがある場合は最初の画像を除いて使用
  if (currentSpot.value?.spot_images && currentSpot.value.spot_images.length > 1) {
    return currentSpot.value.spot_images.slice(1, 5) // 最初の画像を除く最大4枚
  }
  
  // 2. フォールバック: Google Places APIの画像
  return galleryPhotos.value
})

// Fetch spot details from API
const fetchSpotFromAPI = async (id: number) => {
  try {
    const response = await $fetch(`${config.public.apiBase}/api/travel-spots/${id}`)
    return response.data || response
  } catch (error) {
    return null
  }
}

// Load gallery photos
const loadGalleryPhotos = async () => {
  if (currentSpot.value?.name && currentSpot.value?.place_id) {
    try {
      const photos = await getGalleryPhotos(currentSpot.value.name, currentSpot.value.place_id)
      // Skip the first photo (already shown in main image), take next 4 photos
      galleryPhotos.value = photos.slice(1, 5)
    } catch (error) {
      console.error('Error loading gallery photos:', error)
      galleryPhotos.value = []
    }
  }
}

// Total image count for indicators
const getTotalImageCount = computed(() => {
  // spot_imagesがある場合はその数、ない場合はGoogle Places APIの画像数
  if (currentSpot.value?.spot_images && currentSpot.value.spot_images.length > 0) {
    return currentSpot.value.spot_images.length
  }
  return 1 + galleryPhotos.value.length // 1 main image + gallery images
})

// Tab management functions
const setAccessTab = (tab: string) => {
  accessTab.value = tab
  currentTabIndex.value = availableTabs.value.indexOf(tab)
}

const getTabLabel = (tab: string) => {
  const labels = {
    transport: '電車',
    car: '車',
    parking: '駐車場',
    walking: '徒歩'
  }
  return labels[tab] || tab
}

// Initialize available tabs based on spot data
const initializeTabs = () => {
  if (!currentSpot.value) return
  
  const tabs = []
  if (currentSpot.value.public_transport && currentSpot.value.public_transport.length > 0) {
    tabs.push('transport')
  }
  if (currentSpot.value.car_access && currentSpot.value.car_access.length > 0) {
    tabs.push('car')
  }
  if (currentSpot.value.parking_info) {
    tabs.push('parking')
  }
  if (currentSpot.value.walking_info) {
    tabs.push('walking')
  }
  
  availableTabs.value = tabs
  if (tabs.length > 0) {
    accessTab.value = tabs[0]
    currentTabIndex.value = 0
  }
}

// Computed properties for tab availability
const hasPublicTransport = computed(() => 
  currentSpot.value?.public_transport && currentSpot.value.public_transport.length > 0
)
const hasCarAccess = computed(() => 
  currentSpot.value?.car_access && currentSpot.value.car_access.length > 0
)
const hasParkingInfo = computed(() => 
  currentSpot.value?.parking_info
)
const hasWalkingInfo = computed(() => 
  currentSpot.value?.walking_info
)

// Helper function to highlight time patterns in text
const highlightTimes = (text: string): string => {
  if (!text) return ''
  
  // Pattern to match time expressions like "徒歩3分", "約5分", "30分", etc.
  const timePattern = /(徒歩\d+分|約\d+分|\d+分)/g
  
  return text.replace(timePattern, '<span class="text-red-600 dark:text-red-400 font-medium">$1</span>')
}

// Generate audio guide text for text-to-speech
const getAudioGuideText = () => {
  if (!currentSpot.value) {
    return ''
  }

  const spot = currentSpot.value
  let audioText = ''

  // Introduction
  audioText += `ようこそ、${spot.name}へお越しいただき、ありがとうございます。`
  
  // Overview/Description
  if (spot.overview || spot.description) {
    audioText += `${spot.overview || spot.description}`
  }

  // History section
  if (spot.history) {
    audioText += `\n\n${spot.name}の歴史についてご紹介します。${spot.history}`
  }

  // Highlights section
  if (spot.highlights && spot.highlights.length > 0) {
    audioText += `\n\nこちらの見どころは、${spot.highlights.join('、')}となっております。`
  }

  // Closing
  audioText += `\n\n${spot.name}での素晴らしいひとときをお楽しみください。ありがとうございました。`

  return audioText
}

// Navigation function for error state
const goBack = () => {
  navigateTo('/')
}

// Load spot data on mount
onMounted(async () => {
  try {
    isLoading.value = true
    error.value = null
    
    // First fetch the spots data
    await fetchSpots()
    
    // Get the ID from route params
    const id = route.params.id as string
    
    if (!id) {
      error.value = 'No spot ID provided'
      return
    }
    
    const numericId = parseInt(id)
    console.log('Loading spot detail for URL ID:', id, 'Numeric ID:', numericId)
    
    // travel_spots APIから直接取得
    let spot = null
    
    // travel_spots API優先
    spot = await fetchSpotFromAPI(numericId)
    console.log('API response for spot ID', numericId, ':', spot)
    
    // フォールバック: composableからも確認（削除予定）
    if (!spot) {
      spot = getSpotById(numericId)
      console.log('useTouristSpots fallback search for ID', numericId, ':', spot)
    }
    
    if (spot) {
      currentSpot.value = spot
      console.log('Setting currentSpot for URL ID:', numericId, 'Spot data:', { id: spot.id, name: spot.name, description: spot.description })
      
      // Load gallery photos from Google Places API
      await loadGalleryPhotos()
      
      // Initialize access tabs
      initializeTabs()
      
      // Update page title
      useHead({
        title: `${spot.name} - Travel Voice`
      })
    } else {
      error.value = 'Spot not found'
    }
  } catch (err) {
    console.error('Error loading spot:', err)
    error.value = 'Failed to load spot data'
  } finally {
    isLoading.value = false
  }
})

// Watch for route changes
watch(() => route.params.id, async (newId) => {
  if (newId) {
    const numericId = parseInt(newId as string)
    console.log('Route changed to ID:', newId, 'Numeric ID:', numericId)
    
    // travel_spots API優先
    let spot = await fetchSpotFromAPI(numericId)
    console.log('Watch: API search for ID', numericId, ':', spot)
    
    if (!spot) {
      spot = getSpotById(numericId)
      console.log('Watch: useTouristSpots fallback for ID', numericId, ':', spot)
    }
    
    if (spot) {
      currentSpot.value = spot
      console.log('Watch: Setting currentSpot:', { id: spot.id, name: spot.name })
      // Load gallery photos for the new spot
      await loadGalleryPhotos()
      // Re-initialize tabs
      initializeTabs()
    }
  }
})
</script>

<style scoped>
.gallery-scroll {
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.gallery-scroll::-webkit-scrollbar {
  display: none;
}
</style>
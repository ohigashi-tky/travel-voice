<template>
  <div class="relative h-full w-full">
    <!-- Loading state -->
    <div v-if="loading" class="w-full h-full bg-gray-200 dark:bg-gray-700 animate-pulse rounded-lg flex items-center justify-center">
      <div class="text-gray-400 dark:text-gray-500 text-sm">画像読み込み中...</div>
    </div>

    <!-- Image -->
    <img 
      v-if="!loading && !error && imageUrl"
      :src="imageUrl" 
      :alt="alt"
      class="w-full h-full object-cover"
      :class="imageClass"
      loading="lazy"
      @error="handleImageError"
    />
    
    <!-- Attribution removed -->
    
    <!-- Error/No photo state -->
    <div v-if="!loading && (error || !imageUrl)" class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
      <div class="text-center text-gray-500 dark:text-gray-400 p-4">
        <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <div class="text-sm font-medium mb-1">{{ spotName }}</div>
        <div class="text-xs opacity-75">写真なし</div>
      </div>
    </div>
    
    <!-- Slot for overlays -->
    <slot />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useGooglePlacePhotos } from '~/composables/useGooglePlacePhotos'

interface Props {
  spotName: string
  placeId?: string
  alt?: string
  imageClass?: string
  showAttribution?: boolean
  fallbackUrl?: string
  width?: number
  height?: number
  spotImages?: Array<{
    id: number
    image_url: string
    thumbnail_url?: string
    width?: number
    height?: number
  }>
}

const props = withDefaults(defineProps<Props>(), {
  alt: '',
  imageClass: '',
  showAttribution: false,
  fallbackUrl: '',
  width: 800,
  height: 600
})

const { 
  getPhotosWithFallback, 
  getFallbackImages 
} = useGooglePlacePhotos()

const imageUrl = ref(props.fallbackUrl)
const loading = ref(true)
const error = ref(false)

const handleImageError = (event: Event) => {
  error.value = true
  // No fallback - show error state
}

const loadImage = async () => {
  try {
    loading.value = true
    error.value = false
    
    // 1. 最優先: travel_spot_imagesからの画像を使用
    if (props.spotImages && props.spotImages.length > 0) {
      imageUrl.value = props.spotImages[0].image_url
      error.value = false
      loading.value = false
      return
    }
    
    // 2. フォールバック: Google Places APIを使用
    if (!props.spotName && !props.placeId) {
      error.value = true
      loading.value = false
      return
    }
    
    const photos = await getPhotosWithFallback(props.spotName, props.placeId)
    
    if (photos.length > 0) {
      imageUrl.value = photos[0].url
      error.value = false
    } else {
      // No photos available - show error state
      error.value = true
    }
  } catch (err) {
    error.value = true
  } finally {
    loading.value = false
  }
}

// Load image on mount
onMounted(() => {
  loadImage()
})

// Watch for prop changes
watch([() => props.spotName, () => props.placeId, () => props.spotImages], () => {
  if (props.spotName || props.spotImages) {
    loadImage()
  }
}, { immediate: false })
</script>
<template>
  <div 
    @click="goToSpotDetail(spot.id)"
    class="bg-gray-50 dark:bg-gray-800 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 dark:border-gray-700 cursor-pointer group"
  >
    <!-- Spot Image -->
    <div class="h-48 bg-gradient-to-br from-cyan-400 to-blue-500 relative overflow-hidden">
      <PlacePhotoImage 
        :spot-name="spot.name"
        :place-id="spot.place_id"
        :spot-images="spot.spot_images"
        :alt="spot.name"
        image-class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      >
        <!-- Category Tag (right) -->
        <div class="absolute top-3 right-3">
          <span class="bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white px-2 py-1 rounded-lg text-xs font-medium">
            {{ showPrefecture ? spot.prefecture : spot.category }}
          </span>
        </div>
        
        <!-- Additional Tag (left) - only show if both category and prefecture should be shown -->
        <div v-if="showBothTags" class="absolute top-3 left-3">
          <span class="bg-blue-500/90 text-white px-2 py-1 rounded-lg text-xs font-medium">
            {{ spot.category }}
          </span>
        </div>
      </PlacePhotoImage>
    </div>
    
    <!-- Spot Info -->
    <div class="p-4">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
        {{ spot.name }}
      </h3>
      <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2 transition-colors duration-300">
        {{ spot.description }}
      </p>
      
      <!-- Tags (prefecture pages) or Audio Guide Indicator (category pages) -->
      <div v-if="showTags && spotTags.length > 0" class="flex flex-wrap gap-1 mb-3">
        <span 
          v-for="tag in spotTags" 
          :key="tag"
          :class="getTagColorClasses(tag)"
          class="px-2 py-1 rounded-md text-xs transition-colors duration-300"
        >
          {{ tag }}
        </span>
      </div>
      
      <!-- Audio Guide Indicator for category pages -->
      <div v-if="!showTags" class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400">
          <span>🎧</span>
          <span>音声ガイド対応</span>
        </div>
        <div class="text-gray-400 text-xs">
          詳細を見る →
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { TouristSpot } from '~/types'
import { getSpotTags, getTagColorClasses } from '~/constants/touristSpotTags'

interface Props {
  spot: TouristSpot
  showPrefecture?: boolean  // true: show prefecture in right tag, false: show category
  showBothTags?: boolean   // true: show both prefecture and category
  showTags?: boolean       // true: show spot tags (for prefecture pages)
}

const props = withDefaults(defineProps<Props>(), {
  showPrefecture: false,
  showBothTags: false,
  showTags: true
})

const goToSpotDetail = (spotId: number) => {
  // Special case for 銀座 (ID 4) - redirect to dedicated page
  if (spotId === 4) {
    navigateTo('/ginza')
  } else {
    navigateTo(`/spots/${spotId}`)
  }
}

// 観光地のタグを計算プロパティで取得
const spotTags = computed(() => {
  return getSpotTags(props.spot.name, props.spot.category)
})
</script>
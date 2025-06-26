<template>
  <div class="quick-map-container w-full h-80 rounded-lg shadow-md">
    <div class="w-full h-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-700 dark:to-gray-800 rounded-lg flex flex-col items-center justify-center p-6">
      <div class="text-center max-w-sm">
        <div class="text-4xl mb-4">📍</div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">{{ spotName }}</h3>
        <div class="text-sm text-gray-600 dark:text-gray-300 mb-4">
          <div>緯度: {{ coordinates.lat.toFixed(6) }}</div>
          <div>経度: {{ coordinates.lng.toFixed(6) }}</div>
        </div>
        
        <div class="space-y-2 w-full">
          <button 
            @click="openGoogleMaps"
            class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
          >
            <span>🗺️</span>
            Google Mapsで表示
          </button>
          
          <button 
            @click="openAppleMaps"
            class="w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
          >
            <span>🍎</span>
            Apple Mapsで表示
          </button>
        </div>
        
        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
          地図アプリで詳細な位置と周辺情報を確認
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  spotName: string
  zoom?: number
}

const props = withDefaults(defineProps<Props>(), {
  zoom: 16
})

// 観光地の座標データ
const spotCoordinates: Record<string, { lat: number; lng: number }> = {
  '東京スカイツリー': { lat: 35.7101, lng: 139.8107 },
  '浅草寺': { lat: 35.7148, lng: 139.7967 },
  '明治神宮': { lat: 35.6762, lng: 139.6993 },
  '大阪城': { lat: 34.6873, lng: 135.5262 },
  '通天閣': { lat: 34.6523, lng: 135.5061 },
  '海遊館': { lat: 34.6547, lng: 135.4281 },
  '清水寺': { lat: 34.9949, lng: 135.7851 },
  '金閣寺': { lat: 35.0394, lng: 135.7292 },
  '伏見稲荷大社': { lat: 34.9671, lng: 135.7727 },
  '札幌時計台': { lat: 43.0642, lng: 141.3469 },
  '函館山': { lat: 41.7640, lng: 140.6982 },
  '小樽運河': { lat: 43.1907, lng: 140.9947 },
  '名古屋城': { lat: 35.1856, lng: 136.8997 },
  '熱田神宮': { lat: 35.1281, lng: 136.9075 },
  'トヨタ産業技術記念館': { lat: 35.1954, lng: 136.8805 },
  '原爆ドーム': { lat: 34.3955, lng: 132.4536 },
  '厳島神社': { lat: 34.2969, lng: 132.3196 },
  '広島城': { lat: 34.4026, lng: 132.4594 },
  '太宰府天満宮': { lat: 33.5217, lng: 130.5332 },
  '福岡城跡': { lat: 33.5837, lng: 130.3791 },
  '博多駅': { lat: 33.5904, lng: 130.4017 }
}

const coordinates = computed(() => {
  return spotCoordinates[props.spotName] || { lat: 35.6762, lng: 139.6993 }
})

const openGoogleMaps = () => {
  const url = `https://www.google.com/maps?q=${coordinates.value.lat},${coordinates.value.lng}&z=${props.zoom}`
  window.open(url, '_blank')
}

const openAppleMaps = () => {
  const url = `https://maps.apple.com/?q=${coordinates.value.lat},${coordinates.value.lng}&z=${props.zoom}`
  window.open(url, '_blank')
}
</script>

<style scoped>
.quick-map-container {
  width: 100%;
}
</style>
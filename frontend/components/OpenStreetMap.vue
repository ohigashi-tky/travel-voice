<template>
  <div class="osm-map-container">
    <ClientOnly>
      <div 
        ref="mapElement" 
        class="w-full h-80 rounded-lg shadow-md bg-gray-100 dark:bg-gray-700"
        style="min-height: 320px;"
      ></div>
      <template #fallback>
        <div class="w-full h-80 rounded-lg shadow-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
          <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
            <p class="text-gray-600 dark:text-gray-400 text-sm">地図を読み込み中...</p>
          </div>
        </div>
      </template>
    </ClientOnly>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

interface Props {
  spotName: string
  zoom?: number
}

const props = withDefaults(defineProps<Props>(), {
  zoom: 16
})

const mapElement = ref<HTMLElement>()

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

const initMap = async () => {
  if (!import.meta.client || !mapElement.value) return

  const coordinates = spotCoordinates[props.spotName] || { lat: 35.6762, lng: 139.6993 }
  
  console.log('📍 Loading OpenStreetMap for:', props.spotName)
  console.log('📍 Coordinates:', coordinates)

  try {
    // OpenStreetMapのiframe埋め込み
    const osmUrl = `https://www.openstreetmap.org/export/embed.html?bbox=${coordinates.lng-0.01},${coordinates.lat-0.01},${coordinates.lng+0.01},${coordinates.lat+0.01}&layer=mapnik&marker=${coordinates.lat},${coordinates.lng}`
    
    mapElement.value.innerHTML = `
      <div class="relative w-full h-full">
        <iframe
          width="100%"
          height="100%"
          style="border:0; border-radius: 8px;"
          src="${osmUrl}"
          allowfullscreen>
        </iframe>
        <div class="absolute top-2 left-2 bg-white/90 dark:bg-gray-800/90 px-2 py-1 rounded text-xs">
          <span class="text-red-600">●</span> ${props.spotName}
        </div>
        <div class="absolute bottom-2 right-2">
          <button 
            onclick="window.open('https://www.google.com/maps?q=${coordinates.lat},${coordinates.lng}', '_blank')"
            class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors"
          >
            Google Mapsで開く
          </button>
        </div>
      </div>
    `
    
    console.log('✅ OpenStreetMap loaded successfully')
    
  } catch (error) {
    console.error('❌ Map loading failed:', error)
    
    // フォールバック: 静的表示
    mapElement.value.innerHTML = `
      <div class="w-full h-full bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
        <div class="text-center p-4">
          <div class="text-2xl mb-2">📍</div>
          <p class="text-gray-700 dark:text-gray-300 font-medium mb-1">${props.spotName}</p>
          <p class="text-gray-500 dark:text-gray-400 text-sm mb-3">
            緯度: ${coordinates.lat.toFixed(4)}<br>
            経度: ${coordinates.lng.toFixed(4)}
          </p>
          <div class="space-y-2">
            <button 
              onclick="window.open('https://www.google.com/maps?q=${coordinates.lat},${coordinates.lng}', '_blank')"
              class="block w-full px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition-colors"
            >
              Google Mapsで開く
            </button>
            <button 
              onclick="window.open('https://www.openstreetmap.org/?mlat=${coordinates.lat}&mlon=${coordinates.lng}&zoom=${props.zoom}', '_blank')"
              class="block w-full px-3 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors"
            >
              OpenStreetMapで開く
            </button>
          </div>
        </div>
      </div>
    `
  }
}

onMounted(() => {
  initMap()
})
</script>

<style scoped>
.osm-map-container {
  width: 100%;
}
</style>
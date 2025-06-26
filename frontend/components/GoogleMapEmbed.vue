<template>
  <div class="map-embed-container w-full h-80 rounded-lg shadow-md overflow-hidden bg-gray-100">
    <!-- Place モードで試す -->
    <iframe
      v-if="usePlace"
      :src="placeMapUrl"
      width="100%"
      height="100%"
      style="border:0;"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      class="rounded-lg"
      @error="handleError"
    ></iframe>
    
    <!-- View モードにフォールバック -->
    <iframe
      v-else
      :src="viewMapUrl"
      width="100%"
      height="100%"
      style="border:0;"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      class="rounded-lg"
      @error="handleError"
    ></iframe>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

interface Props {
  spotName: string
  zoom?: number
}

const props = withDefaults(defineProps<Props>(), {
  zoom: 16
})

const usePlace = ref(true)

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
  '博多駅': { lat: 33.5904, lng: 130.4017 },
  // 愛媛県
  '道後温泉': { lat: 33.8517, lng: 132.7862 },
  '松山城': { lat: 33.8460, lng: 132.7655 },
  '今治城': { lat: 34.0658, lng: 133.0055 },
  '内子町': { lat: 33.5469, lng: 132.6516 },
  '石鎚山': { lat: 33.7663, lng: 133.1117 },
  'しまなみ海道': { lat: 34.2303, lng: 133.1956 }
}

const coordinates = computed(() => {
  return spotCoordinates[props.spotName] || { lat: 35.6762, lng: 139.6993 }
})

const apiKey = computed(() => {
  const config = useRuntimeConfig()
  return config.public.googleMapsApiKey || 'AIzaSyDBTzeSepixQFP2y2pQcNciOSj8kYlDzh4'
})

// Place モードのURL
const placeMapUrl = computed(() => {
  const query = encodeURIComponent(props.spotName)
  return `https://www.google.com/maps/embed/v1/place?key=${apiKey.value}&q=${query}&zoom=${props.zoom}`
})

// View モードのURL
const viewMapUrl = computed(() => {
  return `https://www.google.com/maps/embed/v1/view?key=${apiKey.value}&center=${coordinates.value.lat},${coordinates.value.lng}&zoom=${props.zoom}`
})

const handleError = () => {
  console.log('Map loading error, switching to view mode')
  usePlace.value = false
}

// デバッグ用
console.log('🗺️ Loading map for:', props.spotName)
console.log('📍 Coordinates:', coordinates.value)
console.log('🔑 API Key available:', !!apiKey.value)
console.log('🌐 Place URL:', placeMapUrl.value)
console.log('🌐 View URL:', viewMapUrl.value)
</script>

<style scoped>
.map-embed-container {
  background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
}
</style>
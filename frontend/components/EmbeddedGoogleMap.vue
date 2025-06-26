<template>
  <div class="embedded-map-container w-full h-80 rounded-lg shadow-md overflow-hidden">
    <iframe
      :src="mapUrl"
      width="100%"
      height="100%"
      style="border:0;"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      class="rounded-lg"
    ></iframe>
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

const mapUrl = computed(() => {
  const config = useRuntimeConfig()
  const apiKey = config.public.googleMapsApiKey || 'AIzaSyDBTzeSepixQFP2y2pQcNciOSj8kYlDzh4'
  
  // Google Maps Embed API using view mode
  return `https://www.google.com/maps/embed/v1/view?key=${apiKey}&center=${coordinates.value.lat},${coordinates.value.lng}&zoom=${props.zoom}`
})
</script>

<style scoped>
.embedded-map-container {
  background: #f3f4f6;
}
</style>
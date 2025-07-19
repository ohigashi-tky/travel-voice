<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Back Button -->
    <BackButton />

    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/13.jpeg" 
          alt="東京都"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            東京都
          </h1>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 pb-24">
      <div class="max-w-7xl mx-auto py-6">
        <!-- Tourist Spots Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <TouristSpotCard 
            v-for="spot in touristSpots" 
            :key="spot.id"
            :spot="spot"
            :show-tags="true"
          />
        </div>

        <!-- Empty State -->
        <div v-if="touristSpots.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🗼</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            東京の観光スポットを準備中
          </h3>
          <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">
            魅力的な観光地の音声ガイドをお楽しみに！
          </p>
        </div>
      </div>

    </main>
    
    <!-- Footer -->

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { TouristSpot } from '~/types'
import AppHeader from '~/components/AppHeader.vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Tokyo Guide - Discover Japan'
})

// Reactive variables
const activeTab = ref('top')

// Removed useImageGeneration composable

const touristSpots = ref<TouristSpot[]>([])









const goToSpotDetail = (spotId: number) => {
  navigateTo(`/spots/${spotId}`)
}


// Load mock tourist spots data
onMounted(() => {
  // Mock data for Tokyo tourist spots
  touristSpots.value = [
    {
      id: 1,
      name: '東京スカイツリー',
      description: '高さ634mの世界最高クラスの電波塔。展望デッキからは東京の絶景を一望できます。',
      category: '展望台',
      prefecture: '東京都',
      address: '東京都墨田区押上1-1-2',
      latitude: 35.7101,
      longitude: 139.8107,
      place_id: 'ChIJ35ov0dCOGGARKvdDH7NPHX0',
      imageUrl: '',
      openingHours: '8:00-22:00',
      admissionFee: '大人2100円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 2,
      name: '浅草寺',
      description: '東京最古の寺院。雷門と仲見世通りで有名な東京を代表する観光地です。',
      category: '寺院',
      prefecture: '東京都',
      address: '東京都台東区浅草2-3-1',
      latitude: 35.7148,
      longitude: 139.7967,
      place_id: 'ChIJ8T1GpMGOGGARDYGSgpooDWw',
      imageUrl: '',
      openingHours: '6:00-17:00',
      admissionFee: '無料',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 3,
      name: '明治神宮',
      description: '明治天皇と昭憲皇太后を祀る神社。都心にありながら豊かな森に囲まれた神聖な空間です。',
      category: '神社',
      prefecture: '東京都',
      address: '東京都渋谷区代々木神園町1-1',
      latitude: 35.6762,
      longitude: 139.6993,
      place_id: 'ChIJ5SZMmreMGGARcz8QSTiJyo8',
      imageUrl: '',
      openingHours: '5:00-18:00',
      admissionFee: '無料',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 4,
      name: '銀座',
      description: '高級ブランド店が立ち並ぶ東京の代表的なショッピングエリア。洗練された大人の街として世界的に有名です。',
      category: '観光エリア',
      prefecture: '東京都',
      address: '東京都中央区銀座',
      latitude: 35.6762,
      longitude: 139.7631,
      place_id: 'ChIJu2-DAeeLGGARUZipC7OFRmA',
      imageUrl: '',
      openingHours: '店舗により異なる',
      admissionFee: '無料（店舗により異なる）',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 5,
      name: '上野公園',
      description: '桜の名所として有名な都市公園。上野動物園や博物館、美術館が集まる文化の拠点です。',
      category: '公園',
      prefecture: '東京都',
      address: '東京都台東区上野公園',
      latitude: 35.7153,
      longitude: 139.7734,
      place_id: 'ChIJw2qQRZuOGGARWmROEiM2y7E',
      imageUrl: '',
      openingHours: '24時間（施設により異なる）',
      admissionFee: '無料（施設により異なる）',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 6,
      name: '渋谷スクランブル交差点',
      description: '世界で最も有名な交差点の一つ。一度に3000人もの人が行き交う東京のシンボル的な光景です。',
      category: '観光エリア',
      prefecture: '東京都',
      address: '東京都渋谷区道玄坂2丁目',
      latitude: 35.6598,
      longitude: 139.7006,
      place_id: 'ChIJK9EM68qLGGARacmu4KJj5SA',
      imageUrl: '',
      openingHours: '24時間',
      admissionFee: '無料',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  ]
})
</script>

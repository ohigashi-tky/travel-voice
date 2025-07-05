<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Back Button -->
    <BackButton />

    <!-- Page Title -->
    <div class="bg-white dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-wide transition-colors duration-300">
            <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent">
              東京都
            </span>
          </h1>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 pb-24">
      <div class="max-w-7xl mx-auto py-6">
        <!-- Tourist Spots Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="spot in touristSpots" 
            :key="spot.id"
            @click="goToSpotDetail(spot.id)"
            class="bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 dark:border-gray-700 cursor-pointer"
          >
            <!-- Spot Image -->
            <div class="h-48 bg-gradient-to-br from-cyan-400 to-blue-500 relative">
              <PlacePhotoImage 
                :spot-name="spot.name"
                :place-id="spot.place_id"
                :alt="spot.name"
                image-class="w-full h-full object-cover"
              >
                <div class="absolute top-3 right-3">
                  <span class="bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white px-2 py-1 rounded-lg text-xs font-medium">
                    {{ spot.category }}
                  </span>
                </div>
              </PlacePhotoImage>
            </div>
            
            <!-- Spot Info -->
            <div class="p-4">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 transition-colors duration-300">
                {{ spot.name }}
              </h3>
              <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2 transition-colors duration-300">
                {{ spot.description }}
              </p>
              
              <!-- Tags -->
              <div class="flex flex-wrap gap-1 mb-3">
                <span 
                  v-for="tag in getSpotTags(spot)" 
                  :key="tag"
                  class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-md text-xs transition-colors duration-300"
                >
                  {{ tag }}
                </span>
              </div>
              
            </div>
          </div>
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
    <AppFooter v-model="activeTab" />

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { TouristSpot } from '~/types'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'

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






const getSpotTags = (spot: TouristSpot) => {
  const tags = []
  if (spot.name.includes('スカイツリー')) tags.push('展望台', '現代建築')
  if (spot.name.includes('浅草寺')) tags.push('歴史', '寺院')
  if (spot.name.includes('明治神宮')) tags.push('神社', '自然')
  if (spot.name.includes('大阪城')) tags.push('歴史', '桜の名所')
  if (spot.name.includes('通天閣')) tags.push('展望台', 'レトロ')
  if (spot.name.includes('海遊館')) tags.push('水族館', 'ファミリー')
  if (spot.name.includes('銀座')) tags.push('ショッピング', '高級エリア')
  if (spot.name.includes('渋谷')) tags.push('若者文化', '繁華街')
  if (spot.name.includes('新宿')) tags.push('ビジネス街', '娯楽')
  if (spot.name.includes('上野')) tags.push('美術館', '公園')
  return tags.slice(0, 3)
}



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
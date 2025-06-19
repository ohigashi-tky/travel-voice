<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Page Title -->
    <div class="bg-white dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-wide transition-colors duration-300 text-center">
          <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent">
            東京都
          </span>
        </h1>
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
            class="bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 dark:border-gray-700"
          >
            <!-- Spot Image -->
            <div class="h-48 bg-gradient-to-br from-cyan-400 to-blue-500 relative">
              <img 
                :src="generateSpotImage(spot.name, spot.category)" 
                :alt="spot.name"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <div class="absolute top-3 right-3">
                <span class="bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white px-2 py-1 rounded-lg text-xs font-medium">
                  {{ spot.category }}
                </span>
              </div>
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
              
              <!-- Audio Guide Button -->
              <button 
                @click="playAudioGuide(spot)"
                class="w-full bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 text-white py-2 px-4 rounded-lg font-medium text-sm hover:from-cyan-700 hover:via-blue-700 hover:to-purple-700 transition-all duration-300 flex items-center justify-center gap-2"
              >
                <Headphones class="w-4 h-4" />
                音声ガイドを聞く
              </button>
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

      <!-- Audio Player -->
      <AudioGuidePlayer 
        :audio-guide="currentAudioGuide"
        :spot-name="currentSpot?.name || null"
        :is-visible="!!currentAudioGuide"
        @close="closePlayer"
      />
    </main>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Headphones } from 'lucide-vue-next'
import type { TouristSpot, AudioGuide } from '~/types'

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
const currentAudioGuide = ref<AudioGuide | null>(null)
const currentSpot = ref<TouristSpot | null>(null)


const generateSpotImage = (spotName: string, category: string) => {
  // 観光地ごとの実際の画像URLを返す
  const imageMap: Record<string, string> = {
    '東京スカイツリー': 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=400&h=300&fit=crop&auto=format',
    '浅草寺': 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=400&h=300&fit=crop&auto=format',
    '明治神宮': 'https://images.unsplash.com/photo-1490650034439-fd184c3c86a5?w=400&h=300&fit=crop&auto=format'
  }
  
  return imageMap[spotName] || `https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=300&fit=crop&auto=format`
}

const getSpotTags = (spot: TouristSpot) => {
  const tags = []
  if (spot.name.includes('スカイツリー')) tags.push('展望台', '現代建築')
  if (spot.name.includes('浅草寺')) tags.push('歴史', '寺院')
  if (spot.name.includes('銀座')) tags.push('ショッピング', '高級エリア')
  if (spot.name.includes('渋谷')) tags.push('若者文化', '繁華街')
  if (spot.name.includes('新宿')) tags.push('ビジネス街', '娯楽')
  if (spot.name.includes('上野')) tags.push('美術館', '公園')
  return tags.slice(0, 3)
}

const playAudioGuide = async (spot: TouristSpot) => {
  currentSpot.value = spot
  
  // Mock audio guide data
  currentAudioGuide.value = {
    id: Date.now(),
    title: `${spot.name}の音声ガイド`,
    description: `${spot.name}の歴史と魅力について詳しく解説します。`,
    audioUrl: '/audio/sample.mp3', // This would be a real audio file
    duration: 180, // 3 minutes
    transcript: `${spot.name}は東京の代表的な観光スポットの一つです...`,
    touristSpotId: spot.id,
    language: 'ja',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  }
}

const closePlayer = () => {
  currentAudioGuide.value = null
  currentSpot.value = null
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
      imageUrl: '',
      openingHours: '5:00-18:00',
      admissionFee: '無料',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  ]
})
</script>
<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Page Title -->
    <div class="bg-white dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center relative">
          <button 
            @click="goHome"
            class="absolute left-0 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors duration-300 group"
          >
            <ArrowLeft class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" />
            <span class="text-sm font-medium">戻る</span>
          </button>
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
                @click.stop="playAudioGuide(spot)"
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
import { Headphones, ArrowLeft } from 'lucide-vue-next'
import type { TouristSpot, AudioGuide } from '~/types'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import AudioGuidePlayer from '~/components/AudioGuidePlayer.vue'

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
    '浅草寺': 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=400&h=300&fit=crop&auto=format',
    '明治神宮': 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format',
    '大阪城': 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format',
    '通天閣': 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=300&fit=crop&auto=format',
    '海遊館': 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=400&h=300&fit=crop&auto=format',
    '清水寺': 'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=400&h=300&fit=crop&auto=format',
    '金閣寺': 'https://images.unsplash.com/photo-1478436127897-769e1b3f0f36?w=400&h=300&fit=crop&auto=format',
    '伏見稲荷大社': 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=400&h=300&fit=crop&auto=format',
    '札幌時計台': 'https://images.unsplash.com/photo-1607619662634-3ac55ec0e216?w=400&h=300&fit=crop&auto=format',
    '函館山': 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&h=300&fit=crop&auto=format',
    '小樽運河': 'https://images.unsplash.com/photo-1494522855154-9297ac14b55f?w=400&h=300&fit=crop&auto=format'
  }
  
  return imageMap[spotName] || `https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=300&fit=crop&auto=format`
}

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

const goHome = () => {
  navigateTo('/')
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
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
            class="absolute left-0 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 group"
          >
            <ArrowLeft class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" />
            <span class="text-sm font-medium">戻る</span>
          </button>
          <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-wide transition-colors duration-300">
            <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
              北海道
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
            class="bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 dark:border-gray-700"
          >
            <!-- Spot Image -->
            <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-500 relative">
              <img 
                :src="generateSpotImage(spot.name, spot.category)" 
                :alt="spot.name"
                class="w-full h-full object-cover"
                loading="lazy"
                @error="$event.target.style.display = 'none'"
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
                class="w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white py-2 px-4 rounded-lg font-medium text-sm hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 transition-all duration-300 flex items-center justify-center gap-2"
              >
                <Headphones class="w-4 h-4" />
                音声ガイドを聞く
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="touristSpots.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🏔️</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            北海道の観光スポットを準備中
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
  title: 'Hokkaido Guide - Discover Japan'
})

// Reactive variables
const activeTab = ref('top')

const touristSpots = ref<TouristSpot[]>([])
const currentAudioGuide = ref<AudioGuide | null>(null)
const currentSpot = ref<TouristSpot | null>(null)


const generateSpotImage = (spotName: string, category: string) => {
  // 観光地ごとの確実に表示される画像URLを返す
  const imageMap: Record<string, string> = {
    '浅草寺': 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=400&h=300&fit=crop&auto=format',
    '明治神宮': 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format',
    '大阪城': 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format',
    '札幌時計台': 'https://images.unsplash.com/photo-1607619662634-3ac55ec0e216?w=400&h=300&fit=crop&auto=format',
    '函館山': 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&h=300&fit=crop&auto=format',
    '小樽運河': 'https://images.unsplash.com/photo-1494522855154-9297ac14b55f?w=400&h=300&fit=crop&auto=format'
  }
  
  return imageMap[spotName] || `https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=300&fit=crop&auto=format`
}

const getSpotTags = (spot: TouristSpot) => {
  const tags = []
  if (spot.name.includes('札幌時計台')) tags.push('歴史', 'ランドマーク')
  if (spot.name.includes('函館山')) tags.push('夜景', '展望台')
  if (spot.name.includes('小樽運河')) tags.push('歴史', 'ノスタルジー')
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
    transcript: `${spot.name}は北海道の代表的な観光スポットの一つです...`,
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


// Load mock tourist spots data for Hokkaido
onMounted(() => {
  // Mock data for Hokkaido tourist spots
  touristSpots.value = [
    {
      id: 301,
      name: '札幌時計台',
      description: '旧札幌農学校演武場として1878年に建設された北海道のシンボル的建造物です。',
      category: '歴史建造物',
      prefecture: '北海道',
      address: '北海道札幌市中央区北1条西2丁目',
      latitude: 43.0642,
      longitude: 141.3469,
      imageUrl: '',
      openingHours: '8:45-17:10',
      admissionFee: '大人200円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 302,
      name: '函館山',
      description: '世界三大夜景の一つに数えられる美しい夜景スポット。津軽海峡を一望できます。',
      category: '展望台',
      prefecture: '北海道',
      address: '北海道函館市函館山',
      latitude: 41.7640,
      longitude: 140.6982,
      imageUrl: '',
      openingHours: '10:00-21:00（ロープウェイ）',
      admissionFee: 'ロープウェイ往復大人1500円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 303,
      name: '小樽運河',
      description: '1923年完成の歴史ある運河。石造倉庫群とガス灯が織りなすロマンチックな景観です。',
      category: '歴史的景観',
      prefecture: '北海道',
      address: '北海道小樽市港町',
      latitude: 43.1907,
      longitude: 140.9947,
      imageUrl: '',
      openingHours: '24時間',
      admissionFee: '無料',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  ]
})
</script>
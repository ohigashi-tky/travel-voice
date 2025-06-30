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
            <span class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 bg-clip-text text-transparent">
              京都府
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
            <div class="h-48 bg-gradient-to-br from-purple-400 to-pink-500 relative">
              <PlacePhotoImage 
                :spot-name="spot.name" :place-id="spot.place_id"
                :alt="spot.name"
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
                  class="bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-2 py-1 rounded-md text-xs transition-colors duration-300"
                >
                  {{ tag }}
                </span>
              </div>
              
              <!-- Audio Guide Button -->
              <button 
                @click.stop="playAudioGuide(spot)"
                class="w-full bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 text-white py-2 px-4 rounded-lg font-medium text-sm hover:from-purple-700 hover:via-pink-700 hover:to-red-700 transition-all duration-300 flex items-center justify-center gap-2"
              >
                <Headphones class="w-4 h-4" />
                音声ガイドを聞く
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="touristSpots.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">⛩️</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            京都の観光スポットを準備中
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
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import AudioGuidePlayer from '~/components/AudioGuidePlayer.vue'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Kyoto Guide - Discover Japan'
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
  if (spot.name.includes('清水寺')) tags.push('世界遺産', '寺院')
  if (spot.name.includes('金閣寺')) tags.push('世界遺産', '金閣')
  if (spot.name.includes('伏見稲荷大社')) tags.push('神社', '千本鳥居')
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
    transcript: `${spot.name}は京都の代表的な観光スポットの一つです...`,
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


const goToSpotDetail = (spotId: number) => {
  navigateTo(`/spots/${spotId}`)
}


// Load mock tourist spots data for Kyoto
onMounted(() => {
  // Mock data for Kyoto tourist spots
  touristSpots.value = [
    {
      id: 201,
      name: '清水寺',
      description: '778年開創の京都最古の寺院。有名な清水の舞台と美しい景色で知られています。',
      category: '寺院',
      prefecture: '京都府',
      address: '京都府京都市東山区清水1-294',
      latitude: 34.9949,
      longitude: 135.7851,
      imageUrl: '',
      openingHours: '6:00-18:00',
      admissionFee: '大人400円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 202,
      name: '金閣寺',
      description: '足利義満の別荘として建てられた金箔で覆われた美しい楼閣。世界文化遺産です。',
      category: '寺院',
      prefecture: '京都府',
      address: '京都府京都市北区金閣寺町1',
      latitude: 35.0394,
      longitude: 135.7292,
      imageUrl: '',
      openingHours: '9:00-17:00',
      admissionFee: '大人500円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 203,
      name: '伏見稲荷大社',
      description: '全国の稲荷神社の総本宮。千本鳥居の美しい朱色のトンネルで有名です。',
      category: '神社',
      prefecture: '京都府',
      address: '京都府京都市伏見区深草藪之内町68',
      latitude: 34.9671,
      longitude: 135.7727,
      imageUrl: '',
      openingHours: '24時間',
      admissionFee: '無料',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  ]
})
</script>
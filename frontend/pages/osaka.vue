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
            <span class="bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 bg-clip-text text-transparent">
              大阪府
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
            <div class="h-48 bg-gradient-to-br from-orange-400 to-red-500 relative">
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
                  class="bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 px-2 py-1 rounded-md text-xs transition-colors duration-300"
                >
                  {{ tag }}
                </span>
              </div>
              
              <!-- Audio Guide Button -->
              <button 
                @click.stop="playAudioGuide(spot)"
                class="w-full bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 text-white py-2 px-4 rounded-lg font-medium text-sm hover:from-orange-700 hover:via-red-700 hover:to-pink-700 transition-all duration-300 flex items-center justify-center gap-2"
              >
                <Headphones class="w-4 h-4" />
                音声ガイドを聞く
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="touristSpots.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🏯</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            大阪の観光スポットを準備中
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
  title: 'Osaka Guide - Discover Japan'
})

// Reactive variables
const activeTab = ref('top')

const touristSpots = ref<TouristSpot[]>([])
const currentAudioGuide = ref<AudioGuide | null>(null)
const currentSpot = ref<TouristSpot | null>(null)


const generateSpotImage = (spotName: string, category: string) => {
  // 観光地ごとの画像URLを返す（確実に表示される画像）
  const imageMap: Record<string, string> = {
    '大阪城': 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format',
    '通天閣': 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=300&fit=crop&auto=format', 
    '海遊館': 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=400&h=300&fit=crop&auto=format',
    '浅草寺': 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=400&h=300&fit=crop&auto=format',
    '明治神宮': 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format',
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
  if (spot.name.includes('大阪城')) tags.push('歴史', '桜の名所')
  if (spot.name.includes('通天閣')) tags.push('展望台', 'レトロ')
  if (spot.name.includes('海遊館')) tags.push('水族館', 'ファミリー')
  if (spot.name.includes('道頓堀')) tags.push('グルメ', 'ネオン', '繁華街')
  if (spot.name.includes('新世界')) tags.push('レトロ', 'グルメ', '下町')
  if (spot.name.includes('梅田') || spot.name.includes('大阪駅')) tags.push('ショッピング', '交通', 'ビジネス')
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
    transcript: `${spot.name}は大阪の代表的な観光スポットの一つです...`,
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


// Load mock tourist spots data for Osaka
onMounted(() => {
  // Mock data for Osaka tourist spots
  touristSpots.value = [
    {
      id: 101,
      name: '大阪城',
      description: '豊臣秀吉が築城した名城。美しい天守閣と桜の名所として親しまれています。',
      category: '歴史建造物',
      prefecture: '大阪府',
      address: '大阪府大阪市中央区大阪城1-1',
      latitude: 34.6873,
      longitude: 135.5262,
      place_id: 'ChIJ_TooXM3gAGARQR6hXH3QAQ8',
      imageUrl: '',
      openingHours: '9:00-17:00',
      admissionFee: '大人600円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 102,
      name: '通天閣',
      description: '新世界のシンボルタワー。ビリケンさんで有名な大阪を代表する観光地です。',
      category: '展望台',
      prefecture: '大阪府',
      address: '大阪府大阪市浪速区恵美須東1-18-6',
      latitude: 34.6523,
      longitude: 135.5061,
      place_id: 'ChIJ_0Lgd2DnAGARV0X03lbPy-U',
      imageUrl: '',
      openingHours: '8:30-21:30',
      admissionFee: '大人900円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 103,
      name: '海遊館',
      description: '世界最大級の水族館。ジンベエザメやマンタが泳ぐ太平洋水槽は圧巻です。',
      category: '水族館',
      prefecture: '大阪府',
      address: '大阪府大阪市港区海岸通1-1-10',
      latitude: 34.6547,
      longitude: 135.4281,
      place_id: 'ChIJzakNjPToAGARzCwIriDFg28',
      imageUrl: '',
      openingHours: '10:00-20:00',
      admissionFee: '大人2700円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 104,
      name: '道頓堀',
      description: '大阪の代表的な繁華街。グリコの看板や川沿いのネオンサインで有名な観光エリアです。',
      category: '観光エリア',
      prefecture: '大阪府',
      address: '大阪府大阪市中央区道頓堀',
      latitude: 34.6686,
      longitude: 135.5023,
      place_id: 'ChIJzWVthgDgAGARYOk-pwyZ5UU',
      imageUrl: '',
      openingHours: '24時間（店舗により異なる）',
      admissionFee: '無料（店舗により異なる）',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 105,
      name: '新世界',
      description: '通天閣を中心とした下町レトロエリア。串カツやお好み焼きなど大阪グルメの聖地です。',
      category: '観光エリア',
      prefecture: '大阪府',
      address: '大阪府大阪市浪速区恵美須東',
      latitude: 34.6520,
      longitude: 135.5065,
      place_id: 'ChIJX8PVvGLnAGARIh1kJH-aVKM',
      imageUrl: '',
      openingHours: '24時間（店舗により異なる）',
      admissionFee: '無料（店舗により異なる）',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 106,
      name: '大阪駅・梅田',
      description: '関西最大の交通ハブ。ショッピング、グルメ、エンターテイメントが集まる西日本の玄関口です。',
      category: '観光エリア',
      prefecture: '大阪府',
      address: '大阪府大阪市北区梅田',
      latitude: 34.7024,
      longitude: 135.4959,
      place_id: 'ChIJC6fjlY3mAGARSshZ6CLIrhs',
      imageUrl: '',
      openingHours: '24時間（施設により異なる）',
      admissionFee: '無料（施設により異なる）',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  ]
})
</script>
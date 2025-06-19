<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 relative overflow-hidden">
    <!-- Dynamic Background -->
    <div class="absolute inset-0">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 via-purple-600/20 to-pink-600/20 animate-gradient-x"></div>
      <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-br from-cyan-400/20 to-blue-600/20 rounded-full filter blur-3xl animate-float"></div>
      <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-gradient-to-br from-purple-400/20 to-pink-600/20 rounded-full filter blur-3xl animate-float-delay"></div>
    </div>

    <!-- Header -->
    <header class="relative z-10 p-6">
      <nav class="flex items-center justify-between max-w-7xl mx-auto">
        <button @click="goBack" class="flex items-center gap-2 text-white hover:text-cyan-400 transition-colors">
          <ArrowLeft class="w-6 h-6" />
          <span class="font-semibold">戻る</span>
        </button>
        
        <h1 class="text-2xl font-black text-white">
          <span class="bg-gradient-to-r from-cyan-400 to-white bg-clip-text text-transparent">
            {{ prefectureName }}
          </span>
        </h1>
        
        <div class="w-20"></div>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 px-4 sm:px-6 lg:px-8 pb-20">
      <div class="max-w-7xl mx-auto">
        
        <!-- Prefecture Hero -->
        <div class="text-center mb-16">
          <div class="mb-8">
            <img 
              :src="prefectureImage" 
              alt="Prefecture Image"
              class="w-full max-w-2xl mx-auto rounded-3xl shadow-2xl"
            />
          </div>
          
          <h2 class="text-5xl md:text-7xl font-black text-white mb-6 animate-fade-in-up">
            {{ prefectureName }}
            <br>
            <span class="bg-gradient-to-r from-cyan-400 via-blue-400 to-purple-400 bg-clip-text text-transparent animate-shimmer">
              GUIDE
            </span>
          </h2>
          <p class="text-xl text-blue-100 max-w-3xl mx-auto">
            {{ prefectureDescription }}
          </p>
        </div>

        <!-- Tourist Spots or Coming Soon -->
        <div v-if="touristSpots.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
          <div 
            v-for="(spot, index) in touristSpots" 
            :key="spot.id"
            class="group relative"
            :style="{ animationDelay: `${index * 0.2}s` }"
          >
            <!-- Glow Effect -->
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/20 to-purple-600/20 rounded-3xl filter blur-xl group-hover:blur-2xl transition-all duration-500 opacity-0 group-hover:opacity-100"></div>
            
            <!-- Card -->
            <div class="relative glass-card rounded-3xl p-8 backdrop-blur-xl bg-white/5 border border-white/10 group-hover:bg-white/10 transition-all duration-500 transform group-hover:scale-105 animate-fade-in-up">
              
              <!-- Spot Image -->
              <div class="aspect-video rounded-2xl mb-6 overflow-hidden">
                <img 
                  :src="generateSpotImage(spot.name, spot.category)" 
                  :alt="spot.name"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Content -->
              <div class="space-y-4">
                <h3 class="text-2xl font-bold text-white group-hover:text-cyan-400 transition-colors">
                  {{ spot.name }}
                </h3>
                
                <p class="text-gray-300 leading-relaxed line-clamp-3">
                  {{ spot.description }}
                </p>
                
                <!-- Category Tag -->
                <div class="flex flex-wrap gap-2">
                  <span class="px-3 py-1 bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-blue-200 rounded-full text-sm backdrop-blur-sm border border-blue-400/20">
                    {{ spot.category }}
                  </span>
                </div>
                
                <!-- Action Button -->
                <button 
                  @click="exploreSpot(spot)"
                  class="w-full mt-6 py-4 bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-500 text-white font-bold rounded-2xl hover:shadow-lg hover:shadow-cyan-500/25 transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-3"
                >
                  <Icon name="lucide:play" class="w-5 h-5" />
                  音声ガイドを開始
                  <Icon name="lucide:arrow-right" class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Coming Soon Message -->
        <div v-else class="text-center py-20">
          <div class="glass-card rounded-3xl p-12 backdrop-blur-xl bg-white/5 border border-white/10 max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-gradient-to-br from-cyan-400 to-purple-600 rounded-2xl mx-auto mb-6 flex items-center justify-center">
              <Icon name="lucide:clock" class="w-10 h-10 text-white" />
            </div>
            <h3 class="text-3xl font-bold text-white mb-4">準備中です</h3>
            <p class="text-gray-300 leading-relaxed mb-6">
              {{ prefectureName }}の音声ガイドコンテンツを鋭意制作中です。<br>
              現在は東京都のガイドをお楽しみいただけます。
            </p>
            <button 
              @click="navigateTo('/tokyo')"
              class="px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-2xl hover:shadow-lg hover:shadow-cyan-500/25 transform hover:scale-105 transition-all duration-300"
            >
              東京ガイドを体験する
            </button>
          </div>
        </div>

        <!-- Audio Guide Player -->
        <AudioGuidePlayer 
          :audio-guide="currentAudioGuide"
          :spot-name="currentSpot?.name || null"
          :is-visible="!!currentAudioGuide"
          @close="closePlayer"
        />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ArrowLeft } from 'lucide-vue-next'
import type { TouristSpot, AudioGuide } from '~/types'

const route = useRoute()
const prefectureName = decodeURIComponent(route.params.name as string)

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: `${prefectureName} - Discover Japan`
})

// Removed useImageGeneration composable

const touristSpots = ref<TouristSpot[]>([])
const currentAudioGuide = ref<AudioGuide | null>(null)
const currentSpot = ref<TouristSpot | null>(null)

const prefectureImage = computed(() => {
  const imageMap: Record<string, string> = {
    '東京都': 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&h=400&fit=crop&auto=format',
    '大阪府': 'https://images.unsplash.com/photo-1590736969955-71cc94901144?w=600&h=400&fit=crop&auto=format',
    '京都府': 'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=600&h=400&fit=crop&auto=format',
    '北海道': 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=600&h=400&fit=crop&auto=format',
    '奈良県': 'https://images.unsplash.com/photo-1548407260-da850faa41e3?w=600&h=400&fit=crop&auto=format',
    '広島県': 'https://images.unsplash.com/photo-1580674684081-7617fbf3d745?w=600&h=400&fit=crop&auto=format',
    '福岡県': 'https://images.unsplash.com/photo-1511593358241-7eea1f3c84e5?w=600&h=400&fit=crop&auto=format',
    '沖縄県': 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=600&h=400&fit=crop&auto=format'
  }
  
  return imageMap[prefectureName] || `https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&h=400&fit=crop&auto=format`
})

const prefectureDescription = computed(() => {
  const descriptions: Record<string, string> = {
    '東京都': '現代と伝統が融合する日本の首都。スカイツリー、浅草寺、明治神宮など、多彩な魅力に満ちています。',
    '北海道': '雄大な自然と美味しい食べ物の宝庫。四季折々の美しい景色と新鮮な海の幸が楽しめます。',
    '大阪府': '食い倒れの街として知られる関西の中心都市。大阪城や道頓堀など、歴史と現代が交差する魅力的な街です。',
    '京都府': '千年の都として栄えた古都。数多くの寺社仏閣と伝統文化が今も息づく美しい街並みが魅力です。',
    '奈良県': '日本の古都として知られ、東大寺や春日大社などの歴史的建造物と、鹿で有名な奈良公園があります。',
    '広島県': '平和の象徴として世界に知られる都市。厳島神社や原爆ドームなど、重要な歴史的意義を持つ場所があります。',
    '福岡県': '九州の玄関口として栄える都市。博多ラーメンや明太子などの美味しいグルメと、太宰府天満宮などの歴史スポットが魅力です。',
    '沖縄県': '美しい海と独特の文化が魅力の南国の楽園。首里城や美ら海水族館など、本土とは異なる魅力に満ちています。'
  }
  return descriptions[prefectureName] || `${prefectureName}の美しい自然と文化を音声ガイドと共に発見しましょう。`
})

const generateSpotImage = (spotName: string, category: string) => {
  // 観光地ごとの実際の画像URLを返す
  const imageMap: Record<string, string> = {
    '東京スカイツリー': 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=400&h=300&fit=crop&auto=format',
    '浅草寺': 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=400&h=300&fit=crop&auto=format',
    '明治神宮': 'https://images.unsplash.com/photo-1490650034439-fd184c3c86a5?w=400&h=300&fit=crop&auto=format',
    '大阪城': 'https://images.unsplash.com/photo-1590736969955-71cc94901144?w=400&h=300&fit=crop&auto=format',
    '清水寺': 'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=400&h=300&fit=crop&auto=format',
    '札幌時計台': 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop&auto=format'
  }
  
  return imageMap[spotName] || `https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=300&fit=crop&auto=format`
}

const goBack = () => {
  navigateTo('/')
}

const exploreSpot = async (spot: TouristSpot) => {
  try {
    currentSpot.value = spot
    currentAudioGuide.value = {
      id: spot.id,
      title: `${spot.name}の音声ガイド`,
      audio_url: '/sample-audio.mp3',
      duration: 180,
      language: 'ja',
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString()
    }
  } catch (error) {
    console.error('Error loading audio guide:', error)
  }
}

const closePlayer = () => {
  currentAudioGuide.value = null
  currentSpot.value = null
}

// Fetch tourist spots for this prefecture
onMounted(async () => {
  try {
    const { $api } = useNuxtApp()
    const spots = await $api<TouristSpot[]>(`/tourist-spots?prefecture=${encodeURIComponent(prefectureName)}`)
    touristSpots.value = spots
  } catch (error) {
    console.error('Error fetching tourist spots:', error)
    // For non-Tokyo prefectures, show empty state
    touristSpots.value = []
  }
})
</script>
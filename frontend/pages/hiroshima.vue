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
            <span class="bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 bg-clip-text text-transparent">
              広島県
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
            <div class="h-48 bg-gradient-to-br from-red-400 to-orange-500 relative">
              <UnsplashImage 
                :spot-name="spot.name"
                :alt="spot.name"
              >
                <div class="absolute top-3 right-3">
                  <span class="bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white px-2 py-1 rounded-lg text-xs font-medium">
                    {{ spot.category }}
                  </span>
                </div>
              </UnsplashImage>
            </div>
            
            <!-- Spot Info -->
            <div class="p-6">
              <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2 transition-colors duration-300">
                {{ spot.name }}
              </h3>
              <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2 transition-colors duration-300">
                {{ spot.description }}
              </p>
              
              <!-- Tags -->
              <div class="flex flex-wrap gap-2 mb-4">
                <span 
                  v-for="highlight in spot.highlights?.slice(0, 3)" 
                  :key="highlight"
                  class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 px-2 py-1 rounded text-xs transition-colors duration-300"
                >
                  {{ highlight }}
                </span>
              </div>
              
              <!-- Action Button -->
              <div class="flex justify-between items-center">
                <span class="text-red-600 dark:text-red-400 text-sm font-medium">詳細を見る</span>
                <ArrowRight class="w-4 h-4 text-red-600 dark:text-red-400" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ArrowLeft, ArrowRight } from 'lucide-vue-next'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import UnsplashImage from '~/components/UnsplashImage.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '広島県 - Travel Voice',
  meta: [
    { name: 'description', content: '広島県の観光スポットを音声ガイドで楽しもう。原爆ドーム、厳島神社、広島城など平和と歴史を学べる観光地をご紹介。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// 広島県の観光スポット
const allSpots = [
  {
    id: 601,
    name: '原爆ドーム',
    description: '平和の象徴として世界中に知られる広島の代表的なランドマーク。ユネスコ世界文化遺産です。',
    category: '歴史建造物',
    prefecture: '広島県',
    highlights: ['世界文化遺産', '平和記念公園', '慰霊碑', '平和の灯']
  },
  {
    id: 602,
    name: '厳島神社',
    description: '海に浮かぶ朱色の大鳥居で有名な日本三景の一つ。満潮時の美しさは格別です。',
    category: '神社',
    prefecture: '広島県',
    highlights: ['海上大鳥居', '本殿', '能舞台', '宝物館']
  },
  {
    id: 603,
    name: '広島城',
    description: '毛利輝元が築いた名城。現在は歴史博物館として広島の歴史を学ぶことができます。',
    category: '歴史建造物',
    prefecture: '広島県',
    highlights: ['復元天守閣', '二の丸', '護国神社', '桜の名所']
  }
]

// 広島県の観光スポットのみをフィルタリング
const touristSpots = computed(() => {
  return allSpots.filter(spot => spot.prefecture === '広島県')
})

// Navigation methods
const goHome = () => {
  navigateTo('/')
}

const goToSpotDetail = (spotId) => {
  navigateTo(`/spots/${spotId}`)
}

// Image generation for spots
const generateSpotImage = (name, category) => {
  // カテゴリに基づいた画像URL生成
  const imageMap = {
    '原爆ドーム': 'https://images.unsplash.com/photo-1590736969955-71cc94901144?w=400&h=300&fit=crop&auto=format',
    '厳島神社': 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format',
    '広島城': 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format'
  }
  
  return imageMap[name] || `https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=400&h=300&fit=crop&auto=format`
}
</script>
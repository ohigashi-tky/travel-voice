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
            <span class="bg-gradient-to-r from-blue-500 via-cyan-500 to-teal-500 bg-clip-text text-transparent">
              新潟県
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
            <div class="h-48 bg-gradient-to-br from-blue-400 to-teal-500 relative">
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
                  class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded text-xs transition-colors duration-300"
                >
                  {{ highlight }}
                </span>
              </div>
              
              <!-- Action Button -->
              <div class="flex justify-between items-center">
                <span class="text-blue-600 dark:text-blue-400 text-sm font-medium">詳細を見る</span>
                <ArrowRight class="w-4 h-4 text-blue-600 dark:text-blue-400" />
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
  title: '新潟県 - Travel Voice',
  meta: [
    { name: 'description', content: '新潟県の観光スポットを音声ガイドで楽しもう。佐渡島、越後湯沢、清津峡など雪国の魅力をご紹介。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// 新潟県の観光スポット
const allSpots = [
  {
    id: 1001,
    name: '佐渡島',
    description: '日本海に浮かぶ神秘の島。金山の歴史と豊かな自然、伝統芸能が息づく島です。',
    category: '自然',
    prefecture: '新潟県',
    highlights: ['佐渡金山', 'トキの森公園', '能楽', '棚田']
  },
  {
    id: 1002,
    name: '越後湯沢',
    description: '川端康成「雪国」の舞台。温泉と雪景色、スキーリゾートとして親しまれています。',
    category: '温泉',
    prefecture: '新潟県',
    highlights: ['雪国', '温泉', 'スキー場', '雪景色']
  },
  {
    id: 1003,
    name: '弥彦神社',
    description: '越後一宮として古くから信仰を集める神社。弥彦山とともに新潟県を代表する霊地です。',
    category: '神社',
    prefecture: '新潟県',
    highlights: ['越後一宮', '弥彦山', '紅葉', 'パワースポット']
  },
  {
    id: 1004,
    name: '清津峡',
    description: '日本三大峡谷の一つ。トンネル内のアート展示と美しい渓谷美で話題の絶景スポットです。',
    category: '自然',
    prefecture: '新潟県',
    highlights: ['三大峡谷', 'アート', 'トンネル', '絶景']
  },
  {
    id: 1005,
    name: '高田城跡',
    description: '徳川家康の六男松平忠輝の居城跡。桜の名所として日本三大夜桜の一つに数えられます。',
    category: '歴史建造物',
    prefecture: '新潟県',
    highlights: ['三大夜桜', '桜の名所', '城跡', 'ライトアップ']
  },
  {
    id: 1006,
    name: '新潟市水族館マリンピア日本海',
    description: '日本海側最大級の水族館。イルカショーと日本海の豊かな海洋生物を楽しめます。',
    category: '水族館',
    prefecture: '新潟県',
    highlights: ['イルカショー', '日本海', '水族館', '海洋生物']
  }
]

// 新潟県の観光スポットのみをフィルタリング
const touristSpots = computed(() => {
  return allSpots.filter(spot => spot.prefecture === '新潟県')
})

// Navigation methods
const goHome = () => {
  navigateTo('/')
}

const goToSpotDetail = (spotId) => {
  navigateTo(`/spots/${spotId}`)
}
</script>
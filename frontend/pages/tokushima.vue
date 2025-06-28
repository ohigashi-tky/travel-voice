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
            <span class="bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 bg-clip-text text-transparent">
              徳島県
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
            <div class="h-48 bg-gradient-to-br from-green-400 to-teal-500 relative">
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
                  class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-2 py-1 rounded text-xs transition-colors duration-300"
                >
                  {{ highlight }}
                </span>
              </div>
              
              <!-- Action Button -->
              <div class="flex justify-between items-center">
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">詳細を見る</span>
                <ArrowRight class="w-4 h-4 text-green-600 dark:text-green-400" />
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
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '徳島県 - Travel Voice',
  meta: [
    { name: 'description', content: '徳島県の観光スポットを音声ガイドで楽しもう。鳴門の渦潮、祖谷のかずら橋、阿波踊りなど四国の魅力をご紹介。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// 徳島県の観光スポット
const allSpots = [
  {
    id: 1201,
    name: '鳴門の渦潮',
    description: '世界最大級の渦潮。大鳴門橋からの眺めと遊覧船での体験が人気の自然現象です。',
    category: '自然',
    prefecture: '徳島県',
    highlights: ['世界最大級', '渦潮', '大鳴門橋', '遊覧船']
  },
  {
    id: 1202,
    name: '祖谷のかずら橋',
    description: '野生のシラクチカズラで作られた吊り橋。日本三奇橋の一つとして知られています。',
    category: '歴史的景観',
    prefecture: '徳島県',
    highlights: ['三奇橋', 'かずら橋', '吊り橋', '祖谷渓']
  },
  {
    id: 1203,
    name: '阿波踊り会館',
    description: '徳島の伝統芸能阿波踊りを一年中楽しめる施設。踊りの歴史と魅力を学べます。',
    category: '観光エリア',
    prefecture: '徳島県',
    highlights: ['阿波踊り', '伝統芸能', 'よしこの', '踊り子']
  },
  {
    id: 1204,
    name: '大塚国際美術館',
    description: '世界の名画を陶板で原寸大再現した美術館。ルーヴル美術館級の展示が楽しめます。',
    category: '博物館',
    prefecture: '徳島県',
    highlights: ['陶板名画', '原寸大', '世界の名画', '大塚製薬']
  },
  {
    id: 1205,
    name: '剣山',
    description: '四国第二の高峰。霊峰として信仰を集め、高山植物と360度の絶景が楽しめます。',
    category: '自然',
    prefecture: '徳島県',
    highlights: ['四国第二峰', '霊峰', '高山植物', '絶景']
  },
  {
    id: 1206,
    name: 'うだつの町並み',
    description: '脇町の江戸時代の商家群。うだつが上がった美しい伝統的建造物群保存地区です。',
    category: '歴史的景観',
    prefecture: '徳島県',
    highlights: ['うだつ', '商家群', '江戸時代', '伝統建造物']
  }
]

// 徳島県の観光スポットのみをフィルタリング
const touristSpots = computed(() => {
  return allSpots.filter(spot => spot.prefecture === '徳島県')
})

// Navigation methods
const goHome = () => {
  navigateTo('/')
}

const goToSpotDetail = (spotId) => {
  navigateTo(`/spots/${spotId}`)
}
</script>
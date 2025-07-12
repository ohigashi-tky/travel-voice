<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/7.jpeg" 
          alt="福島県"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center relative">
          <button 
            @click="goHome"
            class="absolute left-0 flex items-center gap-2 text-gray-200 hover:text-white transition-colors duration-300 group"
          >
            <ArrowLeft class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" />
            <span class="text-sm font-medium">戻る</span>
          </button>
          <h1 class="text-3xl font-bold text-white tracking-wide">
            福島県
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
            <div class="h-48 bg-gradient-to-br from-green-400 to-purple-500 relative">
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
  title: '福島県 - Travel Voice',
  meta: [
    { name: 'description', content: '福島県の観光スポットを音声ガイドで楽しもう。会津若松城、五色沼、いわき湯本温泉など歴史と自然の魅力をご紹介。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// 福島県の観光スポット
const allSpots = [
  {
    id: 701,
    name: '会津若松城',
    description: '戊辰戦争で有名な名城。白虎隊の悲話で知られる会津の象徴的な城郭です。',
    category: '歴史建造物',
    prefecture: '福島県',
    highlights: ['天守閣', '白虎隊', '戊辰戦争', '桜の名所']
  },
  {
    id: 702,
    name: '五色沼',
    description: '磐梯山の噴火によって生まれた美しい湖沼群。エメラルドグリーンの湖面が神秘的です。',
    category: '自然',
    prefecture: '福島県',
    highlights: ['五色の湖', '磐梯山', 'トレッキング', '紅葉']
  },
  {
    id: 703,
    name: 'いわき湯本温泉',
    description: '1200年の歴史を誇る古湯。常磐地方の代表的な温泉地として親しまれています。',
    category: '温泉',
    prefecture: '福島県',
    highlights: ['古湯', '湯本温泉', 'スパリゾート', '温泉街']
  },
  {
    id: 704,
    name: '大内宿',
    description: '江戸時代の宿場町が保存された重要伝統的建造物群保存地区。茅葺き屋根の町並みが美しい。',
    category: '歴史的景観',
    prefecture: '福島県',
    highlights: ['茅葺き屋根', '宿場町', '伝統建造物', 'ねぎそば']
  },
  {
    id: 705,
    name: 'あぶくま洞',
    description: '東洋一の鍾乳洞として知られる神秘的な地底世界。美しい石筍や石柱が幻想的です。',
    category: '自然',
    prefecture: '福島県',
    highlights: ['鍾乳洞', '石筍', '地底世界', 'ライトアップ']
  },
  {
    id: 706,
    name: 'スパリゾートハワイアンズ',
    description: '温泉をテーマにした大型レジャー施設。フラダンスショーと温泉が楽しめます。',
    category: '観光エリア',
    prefecture: '福島県',
    highlights: ['フラダンス', '温泉', 'ウォータースライダー', 'ショー']
  }
]

// 福島県の観光スポットのみをフィルタリング
const touristSpots = computed(() => {
  return allSpots.filter(spot => spot.prefecture === '福島県')
})

// Navigation methods
const goHome = () => {
  navigateTo('/')
}

const goToSpotDetail = (spotId) => {
  navigateTo(`/spots/${spotId}`)
}
</script>
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
            <span class="bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 bg-clip-text text-transparent">
              山口県
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
            <div class="h-48 bg-gradient-to-br from-orange-400 to-pink-500 relative">
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
                  class="bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 px-2 py-1 rounded text-xs transition-colors duration-300"
                >
                  {{ highlight }}
                </span>
              </div>
              
              <!-- Action Button -->
              <div class="flex justify-between items-center">
                <span class="text-orange-600 dark:text-orange-400 text-sm font-medium">詳細を見る</span>
                <ArrowRight class="w-4 h-4 text-orange-600 dark:text-orange-400" />
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
  title: '山口県 - Travel Voice',
  meta: [
    { name: 'description', content: '山口県の観光スポットを音声ガイドで楽しもう。錦帯橋、秋芳洞、萩城下町など歴史と自然の魅力をご紹介。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// 山口県の観光スポット
const allSpots = [
  {
    id: 1101,
    name: '錦帯橋',
    description: '日本三名橋の一つ。五連の木造アーチ橋が美しい岩国市の象徴的な橋です。',
    category: '歴史建造物',
    prefecture: '山口県',
    highlights: ['三名橋', '木造アーチ', '桜の名所', '岩国城']
  },
  {
    id: 1102,
    name: '秋芳洞',
    description: '日本最大級の鍾乳洞。神秘的な地下世界と美しい石灰岩の造形美が楽しめます。',
    category: '自然',
    prefecture: '山口県',
    highlights: ['鍾乳洞', '秋吉台', '石灰岩', '地下世界']
  },
  {
    id: 1103,
    name: '萩城下町',
    description: '明治維新の舞台となった武家屋敷群。幕末の志士たちが歩いた歴史ある町並みです。',
    category: '歴史的景観',
    prefecture: '山口県',
    highlights: ['明治維新', '武家屋敷', '幕末', '歴史的町並み']
  },
  {
    id: 1104,
    name: '角島大橋',
    description: 'エメラルドグリーンの海に架かる美しい橋。CMや映画のロケ地としても有名です。',
    category: '観光エリア',
    prefecture: '山口県',
    highlights: ['エメラルドグリーン', '絶景', 'ロケ地', '角島']
  },
  {
    id: 1105,
    name: '瑠璃光寺',
    description: '国宝の五重塔で有名な臨済宗の寺院。山口市を代表する美しい古刹です。',
    category: '寺院',
    prefecture: '山口県',
    highlights: ['国宝', '五重塔', '古刹', '山口市']
  },
  {
    id: 1106,
    name: '元乃隅神社',
    description: '海に向かって123基の鳥居が連なる絶景神社。CNNに選ばれた美しい場所です。',
    category: '神社',
    prefecture: '山口県',
    highlights: ['123基の鳥居', 'CNN選出', '絶景', '海の神社']
  }
]

// 山口県の観光スポットのみをフィルタリング
const touristSpots = computed(() => {
  return allSpots.filter(spot => spot.prefecture === '山口県')
})

// Navigation methods
const goHome = () => {
  navigateTo('/')
}

const goToSpotDetail = (spotId) => {
  navigateTo(`/spots/${spotId}`)
}
</script>
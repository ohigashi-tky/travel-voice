<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/11.jpeg" 
          alt="埼玉県"
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
            埼玉県
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
            <div class="h-48 bg-gradient-to-br from-pink-400 to-orange-500 relative">
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
                  class="bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300 px-2 py-1 rounded text-xs transition-colors duration-300"
                >
                  {{ highlight }}
                </span>
              </div>
              
              <!-- Action Button -->
              <div class="flex justify-between items-center">
                <span class="text-pink-600 dark:text-pink-400 text-sm font-medium">詳細を見る</span>
                <ArrowRight class="w-4 h-4 text-pink-600 dark:text-pink-400" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    
    <!-- Footer -->
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ArrowLeft, ArrowRight } from 'lucide-vue-next'
import AppHeader from '~/components/AppHeader.vue'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '埼玉県 - Travel Voice',
  meta: [
    { name: 'description', content: '埼玉県の観光スポットを音声ガイドで楽しもう。川越の蔵造り、秩父神社、長瀞渓谷など歴史と自然の魅力をご紹介。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// 埼玉県の観光スポット
const allSpots = [
  {
    id: 901,
    name: '川越',
    description: '小江戸と呼ばれる蔵造りの町並み。江戸の情緒を残す歴史ある観光地です。',
    category: '歴史的景観',
    prefecture: '埼玉県',
    highlights: ['蔵造り', '菓子屋横丁', '時の鐘', '小江戸']
  },
  {
    id: 902,
    name: '秩父神社',
    description: '2000年以上の歴史を持つ古社。秩父夜祭で有名な関東屈指のパワースポットです。',
    category: '神社',
    prefecture: '埼玉県',
    highlights: ['秩父夜祭', 'パワースポット', '左甚五郎', '彫刻']
  },
  {
    id: 903,
    name: '長瀞渓谷',
    description: '荒川が作り出した美しい渓谷美。ライン下りと岩畳で有名な景勝地です。',
    category: '自然',
    prefecture: '埼玉県',
    highlights: ['ライン下り', '岩畳', '紅葉', '渓谷美']
  },
  {
    id: 904,
    name: '武蔵一宮氷川神社',
    description: '関東の氷川神社の総本社。2400年の歴史を誇る格式高い神社です。',
    category: '神社',
    prefecture: '埼玉県',
    highlights: ['総本社', '参道', '楼門', '氷川神社']
  },
  {
    id: 905,
    name: '鉄道博物館',
    description: '日本最大級の鉄道博物館。実物の車両展示と体験型展示が充実しています。',
    category: '博物館',
    prefecture: '埼玉県',
    highlights: ['車両展示', 'ジオラマ', 'シミュレータ', '体験型']
  },
  {
    id: 906,
    name: '芝桜の丘',
    description: '羊山公園の芝桜が織りなす美しいカーペット。春の絶景スポットとして人気です。',
    category: '自然',
    prefecture: '埼玉県',
    highlights: ['芝桜', '羊山公園', '春の絶景', 'カーペット']
  }
]

// 埼玉県の観光スポットのみをフィルタリング
const touristSpots = computed(() => {
  return allSpots.filter(spot => spot.prefecture === '埼玉県')
})

// Navigation methods
const goHome = () => {
  navigateTo('/')
}

const goToSpotDetail = (spotId) => {
  navigateTo(`/spots/${spotId}`)
}
</script>

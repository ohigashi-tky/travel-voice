<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/38.jpg" 
          alt="愛媛県"
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
            愛媛県
          </h1>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 pb-24">
      <div class="max-w-7xl mx-auto py-6">
        <!-- Tourist Spots Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <TouristSpotCard
            v-for="spot in touristSpots"
            :key="spot.id"
            :spot="spot"
            :show-tags="true"
          />
        </div>
      </div>
    </main>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { ArrowLeft, ArrowRight } from 'lucide-vue-next'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'
import { useTouristSpots } from '~/composables/useTouristSpots'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '愛媛県 - Travel Voice',
  meta: [
    { name: 'description', content: '愛媛県の観光スポットを音声ガイドで楽しもう。道後温泉、松山城、今治城など歴史と温泉の魅力をご紹介。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// 愛媛県の観光スポット
const allSpots = [
  {
    id: 801,
    name: '道後温泉',
    description: '日本最古の温泉の一つ。千と千尋の神隠しのモデルとしても有名な歴史ある温泉地です。',
    category: '温泉',
    prefecture: '愛媛県',
    highlights: ['道後温泉本館', '湯神社', '道後公園', '坊っちゃん列車']
  },
  {
    id: 802,
    name: '松山城',
    description: '松山市の中心部、勝山山頂に建つ現存12天守の一つ。美しい桜の名所としても知られています。',
    category: '歴史建造物',
    prefecture: '愛媛県',
    highlights: ['現存天守', '本丸', 'ロープウェイ', '桜の名所']
  },
  {
    id: 803,
    name: '今治城',
    description: '瀬戸内海に面した水城として築かれた美しい城。藤堂高虎が築城した海岸平城です。',
    category: '歴史建造物',
    prefecture: '愛媛県',
    highlights: ['水城', '天守閣', '石垣', '瀬戸内海']
  },
  {
    id: 804,
    name: '内子町',
    description: '江戸時代から明治時代の町並みが美しく保存された重要伝統的建造物群保存地区です。',
    category: '歴史的景観',
    prefecture: '愛媛県',
    highlights: ['伝統的建造物群', '内子座', '大村家住宅', '和ろうそく']
  },
  {
    id: 805,
    name: '石鎚山',
    description: '西日本最高峰の霊峰。四季折々の美しい自然と登山が楽しめる愛媛県のシンボルです。',
    category: '自然',
    prefecture: '愛媛県',
    highlights: ['西日本最高峰', '霊峰', '紅葉', 'ロープウェイ']
  },
  {
    id: 806,
    name: 'しまなみ海道',
    description: '本州と四国を結ぶ美しい橋の連続。サイクリングの聖地として世界的に有名です。',
    category: '観光エリア',
    prefecture: '愛媛県',
    highlights: ['サイクリング', '来島海峡大橋', '瀬戸内海', '島めぐり']
  }
]

// 愛媛県の観光スポットのみをフィルタリング
const touristSpots = computed(() => {
  return allSpots.filter(spot => spot.prefecture === '愛媛県')
})

// Navigation methods
const goHome = () => {
  navigateTo('/')
}

const goToSpotDetail = (spotId) => {
  navigateTo(`/spots/${spotId}`)
}
</script>
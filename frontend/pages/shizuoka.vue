<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Back Button -->
    <BackButton />

    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/22.jpeg" 
          alt="静岡県"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            静岡県
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

        <!-- Empty State -->
        <div v-if="touristSpots.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🗻</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            静岡の観光スポットを準備中
          </h3>
          <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">
            魅力的な観光地の音声ガイドをお楽しみに！
          </p>
        </div>
      </div>

    </main>
    
    <!-- Footer -->

  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { TouristSpot } from '~/types'
import AppHeader from '~/components/AppHeader.vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'
import BackButton from '~/components/BackButton.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '静岡県 - Travel Voice',
  meta: [
    { name: 'description', content: '静岡県の観光スポットを音声ガイドで楽しもう。富士山、熱海温泉、伊豆半島など雄大な自然と温泉の魅力を発見。' }
  ]
})

// Reactive variables
const activeTab = ref('guide')

// Tourist spots data for Shizuoka
const touristSpots = ref<TouristSpot[]>([
  {
    id: 1101,
    name: '富士山',
    description: '日本最高峰の霊峰。世界文化遺産に登録され、古来より信仰の対象として親しまれる日本のシンボルです。',
    category: '自然・公園',
    prefecture: '静岡県',
    address: '静岡県富士宮市粟倉',
    latitude: 35.360556,
    longitude: 138.727778,
    imageUrl: '',
    openingHours: '24時間（登山期間：7月〜9月）',
    admissionFee: '無料（登山道使用料別途）',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1102,
    name: '熱海温泉',
    description: '古くから湯治場として栄えた日本有数の温泉リゾート。海と山に囲まれた美しい景観と豊富な湯量が自慢です。',
    category: '温泉',
    prefecture: '静岡県',
    address: '静岡県熱海市',
    latitude: 35.096111,
    longitude: 139.077778,
    imageUrl: '',
    openingHours: '24時間（施設により異なる）',
    admissionFee: '施設により異なる',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1103,
    name: '伊豆半島ジオパーク',
    description: '伊豆半島全体が日本ジオパークに認定。火山活動によって形成された美しい景観と豊かな自然を楽しめます。',
    category: '自然・公園',
    prefecture: '静岡県',
    address: '静岡県伊豆市',
    latitude: 34.919167,
    longitude: 138.953333,
    imageUrl: '',
    openingHours: '24時間',
    admissionFee: '無料',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1104,
    name: '久能山東照宮',
    description: '徳川家康公が眠る霊廟。美しい極彩色の社殿と駿河湾を一望できる絶景で知られる重要文化財です。',
    category: '神社',
    prefecture: '静岡県',
    address: '静岡県静岡市駿河区根古屋390',
    latitude: 34.943889,
    longitude: 138.466111,
    imageUrl: '',
    openingHours: '9:00-17:00',
    admissionFee: '大人500円',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1105,
    name: '白糸の滝',
    description: '富士山の湧水が流れ落ちる美しい滝。幅150m、高さ20mの白いカーテンのような景観は圧巻です。',
    category: '自然・公園',
    prefecture: '静岡県',
    address: '静岡県富士宮市上井出',
    latitude: 35.311944,
    longitude: 138.590833,
    imageUrl: '',
    openingHours: '8:30-17:00',
    admissionFee: '無料',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1106,
    name: '三保の松原',
    description: '世界文化遺産富士山の構成資産。約7kmの海岸線に約30,000本の松が群生し、富士山との絶景を楽しめます。',
    category: '自然・公園',
    prefecture: '静岡県',
    address: '静岡県静岡市清水区三保',
    latitude: 35.018611,
    longitude: 138.522222,
    imageUrl: '',
    openingHours: '24時間',
    admissionFee: '無料',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  }
])
</script>

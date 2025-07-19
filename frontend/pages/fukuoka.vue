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
          src="/prefectures_image/40.jpeg" 
          alt="福岡県"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            福岡県
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
  </div>
</template>

<script setup>
import { ref } from 'vue'
import AppHeader from '~/components/AppHeader.vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '福岡県 - Travel Voice',
  meta: [
    { name: 'description', content: '福岡県の観光スポットを音声ガイドで楽しもう。大宰府天満宮、福岡城跡など歴史と文化の魅力を発見。' }
  ]
})

// Reactive variables
const activeTab = ref('guide')

// Tourist spots data for Fukuoka - use local data for now
const touristSpots = [
  {
    id: 501,
    name: '太宰府天満宮',
    description: '学問の神様菅原道真公を祀る由緒ある神社。受験合格や学業成就を願う多くの参拝者が訪れます。',
    category: '神社',
    prefecture: '福岡県'
  },
  {
    id: 502,
    name: '福岡城跡',
    description: '黒田如水・長政父子が築城した福岡藩52万石の居城跡。桜の名所としても有名です。',
    category: '歴史建造物',
    prefecture: '福岡県'
  },
  {
    id: 503,
    name: '博多駅',
    description: '九州の玄関口として親しまれる巨大ターミナル駅。グルメやショッピングも楽しめます。',
    category: '観光エリア',
    prefecture: '福岡県'
  },
  {
    id: 504,
    name: 'キャナルシティ博多',
    description: '運河が流れる開放的なショッピングモール。ファッション、グルメ、エンターテイメントが集結する複合商業施設です。',
    category: '観光エリア',
    prefecture: '福岡県'
  },
  {
    id: 505,
    name: '櫛田神社',
    description: '博多総鎮守として親しまれる古社。博多祇園山笠の出発点として知られ、博多っ子の心の拠り所です。',
    category: '神社',
    prefecture: '福岡県'
  },
  {
    id: 506,
    name: '福岡PayPayドーム',
    description: '福岡ソフトバンクホークスの本拠地として親しまれる日本初の開閉式ドーム球場。野球以外にもコンサートなど多彩なイベントが開催されます。',
    category: '観光エリア',
    prefecture: '福岡県'
  }
]

// Navigation functions (not needed anymore with TouristSpotCard)

</script>

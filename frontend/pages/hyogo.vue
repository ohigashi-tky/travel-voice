<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">

    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/28.jpeg" 
          alt="兵庫県"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            兵庫県
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
          <div class="text-6xl mb-4">🏯</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            兵庫の観光スポットを準備中
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
import TouristSpotCard from '~/components/TouristSpotCard.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '兵庫県 - Travel Voice',
  meta: [
    { name: 'description', content: '兵庫県の観光スポットを音声ガイドで楽しもう。姫路城、有馬温泉、神戸ポートタワーなど歴史と文化の魅力を発見。' }
  ]
})

// Reactive variables
const activeTab = ref('guide')

// Tourist spots data for Hyogo
const touristSpots = ref<TouristSpot[]>([
  {
    id: 1001,
    name: '姫路城',
    description: '白鷺城とも呼ばれる世界文化遺産の名城。日本の城郭建築の最高峰として国宝に指定されています。',
    category: '歴史建造物',
    prefecture: '兵庫県',
    address: '兵庫県姫路市本町68番地',
    latitude: 34.839444,
    longitude: 134.693889,
    imageUrl: '',
    openingHours: '9:00-16:00（夏期は17:00まで）',
    admissionFee: '大人1000円',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1002,
    name: '有馬温泉',
    description: '日本三古湯の一つとして親しまれる歴史ある温泉地。金泉・銀泉の2つの泉質で多くの観光客を魅了しています。',
    category: '温泉',
    prefecture: '兵庫県',
    address: '兵庫県神戸市北区有馬町',
    latitude: 34.800556,
    longitude: 135.248611,
    imageUrl: '',
    openingHours: '24時間（施設により異なる）',
    admissionFee: '施設により異なる',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1003,
    name: '神戸ポートタワー',
    description: '神戸港のシンボルとして親しまれる赤い鉄塔。展望台からは神戸の美しい夜景と港の風景を一望できます。',
    category: '展望台',
    prefecture: '兵庫県',
    address: '兵庫県神戸市中央区波止場町5-5',
    latitude: 34.681944,
    longitude: 135.186111,
    imageUrl: '',
    openingHours: '9:00-21:00',
    admissionFee: '大人700円',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1004,
    name: '竹田城跡',
    description: '天空の城として有名な山城跡。雲海に浮かぶ幻想的な姿は「日本のマチュピチュ」とも呼ばれています。',
    category: '歴史建造物',
    prefecture: '兵庫県',
    address: '兵庫県朝来市和田山町竹田古城山169番地',
    latitude: 35.306111,
    longitude: 134.832222,
    imageUrl: '',
    openingHours: '8:00-18:00（季節により異なる）',
    admissionFee: '大人500円',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1005,
    name: '神戸異人館',
    description: '明治・大正時代の外国人居留地の洋風建築群。異国情緒あふれる街並みで神戸の歴史を感じることができます。',
    category: '歴史建造物',
    prefecture: '兵庫県',
    address: '兵庫県神戸市中央区北野町',
    latitude: 34.695556,
    longitude: 135.184444,
    imageUrl: '',
    openingHours: '9:00-18:00（館により異なる）',
    admissionFee: '館により異なる',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 1006,
    name: '淡路島',
    description: '瀬戸内海最大の島。美しい自然と温暖な気候に恵まれ、玉ねぎや海産物などの特産品でも有名な観光地です。',
    category: '自然・公園',
    prefecture: '兵庫県',
    address: '兵庫県淡路市',
    latitude: 34.387222,
    longitude: 134.906944,
    imageUrl: '',
    openingHours: '24時間',
    admissionFee: '無料',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  }
])
</script>

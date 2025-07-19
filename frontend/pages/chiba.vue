<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />


    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/12.jpeg" 
          alt="千葉県"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            千葉県
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
          <div class="text-6xl mb-4">🏰</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            千葉の観光スポットを準備中
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

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '千葉県 - Travel Voice',
  meta: [
    { name: 'description', content: '千葉県の観光スポットを音声ガイドで楽しもう。成田山新勝寺、東京ディズニーランド、鴨川シーワールドなど魅力ある観光地を発見。' }
  ]
})

// Reactive variables
const activeTab = ref('guide')

// Tourist spots data for Chiba
const touristSpots = ref<TouristSpot[]>([
  {
    id: 901,
    name: '成田山新勝寺',
    description: '真言宗智山派の大本山。初詣の参拝者数は全国屈指で、成田のお不動さんとして親しまれています。',
    category: '寺院',
    prefecture: '千葉県',
    address: '千葉県成田市成田1番地',
    latitude: 35.777778,
    longitude: 140.318889,
    imageUrl: '',
    openingHours: '6:00-16:30',
    admissionFee: '無料',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 902,
    name: '東京ディズニーランド',
    description: '夢と魔法の王国。世界中から愛されるディズニーキャラクターたちと過ごす特別な体験ができるテーマパークです。',
    category: 'テーマパーク',
    prefecture: '千葉県',
    address: '千葉県浦安市舞浜1-1',
    latitude: 35.632896,
    longitude: 139.880394,
    imageUrl: '',
    openingHours: '8:00-22:00（日により異なる）',
    admissionFee: '大人7900円〜',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 903,
    name: '鴨川シーワールド',
    description: '海の世界との出会いをテーマにした水族館。シャチやイルカのパフォーマンスで有名な日本を代表する海洋公園です。',
    category: '水族館',
    prefecture: '千葉県',
    address: '千葉県鴨川市東町1464-18',
    latitude: 35.114722,
    longitude: 140.123611,
    imageUrl: '',
    openingHours: '9:00-17:00',
    admissionFee: '大人3300円',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 904,
    name: '香取神宮',
    description: '日本全国の香取神社の総本社。武神・経津主大神を祀り、勝負運や厄除けのご利益で知られる由緒ある神社です。',
    category: '神社',
    prefecture: '千葉県',
    address: '千葉県香取市香取1697-1',
    latitude: 35.886111,
    longitude: 140.528889,
    imageUrl: '',
    openingHours: '8:30-17:00',
    admissionFee: '無料',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 905,
    name: '養老渓谷',
    description: '房総半島中央部に位置する自然豊かな渓谷。紅葉の名所として知られ、滝や温泉も楽しめる癒しの空間です。',
    category: '自然・公園',
    prefecture: '千葉県',
    address: '千葉県夷隅郡大多喜町養老渓谷',
    latitude: 35.267778,
    longitude: 140.156944,
    imageUrl: '',
    openingHours: '24時間',
    admissionFee: '無料',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 906,
    name: '犬吠埼灯台',
    description: '本州最東端の白い灯台。日本で一番早い初日の出を見ることができ、太平洋の大パノラマが楽しめます。',
    category: '自然・公園',
    prefecture: '千葉県',
    address: '千葉県銚子市犬吠埼9576',
    latitude: 35.706944,
    longitude: 140.867222,
    imageUrl: '',
    openingHours: '8:30-16:00',
    admissionFee: '大人300円',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  }
])
</script>

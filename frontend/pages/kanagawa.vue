<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Back Button -->
    <BackButton />

    <!-- Page Title -->
    <div class="bg-white dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-wide transition-colors duration-300">
            <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
              神奈川県
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
          <TouristSpotCard 
            v-for="spot in touristSpots" 
            :key="spot.id"
            :spot="spot"
            :show-tags="true"
          />
        </div>

        <!-- Empty State -->
        <div v-if="touristSpots.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🗾</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            神奈川の観光スポットを準備中
          </h3>
          <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">
            魅力的な観光地の音声ガイドをお楽しみに！
          </p>
        </div>
      </div>

    </main>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />

  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { TouristSpot } from '~/types'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'
import BackButton from '~/components/BackButton.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '神奈川県 - Travel Voice',
  meta: [
    { name: 'description', content: '神奈川県の観光スポットを音声ガイドで楽しもう。鎌倉大仏、箱根、横浜中華街など歴史と文化の魅力を発見。' }
  ]
})

// Reactive variables
const activeTab = ref('guide')

// Tourist spots data for Kanagawa
const touristSpots = ref<TouristSpot[]>([
  {
    id: 801,
    name: '鎌倉大仏（高徳院）',
    description: '日本で2番目に大きな大仏として親しまれる鎌倉のシンボル。高さ13.35mの阿弥陀如来坐像です。',
    category: '寺院',
    prefecture: '神奈川県',
    address: '神奈川県鎌倉市長谷4-2-28',
    latitude: 35.316667,
    longitude: 139.536111,
    imageUrl: '',
    openingHours: '8:00-17:30',
    admissionFee: '大人300円',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 802,
    name: '箱根神社',
    description: '芦ノ湖畔に建つ湖上の鳥居で有名な神社。関東総鎮守として多くの参拝者が訪れます。',
    category: '神社',
    prefecture: '神奈川県',
    address: '神奈川県足柄下郡箱根町元箱根80-1',
    latitude: 35.204444,
    longitude: 139.025556,
    imageUrl: '',
    openingHours: '6:00-17:00',
    admissionFee: '無料',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 803,
    name: '横浜中華街',
    description: '日本最大の中華街。約600店舗が軒を連ね、本格的な中華料理を楽しめる国際色豊かなエリアです。',
    category: '観光エリア',
    prefecture: '神奈川県',
    address: '神奈川県横浜市中区山下町',
    latitude: 35.442778,
    longitude: 139.645556,
    imageUrl: '',
    openingHours: '11:00-21:00（店舗により異なる）',
    admissionFee: '無料（店舗により異なる）',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 804,
    name: '江島神社',
    description: '江の島にある縁結びの神様として親しまれる神社。美しい海岸線と富士山の眺望も楽しめます。',
    category: '神社',
    prefecture: '神奈川県',
    address: '神奈川県藤沢市江の島2-3-8',
    latitude: 35.299444,
    longitude: 139.478889,
    imageUrl: '',
    openingHours: '8:30-17:00',
    admissionFee: '無料',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 805,
    name: '赤レンガ倉庫',
    description: '明治・大正時代の歴史的建造物を活用した文化・商業施設。横浜港の美しい夜景スポットとしても人気です。',
    category: '観光エリア',
    prefecture: '神奈川県',
    address: '神奈川県横浜市中区新港1-1',
    latitude: 35.453056,
    longitude: 139.643056,
    imageUrl: '',
    openingHours: '11:00-20:00',
    admissionFee: '無料（イベントにより異なる）',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  },
  {
    id: 806,
    name: '報国寺（竹寺）',
    description: '美しい竹林で有名な臨済宗の古刹。「竹の庭」では約2000本の孟宗竹が幻想的な空間を演出します。',
    category: '寺院',
    prefecture: '神奈川県',
    address: '神奈川県鎌倉市浄明寺2-7-4',
    latitude: 35.323611,
    longitude: 139.570833,
    imageUrl: '',
    openingHours: '9:00-16:00',
    admissionFee: '大人300円',
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString()
  }
])
</script>
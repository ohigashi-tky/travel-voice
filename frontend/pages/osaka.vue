<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />


    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/27.jpeg" 
          alt="大阪府"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            大阪府
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
            大阪の観光スポットを準備中
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
import { ref, onMounted } from 'vue'
import type { TouristSpot } from '~/types'
import AppHeader from '~/components/AppHeader.vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Osaka Guide - Discover Japan'
})

// Reactive variables
const activeTab = ref('top')

const touristSpots = ref<TouristSpot[]>([])


const generateSpotImage = (spotName: string, category: string) => {
  // 観光地ごとの画像URLを返す（確実に表示される画像）
  const imageMap: Record<string, string> = {
    '大阪城': '',
    '通天閣': '', 
    '海遊館': '',
    '浅草寺': '',
    '明治神宮': '',
    '清水寺': '',
    '金閣寺': '',
    '伏見稲荷大社': '',
    '札幌時計台': '',
    '函館山': '',
    '小樽運河': ''
  }
  
  return imageMap[spotName] || ''
}




const goToSpotDetail = (spotId: number) => {
  navigateTo(`/spots/${spotId}`)
}


// Load mock tourist spots data for Osaka
onMounted(() => {
  // Mock data for Osaka tourist spots
  touristSpots.value = [
    {
      id: 101,
      name: '大阪城',
      description: '豊臣秀吉が築城した名城。美しい天守閣と桜の名所として親しまれています。',
      category: '歴史建造物',
      prefecture: '大阪府',
      address: '大阪府大阪市中央区大阪城1-1',
      latitude: 34.6873,
      longitude: 135.5262,
      place_id: 'ChIJ_TooXM3gAGARQR6hXH3QAQ8',
      imageUrl: '',
      openingHours: '9:00-17:00',
      admissionFee: '大人600円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 102,
      name: '通天閣',
      description: '新世界のシンボルタワー。ビリケンさんで有名な大阪を代表する観光地です。',
      category: '展望台',
      prefecture: '大阪府',
      address: '大阪府大阪市浪速区恵美須東1-18-6',
      latitude: 34.6523,
      longitude: 135.5061,
      place_id: 'ChIJ_0Lgd2DnAGARV0X03lbPy-U',
      imageUrl: '',
      openingHours: '8:30-21:30',
      admissionFee: '大人900円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 103,
      name: '海遊館',
      description: '世界最大級の水族館。ジンベエザメやマンタが泳ぐ太平洋水槽は圧巻です。',
      category: '水族館',
      prefecture: '大阪府',
      address: '大阪府大阪市港区海岸通1-1-10',
      latitude: 34.6547,
      longitude: 135.4281,
      place_id: 'ChIJzakNjPToAGARzCwIriDFg28',
      imageUrl: '',
      openingHours: '10:00-20:00',
      admissionFee: '大人2700円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 104,
      name: '道頓堀',
      description: '大阪の代表的な繁華街。グリコの看板や川沿いのネオンサインで有名な観光エリアです。',
      category: '観光エリア',
      prefecture: '大阪府',
      address: '大阪府大阪市中央区道頓堀',
      latitude: 34.6686,
      longitude: 135.5023,
      imageUrl: '',
      openingHours: '24時間（店舗により異なる）',
      admissionFee: '無料（店舗により異なる）',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 105,
      name: '新世界',
      description: '通天閣を中心とした下町レトロエリア。串カツやお好み焼きなど大阪グルメの聖地です。',
      category: '観光エリア',
      prefecture: '大阪府',
      address: '大阪府大阪市浪速区恵美須東',
      latitude: 34.6520,
      longitude: 135.5065,
      imageUrl: '',
      openingHours: '24時間（店舗により異なる）',
      admissionFee: '無料（店舗により異なる）',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 106,
      name: '大阪駅・梅田',
      description: '関西最大の交通ハブ。ショッピング、グルメ、エンターテイメントが集まる西日本の玄関口です。',
      category: '観光エリア',
      prefecture: '大阪府',
      address: '大阪府大阪市北区梅田',
      latitude: 34.7024,
      longitude: 135.4959,
      place_id: 'ChIJC6fjlY3mAGARSshZ6CLIrhs',
      imageUrl: '',
      openingHours: '24時間（施設により異なる）',
      admissionFee: '無料（施設により異なる）',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  ]
})
</script>

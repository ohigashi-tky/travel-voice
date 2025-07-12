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
          src="/prefectures_image/1.jpeg" 
          alt="北海道"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            北海道
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
          <div class="text-6xl mb-4">🏔️</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            北海道の観光スポットを準備中
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
import { ref, onMounted } from 'vue'
import type { TouristSpot } from '~/types'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Hokkaido Guide - Discover Japan'
})

// Reactive variables
const activeTab = ref('top')

const touristSpots = ref<TouristSpot[]>([])




// Load mock tourist spots data for Hokkaido
onMounted(() => {
  // Mock data for Hokkaido tourist spots
  touristSpots.value = [
    {
      id: 301,
      name: '札幌時計台',
      description: '旧札幌農学校演武場として1878年に建設された北海道のシンボル的建造物です。',
      category: '歴史建造物',
      prefecture: '北海道',
      address: '北海道札幌市中央区北1条西2丁目',
      latitude: 43.0642,
      longitude: 141.3469,
      place_id: 'ChIJR3JQJ3YpC18R680ES0qomxs',
      imageUrl: '',
      openingHours: '8:45-17:10',
      admissionFee: '大人200円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 302,
      name: '函館山',
      description: '世界三大夜景の一つに数えられる美しい夜景スポット。津軽海峡を一望できます。',
      category: '展望台',
      prefecture: '北海道',
      address: '北海道函館市函館山',
      latitude: 41.7640,
      longitude: 140.6982,
      place_id: 'ChIJqUkGjFXynl8Rvj6P2613ojo',
      imageUrl: '',
      openingHours: '10:00-21:00（ロープウェイ）',
      admissionFee: 'ロープウェイ往復大人1500円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 303,
      name: '小樽運河',
      description: '1923年完成の歴史ある運河。石造倉庫群とガス灯が織りなすロマンチックな景観です。',
      category: '歴史的景観',
      prefecture: '北海道',
      address: '北海道小樽市港町',
      latitude: 43.1907,
      longitude: 140.9947,
      place_id: 'ChIJ0UxVV2ThCl8RIZdpda0H7gQ',
      imageUrl: '',
      openingHours: '24時間',
      admissionFee: '無料',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  ]
})
</script>
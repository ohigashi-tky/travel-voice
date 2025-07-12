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
          src="/prefectures_image/26.jpeg" 
          alt="京都府"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            京都府
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
          <div class="text-6xl mb-4">⛩️</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            京都の観光スポットを準備中
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
  title: 'Kyoto Guide - Discover Japan'
})

// Reactive variables
const activeTab = ref('top')

const touristSpots = ref<TouristSpot[]>([])




// Load mock tourist spots data for Kyoto
onMounted(() => {
  // Mock data for Kyoto tourist spots
  touristSpots.value = [
    {
      id: 201,
      name: '清水寺',
      description: '778年開創の京都最古の寺院。有名な清水の舞台と美しい景色で知られています。',
      category: '寺院',
      prefecture: '京都府',
      address: '京都府京都市東山区清水1-294',
      latitude: 34.9949,
      longitude: 135.7851,
      imageUrl: '',
      openingHours: '6:00-18:00',
      admissionFee: '大人400円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 202,
      name: '金閣寺',
      description: '足利義満の別荘として建てられた金箔で覆われた美しい楼閣。世界文化遺産です。',
      category: '寺院',
      prefecture: '京都府',
      address: '京都府京都市北区金閣寺町1',
      latitude: 35.0394,
      longitude: 135.7292,
      imageUrl: '',
      openingHours: '9:00-17:00',
      admissionFee: '大人500円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 203,
      name: '伏見稲荷大社',
      description: '全国の稲荷神社の総本宮。千本鳥居の美しい朱色のトンネルで有名です。',
      category: '神社',
      prefecture: '京都府',
      address: '京都府京都市伏見区深草藪之内町68',
      latitude: 34.9671,
      longitude: 135.7727,
      imageUrl: '',
      openingHours: '24時間',
      admissionFee: '無料',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  ]
})
</script>
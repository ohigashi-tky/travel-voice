<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />


    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/47.jpg" 
          alt="沖縄県"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-white tracking-wide">
            沖縄県
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
          <div class="text-6xl mb-4">🏖️</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            沖縄の観光スポットを準備中
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
  title: 'Okinawa Guide - Discover Japan'
})

// Reactive variables
const activeTab = ref('top')

const touristSpots = ref<TouristSpot[]>([])

const goToSpotDetail = (spotId: number) => {
  navigateTo(`/spots/${spotId}`)
}

// Load mock tourist spots data
onMounted(() => {
  // Mock data for Okinawa tourist spots
  touristSpots.value = [
    {
      id: 4701,
      name: '首里城',
      description: '琉球王国の王城として築かれた城で、沖縄県を代表する観光スポットです。世界遺産にも登録されており、琉球文化の象徴的存在です。',
      category: '歴史建造物',
      prefecture: '沖縄県',
      address: '沖縄県那覇市首里金城町1-2',
      latitude: 26.217,
      longitude: 127.719,
      place_id: 'ChIJZ9v0bP5r5TQRi0-esrqficA',
      imageUrl: '',
      openingHours: '9:00-18:00',
      admissionFee: '大人400円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 4702,
      name: '美ら海水族館',
      description: '世界最大級の水族館で、ジンベエザメやマンタが悠々と泳ぐ巨大水槽「黒潮の海」が有名です。',
      category: '水族館',
      prefecture: '沖縄県',
      address: '沖縄県国頭郡本部町石川424',
      latitude: 26.694,
      longitude: 127.878,
      place_id: 'ChIJPZ5hUjH65DQR_p_dD3CmCOo',
      imageUrl: '',
      openingHours: '8:30-18:30',
      admissionFee: '大人2180円',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    },
    {
      id: 4703,
      name: '国際通り',
      description: '那覇市の中心部にある約1.6kmの通りで、沖縄のお土産店や飲食店が立ち並ぶ観光のメインストリートです。',
      category: 'ショッピング',
      prefecture: '沖縄県',
      address: '沖縄県那覇市牧志',
      latitude: 26.211,
      longitude: 127.685,
      place_id: 'EjZLb2t1c2FpLWRvcmksIDItY2jFjW1lLTggTWFraXNoaSwgTmFoYSwgT2tpbmF3YSwgSmFwYW4iLiosChQKEgkxL0FRd2nlNBECXpFe9jlpjRIUChIJvZ-is2Rp5TQR826EwcNFk8M',
      imageUrl: '',
      openingHours: '店舗により異なる',
      admissionFee: '無料',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  ]
})
</script>

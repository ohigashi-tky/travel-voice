<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">

    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/35.jpeg" 
          alt="山口県"
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
            山口県
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
import { ref, computed, onMounted } from 'vue'
import { ArrowLeft, ArrowRight } from 'lucide-vue-next'
import TouristSpotCard from '~/components/TouristSpotCard.vue'
import { useTouristSpots } from '~/composables/useTouristSpots'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '山口県 - Travel Voice',
  meta: [
    { name: 'description', content: '山口県の観光スポットを音声ガイドで楽しもう。錦帯橋、秋芳洞、萩城下町など歴史と自然の魅力をご紹介。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// 観光地データをAPIから取得
const { spots, fetchSpots, getSpotsByPrefecture } = useTouristSpots()

// 山口県の観光スポット
const touristSpots = computed(() => {
  return getSpotsByPrefecture('山口県')
})

// データを初期化
onMounted(async () => {
  await fetchSpots()
})

// Navigation methods
const goHome = () => {
  navigateTo('/')
}

</script>

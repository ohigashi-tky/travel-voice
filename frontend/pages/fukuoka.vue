<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Back Button -->
    <BackButton />

    <!-- Page Title -->
    <div class="bg-white dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
          <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-wide transition-colors duration-300">
            <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent">
              福岡県
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
          <div 
            v-for="spot in touristSpots" 
            :key="spot.id"
            @click="goToSpotDetail(spot.id)"
            class="bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 dark:border-gray-700 cursor-pointer"
          >
            <!-- Spot Image -->
            <div class="h-48 bg-gradient-to-br from-red-400 to-pink-500 relative">
              <PlacePhotoImage 
                :spot-name="spot.name" :place-id="spot.place_id"
                :alt="spot.name"
              >
                <div class="absolute top-3 right-3">
                  <span class="bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white px-2 py-1 rounded-lg text-xs font-medium">
                    {{ spot.category }}
                  </span>
                </div>
              </PlacePhotoImage>
            </div>

            <!-- Spot Info -->
            <div class="p-4">
              <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">{{ spot.name }}</h3>
              <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2">{{ spot.description }}</p>
              
              <!-- Audio Guide Indicator -->
              <div class="flex items-center gap-2 text-xs text-red-600 dark:text-red-400">
                <span>🎧</span>
                <span>音声ガイド対応</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'

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

// Tourist spots data for Fukuoka
const touristSpots = [
  {
    id: 501,
    name: '太宰府天満宮',
    description: '学問の神様菅原道真公を祀る由緒ある神社。受験合格や学業成就を願う多くの参拝者が訪れます。',
    category: '神社'
  },
  {
    id: 502,
    name: '福岡城跡',
    description: '黒田長政が築城した福岡藩の居城跡。現在は舞鶴公園として桜の名所にもなっています。',
    category: '歴史建造物'
  },
  {
    id: 503,
    name: '博多駅',
    description: '九州の玄関口として知られる福岡の中心駅。周辺にはグルメや買い物スポットが充実しています。',
    category: '観光エリア'
  }
]

// Navigation functions

const goToSpotDetail = (spotId) => {
  console.log('Navigating to spot:', spotId)
  navigateTo(`/spots/${spotId}`)
}

</script>
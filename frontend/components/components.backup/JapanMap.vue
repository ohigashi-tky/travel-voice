<template>
  <div class="japan-map-container">
    <div class="relative w-full max-w-4xl mx-auto">
      <!-- Map Title -->
      <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">日本観光マップ</h2>
        <p class="text-gray-600">都道府県をクリックして観光スポットを探索しよう</p>
      </div>

      <!-- Simplified Japan Map -->
      <div class="relative bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <!-- Tokyo Region (Featured) -->
        <div class="relative flex justify-center items-center min-h-[400px]">
          <!-- Tokyo Prefecture -->
          <div
            class="relative group cursor-pointer"
            @click="selectPrefecture('東京都')"
          >
            <!-- Tokyo Shape (Simplified) -->
            <div class="tokyo-shape bg-gradient-to-br from-primary-400 to-primary-600 hover:from-primary-500 hover:to-primary-700 transition-all duration-300 transform group-hover:scale-105 shadow-lg group-hover:shadow-xl">
              <div class="flex items-center justify-center h-full">
                <div class="text-center text-white">
                  <Icon
                    name="lucide:building-2"
                    class="w-8 h-8 mx-auto mb-2"
                  />
                  <h3 class="font-bold text-lg">東京都</h3>
                  <p class="text-xs opacity-90">{{ tokyoSpots }}スポット</p>
                </div>
              </div>
            </div>
            
            <!-- Tooltip -->
            <div class="absolute -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
              <div class="bg-gray-900 text-white px-3 py-2 rounded-lg text-sm whitespace-nowrap">
                東京都の観光スポットを見る
              </div>
              <div class="w-3 h-3 bg-gray-900 transform rotate-45 -mt-1.5 mx-auto"></div>
            </div>
          </div>

          <!-- Coming Soon Areas -->
          <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center pointer-events-none">
            <div class="text-center space-y-4">
              <!-- Other prefectures placeholders -->
              <div class="flex justify-center space-x-4 mb-8">
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center opacity-50">
                  <span class="text-xs text-gray-500">神奈川</span>
                </div>
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center opacity-50">
                  <span class="text-xs text-gray-500">千葉</span>
                </div>
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center opacity-50">
                  <span class="text-xs text-gray-500">埼玉</span>
                </div>
              </div>
              
              <div class="flex justify-center space-x-4">
                <div class="w-20 h-16 bg-gray-200 rounded-lg flex items-center justify-center opacity-50">
                  <span class="text-xs text-gray-500">大阪</span>
                </div>
                <div class="w-20 h-16 bg-gray-200 rounded-lg flex items-center justify-center opacity-50">
                  <span class="text-xs text-gray-500">京都</span>
                </div>
              </div>

              <p class="text-sm text-gray-500 mt-8">
                他の都道府県は近日公開予定
              </p>
            </div>
          </div>
        </div>

        <!-- Legend -->
        <div class="mt-8 flex justify-center">
          <div class="flex items-center space-x-6 text-sm text-gray-600">
            <div class="flex items-center space-x-2">
              <div class="w-4 h-4 bg-gradient-to-br from-primary-400 to-primary-600 rounded"></div>
              <span>観光スポットあり</span>
            </div>
            <div class="flex items-center space-x-2">
              <div class="w-4 h-4 bg-gray-200 rounded"></div>
              <span>近日公開</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Featured Spots Preview -->
      <div
        v-if="selectedPrefecture"
        class="mt-8 card p-6 animate-fade-in"
      >
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-gray-900">
            {{ selectedPrefecture }}の観光スポット
          </h3>
          <BaseButton
            size="sm"
            @click="viewAllSpots"
          >
            すべて見る
          </BaseButton>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div
            v-for="spot in featuredSpots"
            :key="spot.id"
            class="card-hover p-4 cursor-pointer"
            @click="viewSpot(spot)"
          >
            <div class="aspect-video bg-gray-200 rounded-lg mb-3 flex items-center justify-center">
              <Icon
                name="lucide:image"
                class="w-8 h-8 text-gray-400"
              />
            </div>
            <h4 class="font-medium text-gray-900 mb-1">{{ spot.name }}</h4>
            <p class="text-sm text-gray-600 line-clamp-2">{{ spot.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { TouristSpot } from '~/types'

const selectedPrefecture = ref<string>('')
const tokyoSpots = ref(5) // Will be dynamic
const featuredSpots = ref<TouristSpot[]>([])

const selectPrefecture = async (prefecture: string) => {
  selectedPrefecture.value = prefecture
  
  // Fetch featured spots for the prefecture
  try {
    const { $api } = useNuxtApp()
    const spots = await $api<TouristSpot[]>(`/tourist-spots/prefecture/${prefecture}`)
    featuredSpots.value = spots.slice(0, 3) // Show first 3 spots
  } catch (error) {
    console.error('Error fetching spots:', error)
  }
}

const viewAllSpots = () => {
  navigateTo(`/spots?prefecture=${selectedPrefecture.value}`)
}

const viewSpot = (spot: TouristSpot) => {
  navigateTo(`/spots/${spot.id}`)
}

// Auto-select Tokyo on mount
onMounted(() => {
  selectPrefecture('東京都')
})
</script>

<style scoped>
.tokyo-shape {
  width: 120px;
  height: 100px;
  border-radius: 15px;
  position: relative;
}

.tokyo-shape::before {
  content: '';
  position: absolute;
  top: -5px;
  right: -5px;
  width: 30px;
  height: 30px;
  background: inherit;
  border-radius: 8px;
  transform: rotate(45deg);
}

.tokyo-shape::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 20px;
  width: 25px;
  height: 25px;
  background: inherit;
  border-radius: 6px;
  transform: rotate(-15deg);
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
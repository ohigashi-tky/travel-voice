<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Title Section -->
    <div class="py-6 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-2 mb-2">
          <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-md text-xs">
            観光エリア
          </span>
          <span class="text-gray-500 dark:text-gray-400 text-xs">東京都</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-2">
          銀座
        </h1>
        <p class="text-gray-600 dark:text-gray-300 text-sm">
          高級ブランド店が立ち並ぶ東京の代表的なショッピングエリア。洗練された大人の街として世界的に有名です。
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 pb-24">
      <div class="max-w-4xl mx-auto">
        
        <!-- Images Gallery -->
        <section class="mb-2 px-4">
          <div class="relative">
            <div class="flex gap-4 overflow-x-auto scroll-smooth gallery-scroll">
              <!-- Google Place Photo Main -->
              <div class="flex-shrink-0 w-full aspect-video rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                <PlacePhotoImage 
                  spot-name="銀座"
                  place-id="ChIJu2-DAeeLGGARUZipC7OFRmA"
                  alt="銀座 - メイン画像"
                  image-class="hover:scale-105 transition-transform duration-300"
                />
              </div>
              
              <!-- Additional images from Google Place Photos -->
              <div 
                v-for="(photo, index) in galleryPhotos" 
                :key="index"
                class="flex-shrink-0 w-full aspect-video rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300"
              >
                <img 
                  :src="photo.url" 
                  :alt="`銀座 - 画像 ${index + 2}`"
                  class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                />
              </div>
            </div>
            
            <!-- Image Indicators -->
            <div class="flex justify-center mt-4 space-x-2">
              <button
                v-for="(_, index) in getTotalImageCount"
                :key="index"
                class="w-2 h-2 rounded-full transition-all duration-300"
                :class="index === 0 ? 'bg-blue-600 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600'"
              />
            </div>
          </div>
        </section>

        <!-- Audio Guide Section -->
        <div class="px-4 sm:px-6 lg:px-8">
          <AudioGuideSimple 
            :spot-id="4"
            spot-name="銀座"
          />

          <!-- Combined Overview and History Section -->
          <section class="mb-6">
            <div class="space-y-4">
              <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                銀座は東京の中央区にある日本を代表する繁華街です。明治時代から続く老舗店舗と最新のブランドショップが共存し、洗練された大人の街として世界中から注目されています。銀座四丁目交差点を中心とした8つの丁目からなる街並みには、高級デパート、ブティック、ギャラリー、レストランなどが軒を連ね、ショッピングやグルメを楽しむことができます。
              </p>
              <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                江戸時代に徳川幕府が銀貨鋳造所（銀座）を置いたことが地名の由来です。明治5年（1872年）の銀座大火後、煉瓦街として復興され、文明開化の象徴となりました。大正時代にはカフェ文化が花開き、昭和期には百貨店が次々と開業。現在では世界有数の高級商業地区として発展しています。
              </p>
            </div>
          </section>
        </div>

        <!-- Google Map Section -->
        <section class="mb-8">
          <div class="px-4 sm:px-6 lg:px-8 mb-6">
            <GoogleMapEmbed 
              spot-name="銀座"
              place-id="ChIJu2-DAeeLGGARUZipC7OFRmA"
              :zoom="16"
            />
          </div>
          
          <!-- Unified Access Information Card -->
          <div class="mx-4 sm:mx-6 lg:mx-8 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 dark:border-gray-700">
              <button
                @click="setAccessTab('transport')"
                :class="[
                  'flex-1 px-6 py-4 text-sm font-medium transition-colors duration-200 flex items-center justify-center',
                  accessTab === 'transport' 
                    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                ]"
              >
                電車
              </button>
              <button
                @click="setAccessTab('car')"
                :class="[
                  'flex-1 px-6 py-4 text-sm font-medium transition-colors duration-200 flex items-center justify-center',
                  accessTab === 'car' 
                    ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-b-2 border-orange-600 dark:border-orange-400' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                ]"
              >
                車
              </button>
              <button
                @click="setAccessTab('parking')"
                :class="[
                  'flex-1 px-6 py-4 text-sm font-medium transition-colors duration-200 flex items-center justify-center',
                  accessTab === 'parking' 
                    ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 border-b-2 border-purple-600 dark:border-purple-400' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                ]"
              >
                駐車場
              </button>
            </div>

            <!-- Tab Content -->
            <div class="relative overflow-hidden">
              <div 
                class="flex transition-transform duration-300 ease-out"
                :style="{ transform: `translateX(-${currentTabIndex * 100}%)` }"
              >
                <!-- Public Transportation Tab -->
                <div class="w-full flex-shrink-0 p-4">
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                      <span class="block mb-2">
                        <span class="font-medium">JR山手線・京浜東北線</span>「有楽町駅」<span class="text-red-600 dark:text-red-400 font-medium">徒歩2分</span>
                      </span>
                      <span class="block mb-2">
                        <span class="font-medium">東京メトロ銀座線・丸ノ内線・日比谷線</span>「銀座駅」<span class="text-red-600 dark:text-red-400 font-medium">直結</span>
                      </span>
                      <span class="block mb-2">
                        <span class="font-medium">東京メトロ有楽町線</span>「銀座一丁目駅」<span class="text-red-600 dark:text-red-400 font-medium">徒歩1分</span>
                      </span>
                    </p>
                  </div>
                </div>

                <!-- Car Access Tab -->
                <div class="w-full flex-shrink-0 p-4">
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                      <span class="block mb-2">
                        首都高速都心環状線「銀座IC」<span class="text-red-600 dark:text-red-400 font-medium">より約3分</span>
                      </span>
                      <span class="block mb-2">
                        首都高速1号上野線「呉服橋IC」<span class="text-red-600 dark:text-red-400 font-medium">より約5分</span>
                      </span>
                    </p>
                  </div>
                </div>

                <!-- Parking Tab -->
                <div class="w-full flex-shrink-0 p-4">
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                      周辺に有料駐車場が多数あります。主要な駐車場：<br>
                      • 東銀座駐車場（24時間営業）<br>
                      • 銀座パーキングセンター<br>
                      • 松坂屋銀座店駐車場<br>
                      ※料金は時間制で、平日・土日により異なります
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

      </div>
    </main>
    
    <!-- Footer -->
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import AppHeader from '~/components/AppHeader.vue'
import AudioGuideSimple from '~/components/AudioGuideSimple.vue'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'
import GoogleMapEmbed from '~/components/GoogleMapEmbed.vue'
import { useGooglePlacePhotos } from '~/composables/useGooglePlacePhotos'

// Page meta
definePageMeta({
  middleware: 'auth'
})

// Set page title
useHead({
  title: '銀座 - Travel Voice'
})

// Access tab management
const accessTab = ref('transport')
const currentTabIndex = ref(0)

// Tab management functions
const setAccessTab = (tab: string) => {
  accessTab.value = tab
  const tabs = ['transport', 'car', 'parking']
  currentTabIndex.value = tabs.indexOf(tab)
}

// Gallery photos management
const { getGalleryPhotos } = useGooglePlacePhotos()
const galleryPhotos = ref([])

// Load gallery photos
const loadGalleryPhotos = async () => {
  try {
    const photos = await getGalleryPhotos('銀座', 'ChIJu2-DAeeLGGARUZipC7OFRmA')
    // Skip the first photo (already shown in main image), take next 4 photos
    galleryPhotos.value = photos.slice(1, 5)
  } catch (error) {
    console.error('Error loading gallery photos:', error)
    galleryPhotos.value = []
  }
}

// Total image count for indicators
const getTotalImageCount = computed(() => {
  return 1 + galleryPhotos.value.length // 1 main image + gallery images
})

// Load gallery photos on mount
onMounted(() => {
  loadGalleryPhotos()
})
</script>

<style scoped>
.gallery-scroll {
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.gallery-scroll::-webkit-scrollbar {
  display: none;
}
</style>

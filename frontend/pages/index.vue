<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />
    
    <!-- Main Content -->
    <div class="flex-1 relative z-10 pb-24 pt-16">
      <div class="p-6">
        <div class="max-w-7xl mx-auto text-center">
          <h1 class="text-7xl font-bold text-gray-800 dark:text-white mb-4 tracking-wider text-center transition-colors duration-300">
            <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent font-extrabold">
              Travel Voice
            </span>
          </h1>
          <p class="text-xl text-gray-600 dark:text-gray-300 font-medium max-w-2xl mx-auto mb-8 tracking-wide transition-colors duration-300">
            音声ガイドで観光を楽しもう
          </p>
        </div>
        
        <div class="max-w-6xl mx-auto">
          <!-- Functional Map Area -->
          <div class="bg-gray-50 dark:bg-gray-800 rounded-3xl p-8 mb-8 border border-gray-200 dark:border-gray-700 transition-colors duration-300">            
            <!-- Prefecture Selection -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <button
                v-for="prefecture in mainPrefectures"
                :key="prefecture.name"
                @click="selectPrefecture(prefecture)"
                :class="[
                  'group p-4 rounded-xl border transition-all duration-300',
                  prefecture.available 
                    ? 'bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600 transform hover:scale-105 cursor-pointer'
                    : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed'
                ]"
                :disabled="!prefecture.available"
              >
                <div class="text-3xl mb-2">{{ prefecture.emoji }}</div>
                <h4 :class="[
                  'font-light text-sm transition-colors tracking-wide',
                  prefecture.available 
                    ? 'text-gray-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400'
                    : 'text-gray-500 dark:text-gray-400'
                ]">
                  {{ prefecture.name }}
                </h4>
              </button>
            </div>
          </div>
          
          <!-- App Features Panel -->
          <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 text-center transition-colors duration-300">
            <div class="flex items-center justify-center gap-3 mb-4">
              <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                <span class="text-white text-xl">🎧</span>
              </div>
              <h3 class="text-gray-800 dark:text-white text-xl font-light tracking-wide transition-colors duration-300">TravelVoiceの使い方</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-4 font-thin tracking-wide transition-colors duration-300">
              旅先で音声ガイドを聴きながら、歴史や文化を深く学ぶことができます。
            </p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />
    
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '~/stores/auth'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Travel Voice - 音声で楽しむ日本全国の魅力',
  meta: [
    { name: 'description', content: '日本全国47都道府県の観光スポットを音声ガイドで楽しもう。地図から簡単アクセス、プロのガイドで深く学べる日本の歴史と文化。' }
  ]
})

// Auth store
const authStore = useAuthStore()

// Reactive variables
const activeTab = ref('top')

// Initialize auth state on mount (middleware already handles authentication)
onMounted(() => {
  // Auth is already initialized by plugin and checked by middleware
})

const mainPrefectures = [
  { name: '東京都', emoji: '🗼', available: true },
  { name: '大阪府', emoji: '🏯', available: true },
  { name: '京都府', emoji: '⛩️', available: true },
  { name: '北海道', emoji: '🐄', available: true }
]

const selectPrefecture = async (prefecture) => {
  console.log('Prefecture selected:', prefecture.name)
  
  if (prefecture.available) {
    if (prefecture.name === '東京都') {
      console.log('Navigating to Tokyo guide...')
      try {
        await navigateTo('/tokyo')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('東京ガイドページに移動中です...')
      }
    } else if (prefecture.name === '大阪府') {
      console.log('Navigating to Osaka guide...')
      try {
        await navigateTo('/osaka')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('大阪ガイドページに移動中です...')
      }
    } else if (prefecture.name === '京都府') {
      console.log('Navigating to Kyoto guide...')
      try {
        await navigateTo('/kyoto')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('京都ガイドページに移動中です...')
      }
    } else if (prefecture.name === '北海道') {
      console.log('Navigating to Hokkaido guide...')
      try {
        await navigateTo('/hokkaido')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('北海道ガイドページに移動中です...')
      }
    }
  } else {
    alert(`${prefecture.name}のガイドは準備中です。しばらくお待ちください。`)
  }
}

</script>
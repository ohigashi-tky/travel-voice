<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative flex flex-col transition-colors duration-300">
    
    <!-- Main Content (above the image with no gap) -->
    <div class="relative z-10">
        <div class="max-w-6xl mx-auto">
          <!-- Divider -->
          <div class="border-t border-gray-200 dark:border-gray-700 mb-3"></div>
          
          <!-- Navigation Links Section -->
          <NavigationLinks />

        </div>
      
      <!-- Hero Image Fade Slider with Search Overlay -->
      <HeroSliderWithSearch ref="heroSliderRef" />
      
      <div class="px-4 -mt-3">
        <div class="max-w-6xl mx-auto">
          <!-- Popular Spots Section -->
          <PopularSpots ref="popularSpotsRef" />
          
          <!-- Prefecture Selection -->
          <PrefectureSelection />
          
          <!-- Category Selection -->
          <CategorySelection />
          
          <!-- Footer Links and Copyright -->
          <PageFooterLinks />
        </div>
      </div>
    </div>
    
    <!-- PWA Install Prompt -->
    <PWAInstallPrompt />
    
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useLanguage } from '~/composables/useLanguage'
import PWAInstallPrompt from '~/components/PWAInstallPrompt.vue'
import PopularSpots from '~/components/PopularSpots.vue'
import PrefectureSelection from '~/components/PrefectureSelection.vue'
import CategorySelection from '~/components/CategorySelection.vue'
import NavigationLinks from '~/components/NavigationLinks.vue'
import HeroSliderWithSearch from '~/components/HeroSliderWithSearch.vue'
import PageFooterLinks from '~/components/PageFooterLinks.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'おうち旅行 - 音声ガイドで日本を巡ろう',
  meta: [
    { name: 'description', content: '歴史やエピソードを聞きながら、家にいながら本格的な旅行気分を味わえます。' }
  ]
})

// Language
const { t } = useLanguage()

// Component refs
const popularSpotsRef = ref(null)
const heroSliderRef = ref(null)



// データは useTouristSpots コンポーザブルから取得

// Initialize auth state on mount (middleware already handles authentication)
onMounted(async () => {
  // Auth is already initialized by plugin and checked by middleware
  // Hero slider and placeholder rotation are handled by the HeroSliderWithSearch component
})

onUnmounted(() => {
  // Cleanup is handled by individual components
})











</script>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>

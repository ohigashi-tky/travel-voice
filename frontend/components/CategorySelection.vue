<template>
  <!-- Category Selection -->
  <div class="bg-white/40 dark:bg-gray-800/40 backdrop-blur-md border border-white/20 dark:border-gray-700/30 rounded-lg py-2 px-2 mb-8 transition-all duration-300 relative z-10 shadow-lg">
    <div class="text-center mb-2">
      <h3 class="text-gray-800 dark:text-white text-2xl font-bold tracking-wide transition-colors duration-300" style="font-family: 'Inter', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif; font-weight: 700; letter-spacing: 0.05em;">{{ t('カテゴリから探す') }}</h3>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <button
        v-for="category in categoryList"
        :key="category.name"
        @click="selectCategory(category)"
        class="group relative px-2 h-20 rounded-lg border border-gray-200 dark:border-gray-600 transform hover:scale-105 cursor-pointer transition-all duration-300 overflow-hidden flex items-center justify-center"
      >
        <!-- Background Image -->
        <div class="absolute inset-0">
          <img 
            :src="category.image" 
            :alt="category.name"
            class="w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-black/40"></div>
        </div>
        
        <!-- Category Name -->
        <h4 class="relative z-10 font-medium text-sm text-white tracking-wide leading-none text-center">
          {{ category.name }}
        </h4>
      </button>
    </div>
  </div>
</template>

<script setup>
import { useLanguage } from '~/composables/useLanguage'

// Language
const { t } = useLanguage()

// カテゴリリスト
const categoryList = [
  { name: '寺院', image: '/category/tera.jpg' },
  { name: '歴史建造物', image: '/category/rekishi.jpg' },
  { name: '神社', image: '/category/jinjya.jpg' },
  { name: '展望台', image: '/category/tenbodai.jpg' },
  { name: '博物館', image: '/category/hakubutsukan.jpeg' },
  { name: '観光エリア', image: '/category/kanko.jpg' }
]

const selectCategory = (category) => {
  navigateTo(`/category?name=${encodeURIComponent(category.name)}`)
}
</script>
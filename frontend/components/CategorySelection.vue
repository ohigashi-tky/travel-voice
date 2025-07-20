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
        <!-- Loading Placeholder -->
        <div 
          v-if="!imageLoaded[category.name]"
          class="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center"
        >
          <div class="animate-pulse text-gray-400 dark:text-gray-500 text-xs">読み込み中...</div>
        </div>
        
        <!-- Background Image -->
        <img 
          :src="category.image" 
          :alt="category.name"
          class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
          :class="{ 'opacity-0': !imageLoaded[category.name] }"
          loading="eager"
          decoding="async"
          @load="handleImageLoad(category.name)"
          @error="handleImageError"
        />
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/40 group-hover:bg-opacity-30 transition-all duration-300"></div>
        
        <!-- Category Name -->
        <h4 class="relative z-10 font-medium text-sm text-white tracking-wide leading-none text-center">
          {{ category.name }}
        </h4>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useLanguage } from '~/composables/useLanguage'

// Language
const { t } = useLanguage()

// 画像読み込み状態を管理
const imageLoaded = ref({})

// カテゴリリスト
const categoryList = [
  { name: '寺院', image: '/category/tera.jpg' },
  { name: '歴史建造物', image: '/category/rekishi.jpg' },
  { name: '神社', image: '/category/jinjya.jpg' },
  { name: '展望台', image: '/category/tenbodai.jpg' },
  { name: '博物館', image: '/category/hakubutsukan.jpeg' },
  { name: '観光エリア', image: '/category/kanko.jpg' }
]

// 画像読み込み成功時の処理
const handleImageLoad = (categoryName) => {
  imageLoaded.value[categoryName] = true
}

// 画像読み込みエラーハンドリング
const handleImageError = (event) => {
  console.warn('Failed to load category image:', event.target.src)
  // フォールバック画像の設定（任意）
  // event.target.src = '/path/to/fallback-image.jpg'
}

const selectCategory = (category) => {
  navigateTo(`/category?name=${encodeURIComponent(category.name)}`)
}
</script>
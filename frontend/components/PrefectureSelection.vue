<template>
  <!-- Prefecture Selection -->
  <div class="bg-white/40 dark:bg-gray-800/40 backdrop-blur-md border border-white/20 dark:border-gray-700/30 rounded-lg py-2 px-2 mb-4 transition-all duration-300 relative z-10 shadow-lg">
    <div class="text-center mb-2">
      <h3 class="text-gray-800 dark:text-white text-2xl font-bold tracking-wide transition-colors duration-300" style="font-family: 'Inter', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif; font-weight: 700; letter-spacing: 0.05em;">{{ t('都道府県から探す') }}</h3>
    </div>
    <!-- Loading State for Prefecture Grid -->
    <div v-if="prefecturesLoading" class="flex items-center justify-center py-8">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
      <span class="ml-3 text-gray-600 dark:text-gray-300">読み込み中...</span>
    </div>
    
    <!-- Prefecture Grid -->
    <div v-else class="grid grid-cols-3 gap-3">
      <button
        v-for="prefecture in mainPrefectures"
        :key="prefecture.name"
        @click="selectPrefecture(prefecture)"
        :class="[
          'group relative h-20 rounded-lg border transition-all duration-300 overflow-hidden',
          prefecture.available 
            ? 'border-gray-200 dark:border-gray-600 transform hover:scale-105 cursor-pointer'
            : 'border-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed'
        ]"
        :disabled="!prefecture.available"
      >
        <!-- Loading Placeholder -->
        <div 
          v-if="!imageLoaded[prefecture.id]"
          class="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center"
        >
          <div class="animate-pulse text-gray-400 dark:text-gray-500 text-xs">読み込み中...</div>
        </div>
        
        <!-- Background Image -->
        <img 
          :src="getPrefectureImagePath(prefecture.id)"
          :alt="`${prefecture.name}の風景`"
          class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
          :class="{ 'opacity-0': !imageLoaded[prefecture.id] }"
          loading="eager"
          decoding="async"
          @load="handleImageLoad(prefecture.id)"
          @error="handleImageError"
        />
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-30 transition-all duration-300"></div>
        <!-- Prefecture Name -->
        <div class="relative z-10 h-full flex items-center justify-center">
          <h4 class="text-white font-medium text-sm text-center px-2 leading-tight">
            {{ prefecture.name }}
          </h4>
        </div>
      </button>
    </div>
    
    <!-- All Prefectures Button -->
    <div class="mt-6 text-center">
      <button
        @click="showPrefectureModal = true"
        class="inline-flex items-center gap-1 px-4 py-1.5 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
      >
        <span class="text-sm">すべて表示</span>
        <span class="text-xs">→</span>
      </button>
    </div>
  </div>

  <!-- Prefecture Modal -->
  <div
    v-if="showPrefectureModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click="closePrefectureModal"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto"
      @click.stop
    >
      <!-- Modal Header -->
      <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">都道府県を選択</h2>
        <button
          @click="showPrefectureModal = false"
          class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-2xl"
        >
          ×
        </button>
      </div>
      
      <!-- Modal Content -->
      <div class="p-6">
        <!-- Loading State -->
        <div v-if="prefecturesLoading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-3 text-gray-600 dark:text-gray-300">都道府県データを読み込み中...</span>
        </div>
        
        <!-- Prefecture Regions -->
        <div
          v-else
          v-for="region in prefectureRegions"
          :key="region.name"
          class="mb-8 last:mb-0"
        >
          <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">{{ region.name }}</h3>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            <button
              v-for="prefecture in region.prefectures"
              :key="prefecture.name"
              @click="selectPrefectureFromModal(prefecture)"
              :disabled="!prefecture.available"
              :class="[
                'relative h-16 rounded-lg border transition-all duration-200 overflow-hidden',
                prefecture.available
                  ? 'border-gray-200 dark:border-gray-600 hover:scale-105 cursor-pointer'
                  : 'border-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed'
              ]"
            >
              <!-- Loading Placeholder -->
              <div 
                v-if="!imageLoaded[prefecture.id]"
                class="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center"
              >
                <div class="animate-pulse text-gray-400 dark:text-gray-500 text-xs">読み込み中...</div>
              </div>
              
              <!-- Background Image -->
              <img 
                :src="getPrefectureImagePath(prefecture.id)"
                :alt="`${prefecture.name}の風景`"
                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                :class="{ 'opacity-0': !imageLoaded[prefecture.id] }"
                loading="lazy"
                decoding="async"
                @load="handleImageLoad(prefecture.id)"
                @error="handleImageError"
              />
              <!-- Overlay -->
              <div class="absolute inset-0 bg-black bg-opacity-40 hover:bg-opacity-30 transition-all duration-200"></div>
              <!-- Prefecture Name -->
              <div class="relative h-full flex items-center justify-center">
                <span class="text-white text-sm font-medium text-center px-2 leading-tight">
                  {{ prefecture.name }}
                </span>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, onUnmounted } from 'vue'
import { useLanguage } from '~/composables/useLanguage'
import { useTopPageData } from '~/composables/useTopPageData'

// Language
const { t } = useLanguage()

// 都道府県データ - 最適化されたAPI経由で取得
const { 
  featuredPrefectures,
  regionalPrefectures,
  fetchTopPageData,
  isLoading: prefecturesLoading,
  error: prefecturesError
} = useTopPageData()

// Modal state
const showPrefectureModal = ref(false)

// 都道府県データを初期化時に取得
const mainPrefectures = computed(() => 
  featuredPrefectures.value.map(p => ({
    id: p.id,
    name: p.name,
    available: true // APIから取得されるデータは利用可能と仮定
  }))
)

const prefectureRegions = computed(() => 
  regionalPrefectures.value.map(region => ({
    name: region.region,
    prefectures: region.prefectures.map(p => ({
      id: p.id,
      name: p.name,
      available: true // APIから取得されるデータは利用可能と仮定
    }))
  }))
)

// 画像読み込み状態を管理
const imageLoaded = ref({})

// 都道府県の背景画像パスを生成
const getPrefectureImagePath = (prefectureId) => {
  if (!prefectureId) return ''
  
  // ID 25, 33, 38, 44, 47は.jpg形式、その他は.jpeg形式
  const jpgIds = [25, 33, 38, 44, 47]
  const extension = jpgIds.includes(prefectureId) ? 'jpg' : 'jpeg'
  
  return `/prefectures_image/${prefectureId}.${extension}`
}

// 画像読み込み成功時の処理
const handleImageLoad = (prefectureId) => {
  imageLoaded.value[prefectureId] = true
}

// 画像読み込みエラーハンドリング
const handleImageError = (event) => {
  console.warn('Failed to load prefecture image:', event.target.src)
  // フォールバック画像の設定（任意）
  // event.target.src = '/path/to/fallback-image.jpg'
}

const selectPrefecture = async (prefecture) => {
  if (!prefecture.available) {
    alert(`${prefecture.name}のガイドは準備中です。しばらくお待ちください。`)
    return
  }

  try {
    // 都道府県IDベースのルートに遷移
    await navigateTo(`/prefecture/${prefecture.id}`)
  } catch (error) {
    console.error('Navigation error:', error)
    console.warn(`Failed to navigate to ${prefecture.name}. Attempting fallback...`)
    // フォールバック: 名前ベースのルートを試行
    await navigateTo(`/prefecture/${encodeURIComponent(prefecture.name)}`)
  }
}

// モーダル関連の関数
const closePrefectureModal = (event) => {
  if (event.target === event.currentTarget) {
    showPrefectureModal.value = false
  }
}

const selectPrefectureFromModal = async (prefecture) => {
  showPrefectureModal.value = false
  await selectPrefecture(prefecture)
}

// モーダル表示時のスクロール制御
watch(showPrefectureModal, (newValue) => {
  if (newValue) {
    // モーダル表示時：bodyのスクロールを無効化
    document.body.style.overflow = 'hidden'
  } else {
    // モーダル非表示時：bodyのスクロールを有効化
    document.body.style.overflow = ''
  }
})

// Initialize on mount
onMounted(() => {
  fetchTopPageData()
})

// クリーンアップ：ページ離脱時にスクロールを復元
onUnmounted(() => {
  if (showPrefectureModal.value) {
    document.body.style.overflow = ''
  }
})
</script>
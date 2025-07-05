<template>
  <button 
    @click="handleBack"
    class="fixed top-20 left-6 z-50 flex items-center gap-2 text-gray-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 group bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full px-4 py-2 shadow-lg"
  >
    <ArrowLeft class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" />
    <span class="text-sm font-medium">戻る</span>
  </button>
</template>

<script setup lang="ts">
import { ArrowLeft } from 'lucide-vue-next'

interface Props {
  fallbackRoute?: string
}

const props = withDefaults(defineProps<Props>(), {
  fallbackRoute: '/'
})

const handleBack = () => {
  // PWA対応: Nuxtのナビゲーション機能を使用
  try {
    // Nuxtのルーター履歴を使用
    const router = useRouter()
    if (router.options.history.state.back) {
      router.back()
    } else {
      // 履歴がない場合はfallbackRouteに戻る
      navigateTo(props.fallbackRoute)
    }
  } catch (error) {
    // エラー時のフォールバック
    navigateTo(props.fallbackRoute)
  }
}
</script>
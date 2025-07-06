<template>
  <button 
    @click="handleBack"
    class="fixed top-20 left-6 z-50 flex items-center justify-center text-gray-700/80 dark:text-white/80 hover:text-gray-800 dark:hover:text-white hover:bg-white/80 dark:hover:bg-gray-800/80 transition-all duration-300 bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full w-10 h-10 shadow-sm hover:shadow-lg"
  >
    <span class="text-lg">←</span>
  </button>
</template>

<script setup lang="ts">

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
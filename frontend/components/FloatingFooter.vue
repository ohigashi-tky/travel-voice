<template>
  <!-- 浮遊フッター -->
  <div 
    class="fixed bottom-3 left-0 right-0 z-40 pointer-events-none transition-all duration-300 ease-in-out"
    :class="[
      isVisible 
        ? 'translate-y-0 opacity-100' 
        : 'translate-y-full opacity-0'
    ]"
  >
    <div class="max-w-md mx-auto px-6">
      <div class="flex items-center justify-between pointer-events-auto">
        <!-- 戻るボタン -->
        <button 
          v-if="canGoBack"
          @click="goBack"
          class="w-12 h-12 bg-white/30 dark:bg-gray-800/30 backdrop-blur-md rounded-full flex items-center justify-center shadow-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-200 hover:scale-110"
        >
          <ChevronLeft class="w-6 h-6 text-gray-700 dark:text-gray-300" />
        </button>
        <!-- 空のスペース（戻るボタンがない場合） -->
        <div v-else class="w-12"></div>
        
        <!-- ホームボタン -->
        <button 
          @click="goToHome"
          class="w-11 h-11 bg-white/30 dark:bg-gray-800/30 backdrop-blur-md rounded-full flex items-center justify-center shadow-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-200 hover:scale-110"
        >
          <Home class="w-6 h-6 text-gray-700 dark:text-gray-300" />
        </button>
        
        <!-- AIエージェントボタン -->
        <button 
          @click="goToAI"
          class="w-12 h-12 bg-white/30 dark:bg-gray-800/30 backdrop-blur-md rounded-full flex items-center justify-center shadow-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-200 hover:scale-110"
        >
          <Bot class="w-6 h-6 text-gray-700 dark:text-gray-300" />
        </button>
      </div>
    </div>
  </div>
</template>

<style>
/* フッターとコンテンツが重ならないように最小限の余白のみ追加 */
main {
  padding-bottom: 5rem;
}
</style>

<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import { Home, Bot, ChevronLeft } from 'lucide-vue-next'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

// Reactive variables for scroll detection
const isVisible = ref(true)
const lastScrollY = ref(0)
const scrollThreshold = 20 // スクロール検知の閾値

// 戻るボタンが表示可能かチェック
const canGoBack = computed(() => {
  // ホームページでは戻るボタンを表示しない
  if (route.path === '/') return false
  
  // ログインページでは戻るボタンを表示しない
  if (route.path === '/login') return false
  
  // その他のページでは表示
  return true
})

// スクロール検知関数
const handleScroll = () => {
  const currentScrollY = window.scrollY
  const scrollDelta = currentScrollY - lastScrollY.value

  // ページの底に近いかチェック
  const isNearBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 10

  // 底に近い場合は常に表示
  if (isNearBottom) {
    isVisible.value = true
    lastScrollY.value = currentScrollY
    return
  }

  // 閾値を超えたスクロールのみ処理
  if (Math.abs(scrollDelta) > scrollThreshold) {
    if (scrollDelta > 0) {
      // 下スクロール: フッターを隠す
      isVisible.value = false
    } else {
      // 上スクロール: フッターを表示
      isVisible.value = true
    }
    lastScrollY.value = currentScrollY
  }
}

// ナビゲーション関数
const goBack = () => {
  router.back()
}

const goToHome = () => {
  navigateTo('/')
}

const goToAI = () => {
  navigateTo('/ai-agent')
}

// ライフサイクル
onMounted(() => {
  lastScrollY.value = window.scrollY
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

// ルート変更時にフッターを表示
watch(() => route.path, () => {
  isVisible.value = true
  lastScrollY.value = window.scrollY
})
</script>
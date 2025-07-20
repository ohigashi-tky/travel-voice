<template>
  <!-- iOS 16 スタイル リキッドガラス浮遊フッター -->
  <div 
    class="fixed bottom-3 left-0 right-0 z-40 pointer-events-none transition-all duration-500 ease-out"
    :class="[
      isVisible 
        ? 'translate-y-0 opacity-100 footer-visible' 
        : 'translate-y-full opacity-0 footer-hidden'
    ]"
  >
    <div class="max-w-md mx-auto px-6">
      <!-- iOS 16スタイル 直接配置ボタン -->
      <div class="flex items-center justify-center gap-16 pointer-events-auto">
        <!-- 戻るボタン -->
        <button 
          v-if="canGoBack"
          @click="goBack"
          class="liquid-glass-button group"
        >
          <div class="button-content">
            <ChevronLeft class="w-6 h-6 text-black/90 dark:text-white/90 group-active:text-black/90 dark:group-active:text-white/90 transition-colors duration-150" />
          </div>
        </button>
        <!-- 空のスペース（戻るボタンがない場合） -->
        <div v-else class="w-12 opacity-0"></div>
        
        <!-- ホームボタン -->
        <button 
          @click="goToHome"
          class="liquid-glass-button group"
        >
          <div class="button-content">
            <Home class="w-6 h-6 text-black/90 dark:text-white/90 group-active:text-black/90 dark:group-active:text-white/90 transition-colors duration-150" />
          </div>
        </button>
        
        <!-- AIエージェントボタン -->
        <button 
          @click="goToAI"
          class="liquid-glass-button group"
        >
          <div class="button-content">
            <Bot class="w-6 h-6 text-black/90 dark:text-white/90 group-active:text-black/90 dark:group-active:text-white/90 transition-colors duration-150" />
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* フッターとコンテンツが重ならないように最小限の余白のみ追加 */
main {
  padding-bottom: 5rem;
}

/* iOS 16 リキッドガラス ボタン - 超透明版 */
.liquid-glass-button {
  /* 基本サイズと形状 */
  width: 48px;
  height: 48px;
  border-radius: 16px;
  padding: 0;
  border: none;
  cursor: pointer;
  overflow: hidden;
  position: relative;
  
  /* 透明ガラスモーフィズム - 背景色のみ透明 */
  background: transparent;
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
  
  /* 極細の境界線 */
  border: 0.5px solid rgba(255, 255, 255, 0.15);
  
  /* 繊細なシャドウ */
  box-shadow: 
    0 4px 20px rgba(0, 0, 0, 0.08),
    0 1px 3px rgba(0, 0, 0, 0.04),
    inset 0 1px 0 rgba(255, 255, 255, 0.15),
    inset 0 -0.5px 0 rgba(255, 255, 255, 0.08);
  
  /* アニメーション */
  transition: all 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  
  /* ダークモード - 背景色のみ透明 */
  @media (prefers-color-scheme: dark) {
    background: transparent;
    border: 0.5px solid rgba(255, 255, 255, 0.12);
    box-shadow: 
      0 4px 20px rgba(0, 0, 0, 0.15),
      0 1px 3px rgba(0, 0, 0, 0.1),
      inset 0 1px 0 rgba(255, 255, 255, 0.1),
      inset 0 -0.5px 0 rgba(255, 255, 255, 0.05);
  }
}

/* ボタンコンテンツ中央配置 */
.button-content {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  position: relative;
  z-index: 1;
}

/* ホバー効果 - デスクトップ（背景色変更なし） */
@media (hover: hover) {
  .liquid-glass-button:hover {
    transform: scale(1.05);
    /* 背景色は維持 */
    box-shadow: 
      0 6px 24px rgba(0, 0, 0, 0.15),
      0 2px 6px rgba(0, 0, 0, 0.1),
      inset 0 1px 0 rgba(255, 255, 255, 0.25),
      inset 0 -0.5px 0 rgba(255, 255, 255, 0.12);
  }
  
  @media (prefers-color-scheme: dark) {
    .liquid-glass-button:hover {
      /* 背景色は維持 */
      box-shadow: 
        0 6px 24px rgba(0, 0, 0, 0.3),
        0 2px 6px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.18),
        inset 0 -0.5px 0 rgba(255, 255, 255, 0.1);
    }
  }
}

/* アクティブ効果 - タッチデバイス（透明のまま） */
.liquid-glass-button:active {
  transform: scale(0.95);
  /* 背景色は変更せず、影とスケールのみ変更 */
  box-shadow: 
    0 2px 12px rgba(0, 0, 0, 0.12),
    0 1px 4px rgba(0, 0, 0, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.2),
    inset 0 -0.5px 0 rgba(255, 255, 255, 0.1);
  transition: all 0.1s ease-out;
}

@media (prefers-color-scheme: dark) {
  .liquid-glass-button:active {
    /* ダークモードでも背景色は変更しない */
    box-shadow: 
      0 2px 12px rgba(0, 0, 0, 0.25),
      0 1px 4px rgba(0, 0, 0, 0.15),
      inset 0 1px 0 rgba(255, 255, 255, 0.15),
      inset 0 -0.5px 0 rgba(255, 255, 255, 0.08);
  }
}

/* フォーカス状態 - アクセシビリティ */
.liquid-glass-button:focus-visible {
  outline: none;
  border: 1px solid rgba(0, 122, 255, 0.6);
  box-shadow: 
    0 4px 16px rgba(0, 0, 0, 0.1),
    0 0 0 3px rgba(0, 122, 255, 0.2),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

/* サファリ用の最適化 */
@supports (-webkit-backdrop-filter: blur(3px)) {
  .liquid-glass-button {
    -webkit-backdrop-filter: blur(3px);
  }
}

/* 高解像度ディスプレイでの境界線最適化 */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .liquid-glass-button {
    border-width: 0.5px;
  }
}

/* アニメーション性能向上 */
.liquid-glass-button {
  will-change: transform, backdrop-filter;
  -webkit-transform: translateZ(0);
  transform: translateZ(0);
}

/* フッター非表示時は最適化のためblurを解除 */
/* .footer-hidden .liquid-glass-button {
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
} */
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
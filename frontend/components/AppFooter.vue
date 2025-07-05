<template>
  <!-- フッターコンテナ -->
  <div class="fixed bottom-0 left-0 right-0 z-40">
    <!-- フッター本体 -->
    <footer 
      class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 transition-all duration-300 ease-in-out shadow-lg"
      :class="[
        isVisible 
          ? 'translate-y-0 opacity-100' 
          : 'translate-y-full opacity-0 pointer-events-none'
      ]"
      style="height: calc(4rem + env(safe-area-inset-bottom, 0px)); padding-bottom: env(safe-area-inset-bottom, 0px);"
    >
      <div class="h-16 flex items-center justify-around">
        <button 
          @click="goToTop"
          :class="[
            'flex flex-col items-center justify-center flex-1 h-full transition-all duration-200 rounded-lg',
            activeTab === 'top' 
              ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' 
              : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'
          ]"
        >
          <Home class="w-6 h-6 mb-1" />
          <span class="text-xs font-medium tracking-wide">Top</span>
        </button>
        
        <button 
          @click="goToAI"
          :class="[
            'flex flex-col items-center justify-center flex-1 h-full transition-all duration-200 rounded-lg',
            activeTab === 'ai' 
              ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' 
              : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'
          ]"
        >
          <Bot class="w-6 h-6 mb-1" />
          <span class="text-xs font-medium tracking-wide">AIエージェント</span>
        </button>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { Home, Bot } from 'lucide-vue-next'

// Props
const props = defineProps({
  modelValue: {
    type: String,
    default: 'top'
  },
  defaultOpen: {
    type: Boolean,
    default: true
  },
  disableScrollDetection: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['update:modelValue'])

// Reactive variables
const activeTab = ref(props.modelValue)
const isVisible = ref(true)
const lastScrollY = ref(0)
const scrollThreshold = 80 // スクロール検知の閾値を大きく

// Navigation functions
const goToTop = () => {
  activeTab.value = 'top'
  emit('update:modelValue', 'top')
  navigateTo('/')
}

const goToAI = () => {
  activeTab.value = 'ai'
  emit('update:modelValue', 'ai')
  navigateTo('/ai-agent')
}

// Watch props changes
watch(() => props.modelValue, (newValue) => {
  activeTab.value = newValue
})

// Scroll detection function
const handleScroll = () => {
  const currentScrollY = window.scrollY
  const scrollDelta = currentScrollY - lastScrollY.value

  // ほぼ最下部ならフッターを常に表示
  if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 10) {
    isVisible.value = true
    lastScrollY.value = currentScrollY
    return
  }

  // スクロール閾値を大きく
  if (Math.abs(scrollDelta) > scrollThreshold) {
    if (scrollDelta < 0) {
      // 上にスクロール：フッターを非表示
      isVisible.value = false
    } else {
      // 下にスクロール：フッターを表示
      isVisible.value = true
    }
    lastScrollY.value = currentScrollY
  }
}

// Initialize scroll detection
onMounted(() => {
  if (!props.disableScrollDetection) {
    lastScrollY.value = window.scrollY
    window.addEventListener('scroll', handleScroll, { passive: true })
  }
})

// Cleanup scroll listener
onUnmounted(() => {
  if (!props.disableScrollDetection) {
    window.removeEventListener('scroll', handleScroll)
  }
})
</script>
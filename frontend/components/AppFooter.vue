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
import { ref, watch, onMounted, onUnmounted, isRef } from 'vue'
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
  },
  scrollTarget: {
    type: [Object, Function], // ref or HTMLElement
    default: null
  }
})

// Emits
const emit = defineEmits(['update:modelValue', 'visible'])

// Reactive variables
const activeTab = ref(props.modelValue)
const isVisible = ref(true)
const lastScrollY = ref(0)
const scrollThreshold = 20 // スクロール検知の閾値を小さく
let scrollTargetEl = null

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

function bindScrollListener(target) {
  if (target) {
    target.addEventListener('scroll', handleScroll, { passive: true })
    scrollTargetEl = target
  }
}
function unbindScrollListener() {
  if (scrollTargetEl) {
    scrollTargetEl.removeEventListener('scroll', handleScroll)
    scrollTargetEl = null
  }
}

onMounted(() => {
  if (!props.disableScrollDetection) {
    if (isRef(props.scrollTarget)) {
      watch(props.scrollTarget, (el) => {
        unbindScrollListener()
        if (el) {
          lastScrollY.value = el.scrollTop ?? 0
          bindScrollListener(el)
        }
      }, { immediate: false, deep: true })
    } else if (props.scrollTarget) {
      lastScrollY.value = props.scrollTarget.scrollTop ?? 0
      bindScrollListener(props.scrollTarget)
    } else {
      lastScrollY.value = window.scrollY
      bindScrollListener(window)
    }
  }
})

onUnmounted(() => {
  unbindScrollListener()
})

const handleScroll = () => {
  let target = null
  if (isRef(props.scrollTarget)) {
    target = props.scrollTarget.value
  } else if (props.scrollTarget) {
    target = props.scrollTarget
  } else {
    target = window
  }
  
  const currentScrollY = target === window ? window.scrollY : target.scrollTop
  const scrollDelta = currentScrollY - lastScrollY.value

  let isNearBottom = false
  if (target === window) {
    isNearBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 10
  } else {
    const element = target
    if (element && typeof element.scrollTop === 'number' && typeof element.clientHeight === 'number' && typeof element.scrollHeight === 'number') {
      isNearBottom = element.scrollTop + element.clientHeight >= element.scrollHeight - 10
    }
  }

  if (isNearBottom) {
    isVisible.value = true
    lastScrollY.value = currentScrollY
    return
  }

  if (Math.abs(scrollDelta) > scrollThreshold) {
    if (scrollDelta > 0) {
      isVisible.value = false
    } else {
      isVisible.value = true
    }
    lastScrollY.value = currentScrollY
  }
}

watch(isVisible, (val) => {
  emit('visible', val)
})

function showFooter() {
  isVisible.value = true
  if (isRef(props.scrollTarget)) {
    const target = props.scrollTarget.value
    if (target) {
      lastScrollY.value = target.scrollTop ?? 0
    }
  } else if (props.scrollTarget) {
    lastScrollY.value = props.scrollTarget.scrollTop ?? 0
  } else {
    lastScrollY.value = window.scrollY
  }
}

function bindScrollToTarget(target) {
  unbindScrollListener()
  if (target) {
    lastScrollY.value = target.scrollTop ?? 0
    bindScrollListener(target)
  }
}

defineExpose({ showFooter, bindScrollToTarget })
</script>
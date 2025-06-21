<template>
  <footer class="fixed bottom-0 left-0 right-0 h-20 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 transition-colors duration-300 z-40">
    <div class="flex items-center justify-around h-full">
      <button 
        @click="activeTab = 'top'"
        :class="[
          'flex flex-col items-center justify-center flex-1 h-full transition-colors',
          activeTab === 'top' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'
        ]"
      >
        <Home class="w-6 h-6 mb-1" />
        <span class="text-xs font-thin tracking-wide">Top</span>
      </button>
      
      <button 
        @click="activeTab = 'ai'"
        :class="[
          'flex flex-col items-center justify-center flex-1 h-full transition-colors',
          activeTab === 'ai' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'
        ]"
      >
        <Bot class="w-6 h-6 mb-1" />
        <span class="text-xs font-thin tracking-wide">AIエージェント</span>
      </button>
    </div>
  </footer>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Home, Bot } from 'lucide-vue-next'

// Props
const props = defineProps({
  modelValue: {
    type: String,
    default: 'top'
  }
})

// Emits
const emit = defineEmits(['update:modelValue'])

// Reactive variables
const activeTab = ref(props.modelValue)

// Handle tab switching
watch(activeTab, (newTab) => {
  emit('update:modelValue', newTab)
  
  if (newTab === 'top') {
    navigateTo('/')
  } else if (newTab === 'ai') {
    navigateTo('/ai-agent')
  }
})

// Watch props changes
watch(() => props.modelValue, (newValue) => {
  activeTab.value = newValue
})
</script>
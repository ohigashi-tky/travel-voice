<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      @blur="handleBlur"
      class="w-full px-3 py-1.5 text-sm text-left border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-gray-300 dark:focus:border-gray-600 flex items-center justify-between"
    >
      <span class="truncate">{{ displayValue }}</span>
      <svg 
        class="w-5 h-5 text-gray-400 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>
    
    <!-- Dropdown -->
    <div
      v-if="isOpen"
      class="absolute z-[60] w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto"
    >
      <div
        v-for="option in options"
        :key="option.value"
        @mousedown="selectOption(option)"
        class="px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-gray-900 dark:text-white transition-colors"
        :class="{ 'bg-blue-50 dark:bg-blue-900/30': option.value === modelValue }"
      >
        {{ option.label }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    required: true
  },
  placeholder: {
    type: String,
    default: '選択してください'
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)

const displayValue = computed(() => {
  if (!props.modelValue) return props.placeholder
  const option = props.options.find(opt => opt.value === props.modelValue)
  return option ? option.label : props.placeholder
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const selectOption = (option) => {
  emit('update:modelValue', option.value)
  isOpen.value = false
}

const handleBlur = () => {
  // Delay to allow mousedown on options to trigger first
  setTimeout(() => {
    isOpen.value = false
  }, 150)
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Ensure proper z-index layering */
.relative {
  position: relative;
  z-index: 1;
}

/* Override any conflicting z-index from parent components */
.relative:has(.absolute.z-\[60\]) {
  z-index: 60;
}
</style>
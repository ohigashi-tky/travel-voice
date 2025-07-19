<template>
  <div>
    <!-- Footer Links -->
    <div class="text-center mb-4 relative z-10">
      <div class="flex justify-center items-center gap-4">
        <button
          @click="scrollToTop"
          class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
        >
          トップに戻る
        </button>
        <span class="text-xs text-gray-400">|</span>
        <nuxt-link
          to="/terms"
          class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
        >
          利用規約
        </nuxt-link>
      </div>
    </div>
    
    <!-- Copyright -->
    <div class="text-center mb-24 relative z-10">
      <p class="text-xs text-gray-500 dark:text-gray-400">
        © 2025 takuya ohigashi. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script setup>
// トップにスクロールする関数
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}
</script>
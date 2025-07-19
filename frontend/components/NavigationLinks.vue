<template>
  <!-- Navigation Links Section -->
  <div class="mb-3 overflow-x-auto scrollbar-hide">
    <ul class="list-none whitespace-nowrap">
      <li class="inline text-gray-400 dark:text-gray-500 mx-2">|</li>
      <li class="inline">
        <button
          @click="navigateToEvents"
          class="text-gray-800 dark:text-white text-sm"
        >
          イベント情報
        </button>
      </li>
      <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
      <li class="inline">
        <span class="text-gray-400 dark:text-gray-500 cursor-default text-sm">特集</span>
      </li>
      <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
      <li class="inline">
        <span class="text-gray-400 dark:text-gray-500 cursor-default text-sm">穴場スポット</span>
      </li>
      <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
      <li class="inline">
        <span class="text-gray-400 dark:text-gray-500 cursor-default text-sm">天気</span>
      </li>
      <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
      <li class="inline">
        <span class="text-gray-400 dark:text-gray-500 cursor-default text-sm">クイズ</span>
      </li>
      <li class="inline text-gray-400 dark:text-gray-500 mx-2 text-sm">|</li>
    </ul>
  </div>
</template>

<script setup>
const navigateToEvents = () => {
  navigateTo('/events')
}
</script>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
<template>
  <div class="min-h-screen">
    <NuxtPage />
  </div>
</template>

<script setup>
useHead({
  titleTemplate: '%s - 観光ガイドアプリ',
  meta: [
    { name: 'description', content: '日本の観光地を巡る音声ガイドアプリ' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1' }
  ]
})
</script>
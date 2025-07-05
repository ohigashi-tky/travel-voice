<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup>
useHead({
  titleTemplate: '%s - 観光ガイドアプリ',
  meta: [
    { name: 'description', content: '日本の観光地を巡る音声ガイドアプリ' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1, viewport-fit=cover' },
    { name: 'apple-mobile-web-app-capable', content: 'yes' },
    { name: 'apple-mobile-web-app-status-bar-style', content: 'black-translucent' },
    { name: 'mobile-web-app-capable', content: 'yes' }
  ]
})
</script>
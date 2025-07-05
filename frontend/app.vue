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
    { name: 'viewport', content: 'width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no' },
    { name: 'apple-mobile-web-app-capable', content: 'yes' },
    { name: 'apple-mobile-web-app-status-bar-style', content: 'black-translucent' },
    { name: 'apple-mobile-web-app-title', content: 'おうち旅行' },
    { name: 'mobile-web-app-capable', content: 'yes' },
    { name: 'application-name', content: 'おうち旅行' },
    { name: 'msapplication-TileColor', content: '#2563eb' },
    { name: 'theme-color', content: '#2563eb' },
    // PWA表示を強制
    { name: 'apple-touch-fullscreen', content: 'yes' },
    { name: 'format-detection', content: 'telephone=no' }
  ]
})
</script>
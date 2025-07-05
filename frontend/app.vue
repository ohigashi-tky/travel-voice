<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup>
useHead({
  titleTemplate: '%s - 観光ガイドアプリ',
  meta: [
    { name: 'theme-color', content: '#fff', media: '(prefers-color-scheme: light)' },
    { name: 'theme-color', content: '#111827', media: '(prefers-color-scheme: dark)' },
    { name: 'description', content: '日本の観光地を巡る音声ガイドアプリ' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no' },
    { name: 'apple-mobile-web-app-capable', content: 'yes' },
    { name: 'apple-mobile-web-app-status-bar-style', content: 'default' },
    { name: 'apple-mobile-web-app-title', content: 'おうち旅行' },
    { name: 'mobile-web-app-capable', content: 'yes' },
    { name: 'application-name', content: 'おうち旅行' },
    { name: 'msapplication-TileColor', content: '#2563eb' },
    // PWA表示を強制
    { name: 'apple-touch-fullscreen', content: 'yes' },
    { name: 'format-detection', content: 'telephone=no' }
  ]
})
</script>
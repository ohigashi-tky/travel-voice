<template>
  <div v-if="showInstallPrompt" class="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6">
    <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
      <!-- Header -->
      <div class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 p-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg">アプリをインストール</h3>
              <p class="text-white/80 text-sm">ホーム画面に追加</p>
            </div>
          </div>
          <button
            @click="closePrompt"
            class="text-white/80 hover:text-white text-xl leading-none"
          >
            ×
          </button>
        </div>
      </div>
      
      <!-- Content -->
      <div class="p-4">
        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
          「おうち旅行」をホーム画面に追加して、アプリのようにご利用いただけます。
        </p>
        
        <!-- iOS Safari instructions -->
        <div v-if="isIOSSafari" class="mb-4">
          <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
            <h4 class="font-medium text-blue-800 dark:text-blue-300 mb-2 flex items-center">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
              インストール手順
            </h4>
            <ol class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
              <li>1. 画面下部の <span class="inline-flex items-center px-1 bg-blue-100 dark:bg-blue-800 rounded">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M16 12l-4-4-4 4v1h8v-1z"/>
                  <path d="M20 9H4v2h16V9z"/>
                </svg>
              </span> をタップ</li>
              <li>2. 「ホーム画面に追加」をタップ</li>
              <li>3. 「追加」をタップして完了</li>
            </ol>
          </div>
        </div>
        
        <!-- PWA install button for supported browsers -->
        <div v-else-if="canInstall" class="flex space-x-3">
          <button
            @click="installPWA"
            class="flex-1 bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 text-white py-3 px-4 rounded-xl font-medium hover:shadow-lg transition-all duration-200"
          >
            インストール
          </button>
          <button
            @click="closePrompt"
            class="px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            後で
          </button>
        </div>
        
        <!-- Manual installation for other browsers -->
        <div v-else class="flex space-x-3">
          <button
            @click="showInstructions = !showInstructions"
            class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-xl font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
          >
            インストール方法を見る
          </button>
          <button
            @click="closePrompt"
            class="px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            閉じる
          </button>
        </div>
        
        <!-- Manual instructions -->
        <div v-if="showInstructions" class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">ブラウザメニューから：</h4>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            「ホーム画面に追加」または「アプリをインストール」を選択してください。
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

// Reactive variables
const showInstallPrompt = ref(false)
const showInstructions = ref(false)
const canInstall = ref(false)
const isIOSSafari = ref(false)
let deferredPrompt = null

// Check if device/browser supports PWA installation
const checkInstallability = () => {
  // Check for iOS Safari
  const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent)
  const isSafari = /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent)
  isIOSSafari.value = isIOS && isSafari
  
  // Check if already installed
  const isStandalone = window.matchMedia('(display-mode: standalone)').matches
  const isInstalled = window.navigator.standalone === true // iOS Safari
  
  return !isStandalone && !isInstalled
}

// Handle beforeinstallprompt event
const handleBeforeInstallPrompt = (e) => {
  e.preventDefault()
  deferredPrompt = e
  canInstall.value = true
  
  // Show prompt if conditions are met
  if (checkInstallability()) {
    // Wait a bit before showing to avoid immediate popup
    setTimeout(() => {
      showInstallPrompt.value = true
    }, 3000)
  }
}

// Install PWA
const installPWA = async () => {
  if (!deferredPrompt) return
  
  try {
    deferredPrompt.prompt()
    const result = await deferredPrompt.userChoice
    
    if (result.outcome === 'accepted') {
      // PWA installation accepted
    } else {
      // PWA installation declined
    }
  } catch (error) {
    console.error('Error during PWA installation:', error)
  }
  
  deferredPrompt = null
  canInstall.value = false
  showInstallPrompt.value = false
}

// Close prompt
const closePrompt = () => {
  showInstallPrompt.value = false
  // Remember user dismissed the prompt
  localStorage.setItem('pwa-install-dismissed', Date.now().toString())
}

// Check if should show prompt
const shouldShowPrompt = () => {
  const dismissed = localStorage.getItem('pwa-install-dismissed')
  if (dismissed) {
    const dismissedTime = parseInt(dismissed)
    const daysSinceDismissed = (Date.now() - dismissedTime) / (1000 * 60 * 60 * 24)
    // Show again after 7 days
    return daysSinceDismissed > 7
  }
  return true
}

// Lifecycle
onMounted(() => {
  // Only show if user hasn't dismissed recently
  if (!shouldShowPrompt()) return
  
  // Listen for beforeinstallprompt event
  window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
  
  // For iOS Safari, show prompt manually
  if (isIOSSafari.value && checkInstallability()) {
    setTimeout(() => {
      showInstallPrompt.value = true
    }, 5000)
  }
})

onUnmounted(() => {
  window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
})
</script>
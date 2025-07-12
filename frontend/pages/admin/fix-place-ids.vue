<template>
  <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-xl font-bold text-gray-900 mb-4">Place ID 修正</h1>
        <p class="text-gray-600 mb-6">
          東京スカイツリーと浅草寺の画像表示のためにPlace IDを更新します。
        </p>
        
        <button 
          @click="updatePlaceIds"
          :disabled="isLoading"
          class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-2 px-4 rounded-md transition-colors"
        >
          {{ isLoading ? '更新中...' : 'Place ID を更新' }}
        </button>
        
        <div v-if="message" :class="[
          'mt-4 p-3 rounded-md text-sm',
          success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
        ]">
          {{ message }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const isLoading = ref(false)
const message = ref('')
const success = ref(false)
const config = useRuntimeConfig()

const updatePlaceIds = async () => {
  isLoading.value = true
  message.value = ''
  
  try {
    const response = await $fetch(`${config.public.apiBase}/api/travel-spots/update-place-ids`, {
      method: 'POST'
    })
    
    if (response.success) {
      success.value = true
      message.value = 'Place IDが正常に更新されました。画像が表示されるようになります。'
    } else {
      success.value = false
      message.value = response.message || '更新に失敗しました'
    }
  } catch (error) {
    success.value = false
    message.value = 'エラーが発生しました: ' + error.message
  } finally {
    isLoading.value = false
  }
}

useHead({
  title: 'Place ID 修正 - Travel Voice'
})
</script>
<template>
  <div class="flex items-center justify-between">
    <span class="text-gray-700 dark:text-gray-300 font-medium tracking-wide transition-colors duration-300">音声設定</span>
    
    <div class="flex items-center gap-4">
      <div class="flex items-center cursor-pointer" @click="selectVoice('Takumi')">
        <input
          id="voice-male"
          v-model="selectedVoice"
          value="Takumi"
          type="radio"
          name="voice"
          class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-0 focus:ring-transparent dark:bg-gray-700 dark:border-gray-600"
          readonly
        />
        <label for="voice-male" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
          男性
        </label>
      </div>
      
      <div class="flex items-center cursor-pointer" @click="selectVoice('Tomoko')">
        <input
          id="voice-female"
          v-model="selectedVoice"
          value="Tomoko"
          type="radio"
          name="voice"
          class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-0 focus:ring-transparent dark:bg-gray-700 dark:border-gray-600"
          readonly
        />
        <label for="voice-female" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
          女性
        </label>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const selectedVoice = ref('Takumi')

const selectVoice = (voice) => {
  selectedVoice.value = voice
  saveVoiceSetting()
}

const saveVoiceSetting = () => {
  if (process.client) {
    localStorage.setItem('audioGuideVoice', selectedVoice.value)
  }
}

const loadVoiceSetting = () => {
  if (process.client) {
    const saved = localStorage.getItem('audioGuideVoice')
    if (saved) {
      selectedVoice.value = saved
    }
  }
}

onMounted(() => {
  loadVoiceSetting()
})
</script>
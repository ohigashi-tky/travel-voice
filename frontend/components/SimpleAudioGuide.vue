<template>
  <!-- 音声ガイドセクション -->
  <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
          <Icon name="lucide:headphones" class="w-5 h-5 text-white" />
        </div>
        <div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-white">音声ガイド</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">{{ spotName }}の詳しい解説を聞く</p>
        </div>
      </div>
      
      <!-- 再生ボタン -->
      <button
        v-if="!showPlayer"
        @click="generateAndPlay"
        :disabled="isLoading"
        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-2.5 rounded-full font-medium transition-all duration-200 flex items-center gap-2"
      >
        <Icon 
          v-if="isLoading" 
          name="lucide:loader-2" 
          class="w-4 h-4 animate-spin" 
        />
        <Icon 
          v-else 
          name="lucide:play" 
          class="w-4 h-4" 
        />
        {{ isLoading ? '生成中...' : '再生' }}
      </button>
    </div>
    
    <!-- エラー表示 -->
    <div v-if="error" class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
      <p class="text-red-600 dark:text-red-400 text-sm flex items-center gap-2">
        <Icon name="lucide:alert-circle" class="w-4 h-4" />
        {{ error }}
      </p>
    </div>

    <!-- YouTube Music スタイルプレイヤー -->
    <div v-if="showPlayer" class="mt-4 bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm">
      <!-- プレイヤーヘッダー -->
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
            <Icon name="lucide:music" class="w-6 h-6 text-white" />
          </div>
          <div>
            <h5 class="font-medium text-gray-900 dark:text-white">{{ spotName }}</h5>
            <p class="text-sm text-gray-500 dark:text-gray-400">音声ガイド</p>
          </div>
        </div>
        <button
          @click="closePlayer"
          class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
        >
          <Icon name="lucide:x" class="w-5 h-5" />
        </button>
      </div>

      <!-- プログレスバー -->
      <div class="mb-4">
        <div class="relative mb-2">
          <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
            <div 
              class="bg-blue-600 h-1.5 rounded-full transition-all duration-200"
              :style="{ width: `${progress}%` }"
            ></div>
          </div>
          <input
            type="range"
            min="0"
            :max="duration"
            :value="currentTime"
            @input="seekTo($event.target.value)"
            class="absolute top-0 w-full h-1.5 opacity-0 cursor-pointer"
          />
        </div>
        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
          <span>{{ formattedCurrentTime }}</span>
          <span>{{ formattedDuration }}</span>
        </div>
      </div>

      <!-- コントロール -->
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <!-- 再生/停止ボタン -->
          <button
            @click="togglePlayPause"
            class="p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-colors"
          >
            <Icon 
              :name="isPlaying ? 'lucide:pause' : 'lucide:play'" 
              class="w-5 h-5" 
            />
          </button>
          
          <!-- 停止ボタン -->
          <button
            @click="stopAudio"
            class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100"
          >
            <Icon name="lucide:square" class="w-4 h-4" />
          </button>
        </div>

        <!-- 音量コントロール（PC用） -->
        <div class="hidden md:flex items-center space-x-2 flex-1 max-w-32 ml-6">
          <Icon name="lucide:volume-2" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
          <div class="relative flex-1">
            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1">
              <div 
                class="bg-blue-600 h-1 rounded-full transition-all duration-200"
                :style="{ width: `${currentVolume * 100}%` }"
              ></div>
            </div>
            <input
              type="range"
              min="0"
              max="1"
              step="0.1"
              :value="currentVolume"
              @input="setVolume($event.target.value)"
              class="absolute top-0 w-full h-1 opacity-0 cursor-pointer"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAudioGuide } from '~/composables/useAudioGuide'

interface Props {
  spotId: number
  spotName: string
}

const props = defineProps<Props>()

const {
  isLoading,
  isPlaying,
  currentVolume,
  currentTime,
  duration,
  error,
  progress,
  formattedCurrentTime,
  formattedDuration,
  generateTouristSpotAudio,
  playAudio,
  stopAudio,
  togglePlayPause,
  setVolume,
  seekTo,
  cleanup
} = useAudioGuide()

const showPlayer = ref(false)

// ローカル設定を取得
const getSelectedVoice = () => {
  if (process.client) {
    return localStorage.getItem('audioGuideVoice') || 'Takumi'
  }
  return 'Takumi'
}

const generateAndPlay = async () => {
  try {
    const voiceId = getSelectedVoice()
    
    const response = await generateTouristSpotAudio(
      props.spotId,
      voiceId,
      'ja'
    )

    if (response?.success && response.data) {
      const audioUrl = response.data.audio_url.startsWith('http') 
        ? response.data.audio_url 
        : `http://localhost:8000${response.data.audio_url}`
      
      showPlayer.value = true
      await playAudio(audioUrl)
    }
  } catch (err) {
    console.error('Audio generation failed:', err)
  }
}

const closePlayer = () => {
  stopAudio()
  showPlayer.value = false
  cleanup()
}
</script>

<style scoped>
/* カスタムスライダースタイル */
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #2563eb;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

input[type="range"]::-moz-range-thumb {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #2563eb;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
</style>
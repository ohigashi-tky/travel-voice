<template>
  <div class="flex items-center gap-3">
    <!-- 音声ガイド再生ボタン -->
    <button
      @click="handlePlay"
      :disabled="isLoading"
      class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
    >
      <Icon 
        v-if="isLoading" 
        name="lucide:loader-2" 
        class="w-4 h-4 animate-spin" 
      />
      <Icon 
        v-else 
        name="lucide:headphones" 
        class="w-4 h-4" 
      />
      {{ isLoading ? '生成中...' : '音声ガイド' }}
    </button>

    <!-- エラー表示 -->
    <div v-if="error" class="text-red-600 text-sm">
      {{ error }}
    </div>
  </div>

  <!-- 音声プレイヤーモーダル -->
  <div
    v-if="showPlayer"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click="closePlayer"
  >
    <div
      class="bg-white rounded-lg p-6 max-w-md w-full mx-4"
      @click.stop
    >
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">音声ガイド</h3>
        <button
          @click="closePlayer"
          class="text-gray-400 hover:text-gray-600"
        >
          <Icon name="lucide:x" class="w-5 h-5" />
        </button>
      </div>

      <!-- プレイヤーコントロール -->
      <div class="space-y-4">
        <!-- 再生コントロール -->
        <div class="flex items-center justify-center space-x-4">
          <button
            @click="togglePlayPause"
            class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full transition-colors duration-200"
          >
            <Icon 
              :name="isPlaying ? 'lucide:pause' : 'lucide:play'" 
              class="w-6 h-6" 
            />
          </button>
          
          <button
            @click="stopAudio"
            class="bg-gray-600 hover:bg-gray-700 text-white p-3 rounded-full transition-colors duration-200"
          >
            <Icon name="lucide:square" class="w-6 h-6" />
          </button>
        </div>

        <!-- プログレスバー -->
        <div class="space-y-2">
          <div class="flex justify-between text-sm text-gray-600">
            <span>{{ formattedCurrentTime }}</span>
            <span>{{ formattedDuration }}</span>
          </div>
          
          <div class="relative">
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div 
                class="bg-blue-600 h-2 rounded-full transition-all duration-200"
                :style="{ width: `${progress}%` }"
              ></div>
            </div>
            <input
              type="range"
              min="0"
              :max="duration"
              :value="currentTime"
              @input="seekTo($event.target.value)"
              class="absolute top-0 w-full h-2 opacity-0 cursor-pointer"
            />
          </div>
        </div>

        <!-- 音量コントロール -->
        <div class="flex items-center space-x-3">
          <Icon name="lucide:volume-2" class="w-4 h-4 text-gray-600" />
          <input
            type="range"
            min="0"
            max="1"
            step="0.1"
            :value="currentVolume"
            @input="setVolume($event.target.value)"
            class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
          />
          <span class="text-sm text-gray-600 w-8">{{ Math.round(currentVolume * 100) }}%</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAudioGuide } from '~/composables/useAudioGuide'

interface Props {
  text: string
  spotId?: number
  language?: string
}

const props = withDefaults(defineProps<Props>(), {
  language: 'ja'
})

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

// ローカル設定を取得
const getSelectedVoice = () => {
  if (process.client) {
    return localStorage.getItem('audioGuideVoice') || 'Takumi'
  }
  return 'Takumi'
}

const showPlayer = ref(false)

const handlePlay = async () => {
  try {
    const voiceId = getSelectedVoice()
    
    let response
    if (props.spotId) {
      response = await generateTouristSpotAudio(
        props.spotId,
        voiceId,
        props.language
      )
    }

    if (response?.success && response.data) {
      showPlayer.value = true
      // 音声URLを完全なURLに変換
      const audioUrl = response.data.audio_url.startsWith('http') 
        ? response.data.audio_url 
        : `http://localhost:8000${response.data.audio_url}`
      
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
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #2563eb;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

input[type="range"]::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #2563eb;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
</style>
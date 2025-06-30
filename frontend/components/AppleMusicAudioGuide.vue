<template>
  <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
    <!-- ヘッダー -->
    <div class="flex items-center gap-4 mb-6">
      <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
        <Icon name="lucide:headphones" class="w-8 h-8 text-white" />
      </div>
      <div class="flex-1">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">音声ガイド</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ spotName }}の詳しい解説を聞く</p>
      </div>
      <button
        v-if="!isPlaying && !audioUrl"
        @click="generateAudio"
        :disabled="isLoading"
        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-3 rounded-full font-medium transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105"
      >
        <Icon 
          v-if="isLoading" 
          name="lucide:loader-2" 
          class="w-5 h-5 animate-spin" 
        />
        <Icon 
          v-else 
          name="lucide:play" 
          class="w-5 h-5" 
        />
        {{ isLoading ? '生成中...' : '再生' }}
      </button>
    </div>

    <!-- エラー表示 -->
    <div v-if="error" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
      <p class="text-red-600 dark:text-red-400 text-sm flex items-center gap-2">
        <Icon name="lucide:alert-circle" class="w-4 h-4" />
        {{ error }}
      </p>
    </div>

    <!-- Apple Music スタイルプレイヤー -->
    <div v-if="audioUrl" class="space-y-6">
      <!-- アルバムアートエリア（音声波形スタイル） -->
      <div class="relative bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-2xl p-8 text-center shadow-inner">
        <div class="absolute inset-0 bg-black bg-opacity-20 rounded-2xl"></div>
        <div class="relative z-10">
          <div class="flex justify-center items-center space-x-1 mb-4">
            <div v-for="i in 20" :key="i" 
                 class="w-1 bg-white rounded-full opacity-60 transition-all duration-300"
                 :class="isPlaying ? `animate-pulse h-${Math.random() > 0.5 ? '8' : '4'}` : 'h-2'"
                 :style="{ animationDelay: `${i * 50}ms` }">
            </div>
          </div>
          <div class="text-white">
            <h4 class="text-lg font-semibold">{{ spotName }} 音声ガイド</h4>
            <p class="text-white/80 text-sm">{{ duration > 0 ? formattedDuration : '準備中...' }}</p>
          </div>
        </div>
      </div>

      <!-- プログレスバー -->
      <div class="space-y-2">
        <div class="relative group">
          <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
            <div 
              class="bg-gradient-to-r from-blue-500 to-purple-600 h-1.5 rounded-full transition-all duration-200"
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

      <!-- コントロールボタン -->
      <div class="flex items-center justify-center space-x-6">
        <button
          @click="seekTo(Math.max(0, currentTime - 15))"
          class="p-3 rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
        >
          <Icon name="lucide:rotate-ccw" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
        </button>

        <button
          @click="togglePlayPause"
          class="p-4 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
        >
          <Icon 
            :name="isPlaying ? 'lucide:pause' : 'lucide:play'" 
            class="w-8 h-8 text-gray-800 dark:text-white" 
          />
        </button>

        <button
          @click="seekTo(Math.min(duration, currentTime + 15))"
          class="p-3 rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
        >
          <Icon name="lucide:rotate-cw" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
        </button>
      </div>

      <!-- 音量コントロール -->
      <div class="flex items-center space-x-3 px-4">
        <Icon name="lucide:volume-1" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
        <div class="flex-1 relative group">
          <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1">
            <div 
              class="bg-gradient-to-r from-blue-500 to-purple-600 h-1 rounded-full transition-all duration-200"
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
        <Icon name="lucide:volume-2" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
      </div>

      <!-- キャッシュヒット表示 -->
      <div v-if="cacheHit" class="text-center">
        <span class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-3 py-1 rounded-full">
          <Icon name="lucide:zap" class="w-3 h-3" />
          高速読み込み
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAudioGuide } from '~/composables/useAudioGuide'

interface Props {
  spotId: number
  spotName: string
  spotDescription: string
  spotHistory?: string
  highlights?: string[]
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
  togglePlayPause,
  setVolume,
  seekTo,
  cleanup
} = useAudioGuide()

const audioUrl = ref<string>('')
const cacheHit = ref<boolean>(false)

// ローカル設定を取得
const getSelectedVoice = () => {
  if (process.client) {
    return localStorage.getItem('audioGuideVoice') || 'Takumi'
  }
  return 'Takumi'
}

const generateAudio = async () => {
  try {
    const voiceId = getSelectedVoice()
    
    const response = await generateTouristSpotAudio(
      props.spotId,
      voiceId,
      'ja'
    )

    if (response?.success && response.data) {
      audioUrl.value = response.data.audio_url.startsWith('http') 
        ? response.data.audio_url 
        : `http://localhost:8000${response.data.audio_url}`
      
      cacheHit.value = response.data.cache_hit

      // 自動再生
      await playAudio(audioUrl.value)
    }
  } catch (err) {
    console.error('Audio generation failed:', err)
  }
}

onUnmounted(() => {
  cleanup()
})
</script>

<style scoped>
/* カスタムアニメーション */
@keyframes wave {
  0%, 100% { height: 0.5rem; }
  50% { height: 2rem; }
}

.animate-wave {
  animation: wave 1.5s ease-in-out infinite;
}

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
  background: linear-gradient(45deg, #3b82f6, #8b5cf6);
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

input[type="range"]::-moz-range-thumb {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: linear-gradient(45deg, #3b82f6, #8b5cf6);
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
</style>
<template>
  <!-- 音声ガイドセクション -->
  <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 mb-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center">
          <Icon name="lucide:headphones" class="w-6 h-6 text-white" />
        </div>
        <div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-white">音声ガイド</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">{{ spotName }}の詳しい解説を聞く</p>
        </div>
      </div>
      
      <!-- 再生ボタン -->
      <button
        @click="handlePlayButton"
        :disabled="isLoading"
        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-full font-medium transition-all duration-200 flex items-center gap-2 min-w-[80px]"
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
        {{ isLoading ? '生成' : '再生' }}
      </button>
    </div>
    
    <!-- エラー表示 -->
    <div v-if="error" class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
      <p class="text-red-600 dark:text-red-400 text-sm flex items-center gap-2">
        <Icon name="lucide:alert-circle" class="w-4 h-4" />
        {{ error }}
      </p>
    </div>
  </div>

  <!-- フルスクリーン音声プレイヤーモーダル -->
  <div
    v-if="showPlayer"
    class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4"
    @click="closePlayer"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl"
      @click.stop
    >
      <!-- プレイヤーヘッダー -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
            <Icon name="lucide:music" class="w-6 h-6 text-white" />
          </div>
          <div>
            <h5 class="font-semibold text-gray-900 dark:text-white">{{ spotName }}</h5>
            <p class="text-sm text-gray-500 dark:text-gray-400">音声ガイド</p>
          </div>
        </div>
        <button
          @click="closePlayer"
          class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          <Icon name="lucide:x" class="w-5 h-5" />
        </button>
      </div>

      <!-- プログレスバー -->
      <div class="mb-6">
        <div class="relative mb-2">
          <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
            <div 
              class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-200"
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
        <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
          <span>{{ formattedCurrentTime }}</span>
          <span>{{ formattedDuration }}</span>
        </div>
      </div>

      <!-- コントロールボタン -->
      <div class="flex items-center justify-center mb-6">
        <div class="flex items-center space-x-4">
          <!-- 15秒戻る -->
          <button
            @click="seekBackward"
            class="p-3 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
          >
            <Icon name="lucide:rotate-ccw" class="w-5 h-5" />
          </button>
          
          <!-- 再生/停止 -->
          <button
            @click="togglePlayPause"
            class="p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-colors shadow-lg"
          >
            <Icon 
              :name="isPlaying ? 'lucide:pause' : 'lucide:play'" 
              class="w-6 h-6" 
            />
          </button>
          
          <!-- 15秒進む -->
          <button
            @click="seekForward"
            class="p-3 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
          >
            <Icon name="lucide:rotate-cw" class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- 音量コントロール -->
      <div class="flex items-center space-x-3">
        <Icon name="lucide:volume-1" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
        <div class="flex-1 relative">
          <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
            <div 
              class="bg-gradient-to-r from-blue-500 to-purple-600 h-1.5 rounded-full transition-all duration-200"
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
            class="absolute top-0 w-full h-1.5 opacity-0 cursor-pointer"
          />
        </div>
        <Icon name="lucide:volume-2" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface Props {
  spotId: number
  spotName: string
}

const props = defineProps<Props>()

// State
const isLoading = ref(false)
const isPlaying = ref(false)
const currentAudio = ref<HTMLAudioElement | null>(null)
const currentVolume = ref(0.8)
const currentTime = ref(0)
const duration = ref(0)
const error = ref<string | null>(null)
const showPlayer = ref(false)
const audioUrl = ref<string>('')

// Computed
const progress = computed(() => {
  if (duration.value === 0) return 0
  return (currentTime.value / duration.value) * 100
})

const formatTime = (seconds: number): string => {
  const mins = Math.floor(seconds / 60)
  const secs = Math.floor(seconds % 60)
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const formattedCurrentTime = computed(() => formatTime(currentTime.value))
const formattedDuration = computed(() => formatTime(duration.value))

// Helper functions
const getSelectedVoice = () => {
  if (process.client) {
    return localStorage.getItem('audioGuideVoice') || 'Takumi'
  }
  return 'Takumi'
}

const getCachedAudioText = () => {
  if (process.client) {
    const cached = localStorage.getItem(`audioText_${props.spotId}`)
    if (cached) {
      const data = JSON.parse(cached)
      const oneDay = 24 * 60 * 60 * 1000
      if (Date.now() - data.timestamp < oneDay) {
        return data.text
      }
    }
  }
  return null
}

const setCachedAudioText = (text: string) => {
  if (process.client) {
    localStorage.setItem(`audioText_${props.spotId}`, JSON.stringify({
      text,
      timestamp: Date.now()
    }))
  }
}

// Audio functions
const generateAudioText = async (): Promise<string> => {
  // キャッシュチェック
  const cached = getCachedAudioText()
  if (cached) {
    return cached
  }

  // AIで音声ガイドテキストを生成（実際にはAPIから取得）
  const response = await $fetch(`http://localhost:8000/api/audio-guide/tourist-spot`, {
    method: 'POST',
    body: {
      spot_id: props.spotId,
      voice_id: getSelectedVoice(),
      language: 'ja'
    }
  })

  if (response.success && response.data.guide_text) {
    setCachedAudioText(response.data.guide_text)
    return response.data.guide_text
  }

  throw new Error('音声ガイドテキストの生成に失敗しました')
}

const synthesizeSpeech = async (text: string): Promise<string> => {
  const response = await $fetch(`http://localhost:8000/api/audio-guide/synthesize`, {
    method: 'POST',
    body: {
      text,
      voice_id: getSelectedVoice()
    }
  })

  if (response.success && response.data.audio_url) {
    return response.data.audio_url.startsWith('http') 
      ? response.data.audio_url 
      : `http://localhost:8000${response.data.audio_url}`
  }

  throw new Error('音声合成に失敗しました')
}

const playAudio = async (url: string) => {
  try {
    if (currentAudio.value) {
      currentAudio.value.pause()
      currentAudio.value = null
    }

    const audio = new Audio(url)
    currentAudio.value = audio
    audio.volume = currentVolume.value

    audio.addEventListener('loadedmetadata', () => {
      duration.value = audio.duration
    })

    audio.addEventListener('timeupdate', () => {
      currentTime.value = audio.currentTime
    })

    audio.addEventListener('ended', () => {
      isPlaying.value = false
      currentTime.value = 0
    })

    audio.addEventListener('error', () => {
      error.value = '音声の再生に失敗しました'
      isPlaying.value = false
    })

    await audio.play()
    isPlaying.value = true
    showPlayer.value = true

  } catch (err) {
    error.value = '音声の再生に失敗しました'
    isPlaying.value = false
    throw err
  }
}

// Control functions
const handlePlayButton = async () => {
  if (audioUrl.value) {
    showPlayer.value = true
    await playAudio(audioUrl.value)
    return
  }

  isLoading.value = true
  error.value = null

  try {
    const text = await generateAudioText()
    const url = await synthesizeSpeech(text)
    audioUrl.value = url
    await playAudio(url)
  } catch (err: any) {
    error.value = err.message || '音声ガイドの生成に失敗しました'
  } finally {
    isLoading.value = false
  }
}

const togglePlayPause = () => {
  if (!currentAudio.value) return

  if (isPlaying.value) {
    currentAudio.value.pause()
    isPlaying.value = false
  } else {
    currentAudio.value.play()
    isPlaying.value = true
  }
}

const seekTo = (time: number) => {
  if (currentAudio.value && duration.value > 0) {
    const seekTime = Math.max(0, Math.min(duration.value, time))
    currentAudio.value.currentTime = seekTime
    currentTime.value = seekTime
  }
}

const seekBackward = () => {
  seekTo(currentTime.value - 15)
}

const seekForward = () => {
  seekTo(currentTime.value + 15)
}

const setVolume = (volume: number) => {
  currentVolume.value = Math.max(0, Math.min(1, volume))
  if (currentAudio.value) {
    currentAudio.value.volume = currentVolume.value
  }
}

const closePlayer = () => {
  if (currentAudio.value) {
    currentAudio.value.pause()
    isPlaying.value = false
  }
  showPlayer.value = false
}

onUnmounted(() => {
  if (currentAudio.value) {
    currentAudio.value.pause()
    currentAudio.value = null
  }
})
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
  background: linear-gradient(45deg, #3b82f6, #8b5cf6);
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

input[type="range"]::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: linear-gradient(45deg, #3b82f6, #8b5cf6);
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
</style>
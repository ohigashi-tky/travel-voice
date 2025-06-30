<template>
  <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- ヘッダー -->
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-800 flex items-center">
        <Icon name="lucide:headphones" class="w-5 h-5 mr-2 text-blue-600" />
        音声ガイド
      </h3>
      
      <!-- 音声選択 -->
      <select 
        v-model="selectedVoice" 
        class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
        :disabled="isLoading"
      >
        <option value="">デフォルト音声</option>
        <option v-for="voice in availableVoices" :key="voice.id" :value="voice.id">
          {{ voice.name }} ({{ voice.gender === 'Male' ? '男性' : '女性' }})
        </option>
      </select>
    </div>

    <!-- エラー表示 -->
    <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
      <p class="text-sm text-red-600 flex items-center">
        <Icon name="lucide:alert-circle" class="w-4 h-4 mr-2" />
        {{ error }}
      </p>
    </div>

    <!-- 音声生成ボタン -->
    <div v-if="!audioUrl" class="text-center">
      <button
        @click="generateAudio"
        :disabled="isLoading || !text"
        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center mx-auto"
      >
        <Icon 
          v-if="isLoading" 
          name="lucide:loader-2" 
          class="w-5 h-5 mr-2 animate-spin" 
        />
        <Icon 
          v-else 
          name="lucide:play-circle" 
          class="w-5 h-5 mr-2" 
        />
        {{ isLoading ? '生成中...' : '音声ガイドを生成' }}
      </button>
    </div>

    <!-- 音声プレイヤー -->
    <div v-if="audioUrl" class="space-y-4">
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

      <!-- 再生成ボタン -->
      <div class="text-center pt-2">
        <button
          @click="regenerateAudio"
          :disabled="isLoading"
          class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center justify-center mx-auto"
        >
          <Icon name="lucide:refresh-cw" class="w-4 h-4 mr-1" />
          再生成
        </button>
      </div>
    </div>

    <!-- キャッシュヒット表示 -->
    <div v-if="cacheHit" class="mt-3 text-xs text-green-600 text-center flex items-center justify-center">
      <Icon name="lucide:zap" class="w-3 h-3 mr-1" />
      キャッシュから高速読み込み
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useAudioGuide } from '~/composables/useAudioGuide'

interface Props {
  text: string
  spotId?: number
  language?: string
  autoGenerate?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  language: 'ja',
  autoGenerate: false
})

const {
  isLoading,
  isPlaying,
  currentVolume,
  currentTime,
  duration,
  error,
  availableVoices,
  progress,
  formattedCurrentTime,
  formattedDuration,
  synthesizeSpeech,
  generateTouristSpotAudio,
  fetchAvailableVoices,
  playAudio,
  stopAudio,
  togglePlayPause,
  setVolume,
  seekTo,
  cleanup
} = useAudioGuide()

const selectedVoice = ref<string>('')
const audioUrl = ref<string>('')
const cacheHit = ref<boolean>(false)

// 音声生成
const generateAudio = async () => {
  try {
    let response
    
    if (props.spotId) {
      response = await generateTouristSpotAudio(
        props.spotId,
        selectedVoice.value || undefined,
        props.language
      )
    } else {
      response = await synthesizeSpeech(
        props.text,
        selectedVoice.value || undefined
      )
    }

    if (response.success && response.data) {
      audioUrl.value = response.data.audio_url
      cacheHit.value = response.data.cache_hit
      
      // 自動再生
      await playAudio(audioUrl.value)
    }
  } catch (err) {
    console.error('Audio generation failed:', err)
  }
}

// 再生成
const regenerateAudio = async () => {
  audioUrl.value = ''
  cacheHit.value = false
  stopAudio()
  await generateAudio()
}

// 音声選択が変更された場合の再生成
watch(selectedVoice, async (newVoice, oldVoice) => {
  if (oldVoice !== undefined && audioUrl.value) {
    await regenerateAudio()
  }
})

onMounted(async () => {
  // 利用可能な音声を取得
  await fetchAvailableVoices()
  
  // 自動生成が有効な場合
  if (props.autoGenerate && props.text) {
    await generateAudio()
  }
})

onUnmounted(() => {
  cleanup()
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
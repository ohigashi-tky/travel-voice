<template>
  <!-- 音声ガイドセクション -->
  <div class="mb-3">
    
    <!-- 音声プレイヤー -->
    <div class="rounded-2xl">
      <div v-if="isLoading" class="flex items-center justify-center py-4">
        <span class="animate-spin inline-block w-6 h-6 mr-2">⏳</span>
        <span class="text-gray-600 dark:text-gray-300">音声を生成中...</span>
      </div>
      
      <audio
        ref="audioElement"
        v-else-if="audioUrl"
        controls
        controlslist="nodownload"
        class="w-full rounded-lg custom-audio"
        style="height: 54px;"
        @loadedmetadata="onLoadedMetadata"
        @timeupdate="onTimeUpdate"
        @ended="onEnded"
        @loadstart="setupPlaybackRate"
      >
        <source :src="audioUrl" type="audio/mpeg" />
        お使いのブラウザは音声再生をサポートしていません。
      </audio>
      
      <div v-else class="flex items-center justify-center py-4">
        <span class="text-gray-500 dark:text-gray-400">音声を準備中...</span>
      </div>
      
      <!-- エラー表示 -->
      <div v-if="error" class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const config = useRuntimeConfig()

interface Props {
  spotId: number
  spotName: string
}

const props = defineProps<Props>()

const isLoading = ref(false)
const error = ref<string | null>(null)
const audioUrl = ref<string | null>(null)
const audioElement = ref<HTMLAudioElement | null>(null)

const loadAudioGuide = async () => {
  if (!process.client) return
  
  isLoading.value = true
  error.value = null

  try {
    // Critical fix: Detect ID/name mismatch for 清水寺
    let actualSpotId = props.spotId
    if (props.spotName === '清水寺' && props.spotId !== 201) {
      console.warn('Detected 清水寺 with wrong ID:', props.spotId, 'Correcting to 201')
      actualSpotId = 201
    }
    
    const voiceId = localStorage.getItem('audioGuideVoice') || 'Takumi'
    
    const response = await $fetch(`${config.public.apiBase}/api/audio-guide/tourist-spot`, {
      method: 'POST',
      body: {
        spot_id: actualSpotId,
        voice_id: voiceId,
        language: 'ja',
        spot_name: props.spotName // Send spot name for verification
      }
    })

    if (response.success && response.data?.audio_url) {
      audioUrl.value = response.data.audio_url
    } else {
      throw new Error('音声ガイドの生成に失敗しました')
    }
  } catch (err: any) {
    console.error('Audio guide generation error:', err)
    error.value = err.message || '音声ガイドの生成に失敗しました'
  } finally {
    isLoading.value = false
  }
}

// クライアントサイドでのみ実行
onMounted(() => {
  if (process.client) {
    loadAudioGuide()
  }
})

const setupPlaybackRate = () => {
  if (audioElement.value) {
    // デフォルトの再生速度を1.0に設定し、0.75-1.5の範囲で調整可能にする
    audioElement.value.playbackRate = 1.0
    audioElement.value.defaultPlaybackRate = 1.0
  }
}

const onLoadedMetadata = () => {
  // メタデータ読み込み完了
}

const onTimeUpdate = () => {
  // 時間更新
}

const onEnded = () => {
  // 再生終了
}


onUnmounted(() => {
  if (audioElement.value) {
    audioElement.value.pause()
  }
})
</script>

<style scoped>
.custom-audio {
  background: transparent !important;
}

.custom-audio::-webkit-media-controls-panel {
  background: transparent !important;
}

.custom-audio::-webkit-media-controls-enclosure {
  background: transparent !important;
}
</style>
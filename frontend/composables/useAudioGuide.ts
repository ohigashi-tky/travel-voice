import { ref, computed } from 'vue'

export interface AudioGuideResponse {
  success: boolean
  data?: {
    audio_url: string
    cache_hit: boolean
    text_length: number
    filename?: string
    spot_id?: number
    guide_text?: string
  }
  message: string
  errors?: any
}

export interface Voice {
  id: string
  name: string
  gender: string
  language_code: string
  supported_engines: string[]
}

export const useAudioGuide = () => {
  const config = useRuntimeConfig()
  const isLoading = ref(false)
  const isPlaying = ref(false)
  const currentAudio = ref<HTMLAudioElement | null>(null)
  const currentVolume = ref(0.8)
  const currentTime = ref(0)
  const duration = ref(0)
  const error = ref<string | null>(null)
  const availableVoices = ref<Voice[]>([])

  // 音声合成API呼び出し
  const synthesizeSpeech = async (text: string, voiceId?: string): Promise<AudioGuideResponse> => {
    isLoading.value = true
    error.value = null

    try {
      const apiBaseUrl = process.server ? config.apiBaseServer : config.public.apiBase
      const response = await $fetch<AudioGuideResponse>(`${apiBaseUrl}/api/audio-guide/synthesize`, {
        method: 'POST',
        body: {
          text,
          voice_id: voiceId
        }
      })

      return response
    } catch (err: any) {
      error.value = err.data?.message || '音声合成に失敗しました'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  // 観光地音声ガイド生成
  const generateTouristSpotAudio = async (
    spotId: number, 
    voiceId?: string, 
    language: string = 'ja'
  ): Promise<AudioGuideResponse> => {
    isLoading.value = true
    error.value = null

    try {
      const apiBaseUrl = process.server ? config.apiBaseServer : config.public.apiBase
      const response = await $fetch<AudioGuideResponse>(`${apiBaseUrl}/api/audio-guide/tourist-spot`, {
        method: 'POST',
        body: {
          spot_id: spotId,
          voice_id: voiceId,
          language
        }
      })

      return response
    } catch (err: any) {
      error.value = err.data?.message || '音声ガイドの生成に失敗しました'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  // 利用可能な音声一覧を取得
  const fetchAvailableVoices = async (): Promise<Voice[]> => {
    try {
      const apiBaseUrl = process.server ? config.apiBaseServer : config.public.apiBase
      const response = await $fetch<{ success: boolean; data: Voice[] }>(`${apiBaseUrl}/api/audio-guide/voices`)
      if (response.success) {
        availableVoices.value = response.data
        return response.data
      }
      return []
    } catch (err: any) {
      console.error('Failed to fetch voices:', err)
      return []
    }
  }

  // 音声再生
  const playAudio = async (audioUrl: string): Promise<void> => {
    try {
      // 既存の音声を停止
      if (currentAudio.value) {
        stopAudio()
      }

      const audio = new Audio(audioUrl)
      currentAudio.value = audio

      // 音声の設定
      audio.volume = currentVolume.value
      audio.preload = 'auto'

      // イベントリスナーを設定
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

      audio.addEventListener('error', (e) => {
        error.value = '音声の再生に失敗しました'
        isPlaying.value = false
      })

      // 再生開始
      await audio.play()
      isPlaying.value = true

    } catch (err: any) {
      error.value = '音声の再生に失敗しました'
      isPlaying.value = false
      throw err
    }
  }

  // 音声停止
  const stopAudio = (): void => {
    if (currentAudio.value) {
      currentAudio.value.pause()
      currentAudio.value.currentTime = 0
      currentAudio.value = null
    }
    isPlaying.value = false
    currentTime.value = 0
  }

  // 一時停止/再開
  const togglePlayPause = (): void => {
    if (!currentAudio.value) return

    if (isPlaying.value) {
      currentAudio.value.pause()
      isPlaying.value = false
    } else {
      currentAudio.value.play()
      isPlaying.value = true
    }
  }

  // 音量調整
  const setVolume = (volume: number): void => {
    currentVolume.value = Math.max(0, Math.min(1, volume))
    if (currentAudio.value) {
      currentAudio.value.volume = currentVolume.value
    }
  }

  // シーク
  const seekTo = (time: number): void => {
    if (currentAudio.value && duration.value > 0) {
      const seekTime = Math.max(0, Math.min(duration.value, time))
      currentAudio.value.currentTime = seekTime
      currentTime.value = seekTime
    }
  }

  // 進行度（0-100%）
  const progress = computed(() => {
    if (duration.value === 0) return 0
    return (currentTime.value / duration.value) * 100
  })

  // 時間フォーマット
  const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60)
    const secs = Math.floor(seconds % 60)
    return `${mins}:${secs.toString().padStart(2, '0')}`
  }

  const formattedCurrentTime = computed(() => formatTime(currentTime.value))
  const formattedDuration = computed(() => formatTime(duration.value))

  // クリーンアップ
  const cleanup = (): void => {
    stopAudio()
    error.value = null
  }

  return {
    // State
    isLoading: readonly(isLoading),
    isPlaying: readonly(isPlaying),
    currentVolume: readonly(currentVolume),
    currentTime: readonly(currentTime),
    duration: readonly(duration),
    error: readonly(error),
    availableVoices: readonly(availableVoices),
    progress,
    formattedCurrentTime,
    formattedDuration,

    // Actions
    synthesizeSpeech,
    generateTouristSpotAudio,
    fetchAvailableVoices,
    playAudio,
    stopAudio,
    togglePlayPause,
    setVolume,
    seekTo,
    cleanup
  }
}
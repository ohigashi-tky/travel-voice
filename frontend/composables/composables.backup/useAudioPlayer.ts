export const useAudioPlayer = () => {
  const currentAudio = ref<HTMLAudioElement | null>(null)
  const isPlaying = ref(false)
  const currentTime = ref(0)
  const duration = ref(0)
  const volume = ref(1)
  const playbackRate = ref(1)
  const isLoading = ref(false)
  
  const progress = computed(() => 
    duration.value > 0 ? (currentTime.value / duration.value) * 100 : 0
  )
  
  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60)
    const secs = Math.floor(seconds % 60)
    return `${mins}:${secs.toString().padStart(2, '0')}`
  }
  
  const loadAudio = (audioUrl: string) => {
    return new Promise<void>((resolve, reject) => {
      if (currentAudio.value) {
        currentAudio.value.pause()
        currentAudio.value = null
      }
      
      isLoading.value = true
      const audio = new Audio(audioUrl)
      
      audio.addEventListener('loadedmetadata', () => {
        duration.value = Math.floor(audio.duration)
        currentAudio.value = audio
        isLoading.value = false
        resolve()
      })
      
      audio.addEventListener('timeupdate', () => {
        currentTime.value = Math.floor(audio.currentTime)
      })
      
      audio.addEventListener('ended', () => {
        isPlaying.value = false
        currentTime.value = 0
      })
      
      audio.addEventListener('error', () => {
        isLoading.value = false
        reject(new Error('Audio loading failed'))
      })
      
      audio.addEventListener('play', () => {
        isPlaying.value = true
      })
      
      audio.addEventListener('pause', () => {
        isPlaying.value = false
      })
      
      // 音量と再生速度を設定
      audio.volume = volume.value
      audio.playbackRate = playbackRate.value
    })
  }
  
  const play = async () => {
    if (!currentAudio.value) return
    
    try {
      await currentAudio.value.play()
    } catch (error) {
      console.error('Audio play failed:', error)
    }
  }
  
  const pause = () => {
    if (!currentAudio.value) return
    currentAudio.value.pause()
  }
  
  const toggle = () => {
    if (isPlaying.value) {
      pause()
    } else {
      play()
    }
  }
  
  const seek = (seconds: number) => {
    if (!currentAudio.value) return
    currentAudio.value.currentTime = seconds
    currentTime.value = seconds
  }
  
  const seekToPercent = (percent: number) => {
    if (!currentAudio.value) return
    const newTime = (percent / 100) * duration.value
    seek(newTime)
  }
  
  const setVolume = (newVolume: number) => {
    volume.value = Math.max(0, Math.min(1, newVolume))
    if (currentAudio.value) {
      currentAudio.value.volume = volume.value
    }
  }
  
  const setPlaybackRate = (rate: number) => {
    playbackRate.value = Math.max(0.25, Math.min(2, rate))
    if (currentAudio.value) {
      currentAudio.value.playbackRate = playbackRate.value
    }
  }
  
  const stop = () => {
    if (!currentAudio.value) return
    pause()
    seek(0)
  }
  
  const cleanup = () => {
    if (currentAudio.value) {
      currentAudio.value.pause()
      currentAudio.value = null
    }
    isPlaying.value = false
    currentTime.value = 0
    duration.value = 0
  }
  
  // 自動クリーンアップ
  onBeforeUnmount(() => {
    cleanup()
  })
  
  return {
    // State
    isPlaying: readonly(isPlaying),
    currentTime: readonly(currentTime),
    duration: readonly(duration),
    volume: readonly(volume),
    playbackRate: readonly(playbackRate),
    isLoading: readonly(isLoading),
    progress,
    
    // Actions
    loadAudio,
    play,
    pause,
    toggle,
    seek,
    seekToPercent,
    setVolume,
    setPlaybackRate,
    stop,
    cleanup,
    
    // Utils
    formatTime
  }
}
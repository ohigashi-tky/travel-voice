<template>
  <Transition
    enter-active-class="transition-all duration-500"
    enter-from-class="opacity-0 transform translate-y-full"
    enter-to-class="opacity-100 transform translate-y-0"
    leave-active-class="transition-all duration-300"
    leave-from-class="opacity-100 transform translate-y-0"
    leave-to-class="opacity-0 transform translate-y-full"
  >
    <div v-if="audioGuide && spotName" class="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6">
      <div class="max-w-5xl mx-auto">
        <div class="glass-card rounded-3xl backdrop-blur-xl bg-black/60 border border-white/20 shadow-2xl overflow-hidden">
          
          <!-- Header -->
          <div class="p-6 pb-4">
            <div class="flex items-center gap-4">
              <!-- Album Art -->
              <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-cyan-400 to-purple-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                <Headphones class="w-8 h-8 md:w-10 md:h-10 text-white" />
              </div>
              
              <!-- Track Info -->
              <div class="flex-1 min-w-0">
                <h3 class="text-white font-bold text-lg md:text-xl truncate mb-1">{{ audioGuide.title }}</h3>
                <p class="text-gray-300 text-sm md:text-base truncate">{{ spotName }}</p>
                <div class="flex items-center gap-2 mt-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                  <span class="text-emerald-300 text-xs font-medium">音声ガイド再生中</span>
                </div>
              </div>
              
              <!-- Close Button -->
              <button 
                @click="closePlayer"
                class="w-10 h-10 md:w-12 md:h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors flex-shrink-0"
              >
                <X class="w-5 h-5 md:w-6 md:h-6 text-white" />
              </button>
            </div>
          </div>
          
          <!-- Progress Section -->
          <div class="px-6 pb-4">
            <!-- Progress Bar -->
            <div class="mb-4">
              <div 
                class="w-full bg-white/20 rounded-full h-2 cursor-pointer"
                @click="handleProgressClick"
                ref="progressBarRef"
              >
                <div 
                  class="bg-gradient-to-r from-cyan-400 to-purple-600 h-2 rounded-full transition-all duration-300 relative"
                  :style="{ width: `${progress}%` }"
                >
                  <!-- Progress Thumb -->
                  <div class="absolute right-0 top-1/2 transform translate-x-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full shadow-lg"></div>
                </div>
              </div>
              
              <!-- Time Display -->
              <div class="flex justify-between text-sm text-gray-400 mt-2">
                <span>{{ formatTime(currentTime) }}</span>
                <span>{{ formatTime(duration) }}</span>
              </div>
            </div>
          </div>
          
          <!-- Controls Section -->
          <div class="px-6 pb-6">
            <div class="flex items-center justify-between">
              <!-- Left Controls -->
              <div class="flex items-center gap-3">
                <!-- Speed Control -->
                <div class="relative">
                  <button 
                    @click="showSpeedMenu = !showSpeedMenu"
                    class="px-3 py-1 bg-white/10 rounded-full text-white text-xs font-medium hover:bg-white/20 transition-colors"
                  >
                    {{ playbackRate }}x
                  </button>
                  
                  <!-- Speed Menu -->
                  <Transition
                    enter-active-class="transition-all duration-200"
                    enter-from-class="opacity-0 transform scale-95"
                    enter-to-class="opacity-100 transform scale-100"
                    leave-active-class="transition-all duration-150"
                    leave-from-class="opacity-100 transform scale-100"
                    leave-to-class="opacity-0 transform scale-95"
                  >
                    <div v-if="showSpeedMenu" class="absolute bottom-full mb-2 left-0 glass-card rounded-xl backdrop-blur-xl bg-black/80 border border-white/20 p-2 min-w-20">
                      <button
                        v-for="speed in speedOptions"
                        :key="speed"
                        @click="setPlaybackRate(speed); showSpeedMenu = false"
                        class="block w-full px-3 py-1 text-white text-xs hover:bg-white/20 rounded transition-colors"
                        :class="{ 'bg-white/20': playbackRate === speed }"
                      >
                        {{ speed }}x
                      </button>
                    </div>
                  </Transition>
                </div>
                
                <!-- Volume Control -->
                <div class="flex items-center gap-2">
                  <button @click="toggleMute" class="text-white hover:text-cyan-400 transition-colors">
                    <VolumeX v-if="volume === 0" class="w-5 h-5" />
                    <Volume1 v-else-if="volume < 0.5" class="w-5 h-5" />
                    <Volume2 v-else class="w-5 h-5" />
                  </button>
                  <input
                    type="range"
                    min="0"
                    max="1"
                    step="0.1"
                    :value="volume"
                    @input="setVolume(parseFloat(($event.target as HTMLInputElement).value))"
                    class="w-16 md:w-20 accent-cyan-400"
                  />
                </div>
              </div>
              
              <!-- Center Controls -->
              <div class="flex items-center gap-4">
                <!-- Skip Backward -->
                <button 
                  @click="skipBackward"
                  class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors"
                >
                  <SkipBack class="w-5 h-5 text-white" />
                </button>
                
                <!-- Play/Pause -->
                <button 
                  @click="toggle"
                  :disabled="isLoading"
                  class="w-14 h-14 bg-gradient-to-r from-cyan-500 to-purple-600 rounded-full flex items-center justify-center hover:shadow-lg hover:shadow-cyan-500/25 transition-all duration-300 disabled:opacity-50"
                >
                  <Loader2 v-if="isLoading" class="w-6 h-6 text-white animate-spin" />
                  <Pause v-else-if="isPlaying" class="w-6 h-6 text-white" />
                  <Play v-else class="w-6 h-6 text-white ml-1" />
                </button>
                
                <!-- Skip Forward -->
                <button 
                  @click="skipForward"
                  class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors"
                >
                  <SkipForward class="w-5 h-5 text-white" />
                </button>
              </div>
              
              <!-- Right Controls -->
              <div class="flex items-center gap-3">
                <!-- More Options -->
                <button 
                  @click="showMoreOptions = !showMoreOptions"
                  class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors"
                >
                  <MoreHorizontal class="w-5 h-5 text-white" />
                </button>
              </div>
            </div>
          </div>
          
          <!-- More Options Panel -->
          <Transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0 transform translate-y-4"
            enter-to-class="opacity-100 transform translate-y-0"
            leave-active-class="transition-all duration-200"
            leave-from-class="opacity-100 transform translate-y-0"
            leave-to-class="opacity-0 transform translate-y-4"
          >
            <div v-if="showMoreOptions" class="px-6 pb-6 pt-2 border-t border-white/10">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <button class="p-3 bg-white/10 rounded-xl text-white text-xs font-medium hover:bg-white/20 transition-colors flex items-center gap-2">
                  <Download class="w-4 h-4" />
                  ダウンロード
                </button>
                <button class="p-3 bg-white/10 rounded-xl text-white text-xs font-medium hover:bg-white/20 transition-colors flex items-center gap-2">
                  <Share class="w-4 h-4" />
                  シェア
                </button>
                <button class="p-3 bg-white/10 rounded-xl text-white text-xs font-medium hover:bg-white/20 transition-colors flex items-center gap-2">
                  <Bookmark class="w-4 h-4" />
                  保存
                </button>
                <button class="p-3 bg-white/10 rounded-xl text-white text-xs font-medium hover:bg-white/20 transition-colors flex items-center gap-2">
                  <Info class="w-4 h-4" />
                  詳細
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { Headphones, X, VolumeX, Volume1, Volume2, SkipBack, Loader2, Pause, Play, SkipForward, MoreHorizontal, Download, Share, Bookmark, Info } from 'lucide-vue-next'
import type { AudioGuide } from '~/types'

interface Props {
  audioGuide: AudioGuide | null
  spotName: string | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
}>()

const {
  isPlaying,
  currentTime,
  duration,
  volume,
  playbackRate,
  isLoading,
  progress,
  loadAudio,
  toggle,
  seek,
  seekToPercent,
  setVolume,
  setPlaybackRate,
  formatTime
} = useAudioPlayer()

const progressBarRef = ref<HTMLElement>()
const showSpeedMenu = ref(false)
const showMoreOptions = ref(false)
const previousVolume = ref(1)

const speedOptions = [0.5, 0.75, 1, 1.25, 1.5, 2]

const handleProgressClick = (event: MouseEvent) => {
  if (!progressBarRef.value) return
  
  const rect = progressBarRef.value.getBoundingClientRect()
  const percent = ((event.clientX - rect.left) / rect.width) * 100
  seekToPercent(Math.max(0, Math.min(100, percent)))
}

const skipBackward = () => {
  seek(Math.max(0, currentTime.value - 10))
}

const skipForward = () => {
  seek(Math.min(duration.value, currentTime.value + 10))
}

const toggleMute = () => {
  if (volume.value > 0) {
    previousVolume.value = volume.value
    setVolume(0)
  } else {
    setVolume(previousVolume.value)
  }
}

const closePlayer = () => {
  emit('close')
}

// プロップスの変更を監視して音声を読み込み
watch(() => props.audioGuide, async (newAudioGuide) => {
  if (newAudioGuide?.audio_url) {
    try {
      // デモ用のサンプル音声URL（実際の実装では props.audioGuide.audio_url を使用）
      const sampleAudioUrl = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmUeAzmI0fPNgD8KFG+z7N2RPA'
      
      await loadAudio(sampleAudioUrl)
    } catch (error) {
      console.error('Failed to load audio:', error)
    }
  }
}, { immediate: true })

// クリック外しでメニューを閉じる
onMounted(() => {
  const handleClickOutside = () => {
    showSpeedMenu.value = false
    showMoreOptions.value = false
  }
  
  document.addEventListener('click', handleClickOutside)
  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
  })
})
</script>
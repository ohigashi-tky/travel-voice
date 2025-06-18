<template>
  <div class="audio-player bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-4">
      <h4 class="font-medium text-gray-900">{{ title }}</h4>
      <div class="text-sm text-gray-500">
        {{ formatTime(currentTime) }} / {{ formatTime(duration) }}
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="mb-4">
      <div 
        class="w-full bg-gray-200 rounded-full h-2 cursor-pointer"
        @click="seekTo"
      >
        <div 
          class="bg-primary-600 h-2 rounded-full transition-all duration-150"
          :style="{ width: progressPercent + '%' }"
        ></div>
      </div>
    </div>

    <!-- Controls -->
    <div class="flex items-center justify-center space-x-4">
      <!-- Skip Backward -->
      <button
        class="p-2 text-gray-600 hover:text-primary-600 transition-colors"
        @click="skipBackward"
        :disabled="currentTime <= 0"
      >
        <Icon name="lucide:skip-back" class="w-5 h-5" />
      </button>

      <!-- Play/Pause -->
      <button
        class="flex items-center justify-center w-12 h-12 bg-primary-600 hover:bg-primary-700 text-white rounded-full transition-colors"
        @click="togglePlay"
        :disabled="!audioUrl"
      >
        <Icon 
          :name="isPlaying ? 'lucide:pause' : 'lucide:play'" 
          class="w-6 h-6"
          :class="{ 'ml-1': !isPlaying }"
        />
      </button>

      <!-- Skip Forward -->
      <button
        class="p-2 text-gray-600 hover:text-primary-600 transition-colors"
        @click="skipForward"
        :disabled="currentTime >= duration"
      >
        <Icon name="lucide:skip-forward" class="w-5 h-5" />
      </button>

      <!-- Volume -->
      <div class="flex items-center space-x-2">
        <button
          class="p-2 text-gray-600 hover:text-primary-600 transition-colors"
          @click="toggleMute"
        >
          <Icon 
            :name="isMuted ? 'lucide:volume-x' : volume > 0.5 ? 'lucide:volume-2' : 'lucide:volume-1'" 
            class="w-5 h-5" 
          />
        </button>
        
        <input
          v-model.number="volume"
          type="range"
          min="0"
          max="1"
          step="0.1"
          class="w-20 accent-primary-600"
          @input="updateVolume"
        >
      </div>

      <!-- Speed -->
      <div class="relative">
        <button
          class="p-2 text-gray-600 hover:text-primary-600 transition-colors text-sm font-medium"
          @click="showSpeedMenu = !showSpeedMenu"
        >
          {{ playbackRate }}x
        </button>
        
        <div 
          v-if="showSpeedMenu"
          class="absolute bottom-full right-0 mb-2 bg-white border border-gray-200 rounded-lg shadow-lg py-1 min-w-[80px]"
        >
          <button
            v-for="speed in [0.5, 0.75, 1, 1.25, 1.5, 2]"
            :key="speed"
            class="block w-full px-3 py-1 text-sm text-left hover:bg-gray-50 transition-colors"
            :class="{ 'text-primary-600 font-medium': playbackRate === speed }"
            @click="setPlaybackRate(speed)"
          >
            {{ speed }}x
          </button>
        </div>
      </div>
    </div>

    <!-- Hidden Audio Element -->
    <audio
      ref="audioElement"
      :src="audioUrl"
      preload="metadata"
      @loadedmetadata="onLoadedMetadata"
      @timeupdate="onTimeUpdate"
      @ended="onEnded"
      @error="onError"
    ></audio>

    <!-- Error Message -->
    <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
      <p class="text-sm text-red-800">
        <Icon name="lucide:alert-circle" class="w-4 h-4 inline mr-2" />
        音声の読み込みに失敗しました
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  audioUrl: string
  title: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  ended: []
  error: [error: Event]
}>()

const audioElement = ref<HTMLAudioElement>()
const isPlaying = ref(false)
const currentTime = ref(0)
const duration = ref(0)
const volume = ref(1)
const isMuted = ref(false)
const playbackRate = ref(1)
const showSpeedMenu = ref(false)
const error = ref(false)

const progressPercent = computed(() => {
  return duration.value > 0 ? (currentTime.value / duration.value) * 100 : 0
})

const formatTime = (seconds: number): string => {
  const mins = Math.floor(seconds / 60)
  const secs = Math.floor(seconds % 60)
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const togglePlay = () => {
  if (!audioElement.value) return

  if (isPlaying.value) {
    audioElement.value.pause()
  } else {
    audioElement.value.play()
  }
  isPlaying.value = !isPlaying.value
}

const skipBackward = () => {
  if (!audioElement.value) return
  audioElement.value.currentTime = Math.max(0, audioElement.value.currentTime - 10)
}

const skipForward = () => {
  if (!audioElement.value) return
  audioElement.value.currentTime = Math.min(duration.value, audioElement.value.currentTime + 10)
}

const seekTo = (event: MouseEvent) => {
  if (!audioElement.value) return
  
  const rect = (event.target as HTMLElement).getBoundingClientRect()
  const percent = (event.clientX - rect.left) / rect.width
  audioElement.value.currentTime = percent * duration.value
}

const toggleMute = () => {
  if (!audioElement.value) return
  
  isMuted.value = !isMuted.value
  audioElement.value.muted = isMuted.value
}

const updateVolume = () => {
  if (!audioElement.value) return
  audioElement.value.volume = volume.value
}

const setPlaybackRate = (rate: number) => {
  if (!audioElement.value) return
  
  playbackRate.value = rate
  audioElement.value.playbackRate = rate
  showSpeedMenu.value = false
}

const onLoadedMetadata = () => {
  if (!audioElement.value) return
  duration.value = audioElement.value.duration
  error.value = false
}

const onTimeUpdate = () => {
  if (!audioElement.value) return
  currentTime.value = audioElement.value.currentTime
}

const onEnded = () => {
  isPlaying.value = false
  currentTime.value = 0
  emit('ended')
}

const onError = (event: Event) => {
  error.value = true
  isPlaying.value = false
  emit('error', event)
}

// Cleanup on unmount
onUnmounted(() => {
  if (audioElement.value) {
    audioElement.value.pause()
    audioElement.value.src = ''
  }
})

// Handle clicks outside speed menu
onMounted(() => {
  const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement
    if (!target.closest('.relative')) {
      showSpeedMenu.value = false
    }
  }
  
  document.addEventListener('click', handleClickOutside)
  
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
})
</script>
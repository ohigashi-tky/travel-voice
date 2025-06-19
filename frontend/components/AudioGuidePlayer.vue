<template>
  <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click="closePlayer">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl transform transition-all duration-300" @click.stop>
      <!-- Header -->
      <div class="flex items-center justify-end mb-6">
        <button @click="closePlayer" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
          <X class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        </button>
      </div>

      <!-- Spot Info -->
      <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl mx-auto mb-4 flex items-center justify-center">
          <Headphones class="w-10 h-10 text-white" />
        </div>
        <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">{{ spotName }}</h4>
        <p class="text-gray-600 dark:text-gray-300 text-sm">{{ audioGuide?.title }}</p>
      </div>

      <!-- Progress Bar -->
      <div class="mb-6">
        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
          <span>{{ formatTime(currentTime) }}</span>
          <span>{{ formatTime(duration) }}</span>
        </div>
        <div 
          class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full cursor-pointer"
          @click="handleProgressClick"
          ref="progressBar"
        >
          <div 
            class="h-full bg-gradient-to-r from-cyan-500 to-blue-600 rounded-full transition-all duration-150"
            :style="{ width: `${progress}%` }"
          ></div>
        </div>
      </div>

      <!-- Controls -->
      <div class="flex items-center justify-center space-x-6 mb-6">
        <!-- Previous (10s back) -->
        <button 
          @click="seekBackward"
          class="p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
        >
          <RotateCcw class="w-6 h-6 text-gray-600 dark:text-gray-300" />
        </button>

        <!-- Play/Pause -->
        <button 
          @click="togglePlayPause"
          :disabled="isLoading"
          class="w-16 h-16 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-105 transition-all duration-200 disabled:opacity-50"
        >
          <component 
            :is="isLoading ? 'div' : (isPlaying ? Pause : Play)" 
            :class="isLoading ? 'animate-spin w-6 h-6 border-2 border-white border-t-transparent rounded-full' : 'w-8 h-8'"
          />
        </button>

        <!-- Forward (10s forward) -->
        <button 
          @click="seekForward"
          class="p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
        >
          <RotateCw class="w-6 h-6 text-gray-600 dark:text-gray-300" />
        </button>
      </div>

      <!-- Volume Control -->
      <div class="flex items-center space-x-3 mb-4">
        <Volume2 class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        <div class="flex-1">
          <input
            type="range"
            min="0"
            max="100"
            v-model="volume"
            @input="updateVolume"
            class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer slider"
          >
        </div>
        <span class="text-xs text-gray-500 dark:text-gray-400 w-8">{{ volume }}</span>
      </div>

      <!-- Speed Control -->
      <div class="flex items-center justify-between text-sm">
        <span class="text-gray-600 dark:text-gray-300">再生速度</span>
        <div class="flex space-x-2">
          <button 
            v-for="speed in [0.75, 1.0, 1.25, 1.5]" 
            :key="speed"
            @click="setPlaybackRate(speed)"
            :class="[
              'px-3 py-1 rounded-lg transition-colors',
              playbackRate === speed 
                ? 'bg-blue-500 text-white' 
                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
            ]"
          >
            {{ speed }}x
          </button>
        </div>
      </div>

      <!-- Debug Info (Development) -->
      <div v-if="voices.length === 0" class="mt-4 p-3 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-700 rounded-lg">
        <p class="text-yellow-700 dark:text-yellow-400 text-sm">音声エンジンを初期化中です...</p>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mt-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg">
        <p class="text-red-700 dark:text-red-400 text-sm">{{ errorMessage }}</p>
        <button 
          @click="initSpeechSynthesis" 
          class="mt-2 px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors"
        >
          再初期化
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Play, Pause, X, Headphones, Volume2, RotateCcw, RotateCw } from 'lucide-vue-next'

interface AudioGuide {
  id: number
  title: string
  description?: string
  transcript?: string
}

// Props
const props = defineProps<{
  audioGuide: AudioGuide | null
  spotName: string | null
  isVisible: boolean
}>()

// Emits
const emit = defineEmits<{
  close: []
}>()

// Reactive state
const isPlaying = ref(false)
const isLoading = ref(false)
const currentTime = ref(0)
const duration = ref(0)
const volume = ref(75)
const playbackRate = ref(1.0)
const errorMessage = ref('')
const progressBar = ref<HTMLElement>()

// Audio synthesis
const synth = ref<SpeechSynthesis | null>(null)
const utterance = ref<SpeechSynthesisUtterance | null>(null)
const voices = ref<SpeechSynthesisVoice[]>([])

// Computed
const progress = computed(() => {
  if (duration.value === 0) return 0
  return (currentTime.value / duration.value) * 100
})

// Methods
const initSpeechSynthesis = () => {
  if ('speechSynthesis' in window) {
    synth.value = window.speechSynthesis
    
    // 強制的に音声リストを取得
    const loadVoicesWithRetry = () => {
      voices.value = synth.value!.getVoices()
      console.log('利用可能な音声:', voices.value.length)
      
      if (voices.value.length === 0) {
        // 音声リストが空の場合、少し待ってから再試行
        setTimeout(loadVoicesWithRetry, 100)
      }
    }
    
    // 初回読み込み
    loadVoicesWithRetry()
    
    // voiceschangedイベントも設定
    synth.value.addEventListener('voiceschanged', () => {
      voices.value = synth.value!.getVoices()
      console.log('音声リスト更新:', voices.value.length)
    })
  } else {
    errorMessage.value = 'お使いのブラウザは音声合成に対応していません'
  }
}

const getOptimalVoice = () => {
  if (voices.value.length === 0) return null
  
  // 日本語の音声を優先的に選択
  const japaneseVoices = voices.value.filter(voice => 
    voice.lang.includes('ja') || voice.name.includes('Japanese')
  )
  
  if (japaneseVoices.length > 0) {
    // より自然な音声を選択（Google、Microsoft、Appleの順）
    const preferredVoices = japaneseVoices.filter(voice => 
      voice.name.includes('Google') || 
      voice.name.includes('Microsoft') || 
      voice.name.includes('Apple')
    )
    return preferredVoices[0] || japaneseVoices[0]
  }
  
  // 日本語音声がない場合はデフォルト
  return voices.value[0]
}

const generateAudioContent = () => {
  if (!props.audioGuide || !props.spotName) return ''
  
  // 観光地の詳細なガイド文章を生成
  const guidTexts: Record<string, string> = {
    '東京スカイツリー': `東京スカイツリーへようこそ。高さ634メートルを誇るこの電波塔は、2012年に開業し、東京の新たなシンボルとなりました。
    
スカイツリーの高さ634メートルは、「むさし」という語呂合わせで、武蔵国（現在の東京都、埼玉県、神奈川県の一部）に由来しています。

展望デッキからは、東京の街並みを360度見渡すことができ、晴れた日には富士山まで望むことができます。特に夜景は息をのむほど美しく、多くの観光客を魅了しています。

建物の構造は、伝統的な日本建築の「そり」と「むくり」を現代的にアレンジしており、和の美意識と最新技術が見事に融合された傑作です。`,

    '浅草寺': `浅草寺へようこそ。正式名称は金龍山浅草寺といい、1400年近い歴史を持つ東京最古のお寺です。

この寺の始まりは、西暦628年、隅田川で漁をしていた檜前浜成・竹成兄弟が、観音菩薩像を網で引き上げたことから始まります。その後、土師中知がこの像を自宅に安置したのが浅草寺の起源とされています。

雷門の正式名称は「風雷神門」で、風神と雷神が守護しています。門の下に吊り下げられた大きな提灯は、高さ3.9メートル、重さ約700キログラムの巨大なもので、浅草のシンボルとなっています。

仲見世通りは、日本で最も古い商店街の一つで、江戸時代から続く伝統的なお店が軒を連ねています。ここでは、人形焼きや雷おこしなどの伝統的なお菓子を味わうことができます。`,

    '明治神宮': `明治神宮へようこそ。明治天皇と昭憲皇太后を祀るこの神社は、1920年に創建され、都心にありながら豊かな自然に囲まれた神聖な空間です。

この森は、全国から献木された約10万本の木々によって造営された人工の森です。当初は何もない野原でしたが、150年後の姿を見据えた壮大な計画のもと、現在のような深い森へと成長しました。

境内には、約175種類の樹木が植えられており、四季折々の美しい自然を楽しむことができます。特に秋の紅葉と新緑の季節は格別の美しさです。

毎年、初詣の参拝者数は日本一を誇り、新年には300万人以上の人々が訪れます。また、多くの著名人の結婚式が行われることでも知られ、神前式の聖地としても親しまれています。`
  }
  
  return guidTexts[props.spotName] || `${props.spotName}の音声ガイドです。この美しい観光地について詳しくご紹介いたします。`
}

const togglePlayPause = async () => {
  // エラーメッセージをクリア
  errorMessage.value = ''
  
  // ユーザーインタラクションによる音声有効化（ブラウザのポリシー対応）
  if (synth.value && synth.value.getVoices().length === 0) {
    // 強制的にgetVoicesを実行
    synth.value.getVoices()
    await new Promise(resolve => setTimeout(resolve, 100))
  }

  if (isPlaying.value) {
    pauseAudio()
  } else {
    await playAudio()
  }
}

const playAudio = async () => {
  if (!synth.value || !props.audioGuide) {
    console.log('音声合成またはオーディオガイドが利用できません')
    isLoading.value = false
    return
  }
  
  try {
    isLoading.value = true
    errorMessage.value = ''
    
    console.log('音声再生開始...')
    
    // 既存の音声を停止
    synth.value.cancel()
    
    // 短時間待機してからリセット
    await new Promise(resolve => setTimeout(resolve, 100))
    
    // 新しい音声を作成
    const text = generateAudioContent()
    console.log('音声テキスト長:', text.length)
    
    if (text.length === 0) {
      throw new Error('音声テキストが生成されませんでした')
    }
    
    utterance.value = new SpeechSynthesisUtterance(text)
    
    // 音声設定を先に行う
    utterance.value.volume = volume.value / 100
    utterance.value.rate = playbackRate.value
    utterance.value.pitch = 1.0
    utterance.value.lang = 'ja-JP'
    
    // 最適な音声を設定（音声設定の後に行う）
    const voice = getOptimalVoice()
    console.log('選択された音声:', voice?.name || 'デフォルト')
    if (voice) {
      utterance.value.voice = voice
    }
    
    // イベントリスナー設定
    utterance.value.onstart = () => {
      console.log('音声再生開始イベント')
      isPlaying.value = true
      isLoading.value = false
      // 概算の duration を設定（1分あたり約300文字として計算）
      duration.value = Math.max(10, text.length / 5) // 最低10秒、1文字0.2秒の概算
      currentTime.value = 0
      updateProgress()
    }
    
    utterance.value.onend = () => {
      console.log('音声再生終了')
      isPlaying.value = false
      isLoading.value = false
      currentTime.value = 0
    }
    
    utterance.value.onerror = (event) => {
      console.error('Speech synthesis error:', event.error)
      
      // "interrupted"は停止時の正常なエラーなので表示しない
      if (event.error !== 'interrupted') {
        errorMessage.value = `音声エラー: ${event.error}`
      }
      
      isPlaying.value = false
      isLoading.value = false
    }
    
    utterance.value.onpause = () => {
      console.log('音声一時停止')
      isPlaying.value = false
    }
    
    utterance.value.onresume = () => {
      console.log('音声再開')
      isPlaying.value = true
      isLoading.value = false
    }
    
    // 再生開始
    console.log('speak() 実行中...')
    synth.value.speak(utterance.value)
    
    // 即座に状態を更新（onstartイベントが発火しない場合の対応）
    setTimeout(() => {
      if (synth.value && synth.value.speaking && isLoading.value) {
        console.log('手動で再生状態に更新')
        isPlaying.value = true
        isLoading.value = false
        duration.value = Math.max(10, text.length / 5)
        currentTime.value = 0
        updateProgress()
      }
    }, 500)
    
    // タイムアウト設定（3秒以内に開始しない場合）
    setTimeout(() => {
      if (isLoading.value) {
        console.log('音声再生タイムアウト')
        errorMessage.value = '音声の読み込みがタイムアウトしました。再試行してください。'
        isLoading.value = false
        synth.value?.cancel()
      }
    }, 3000)
    
  } catch (error) {
    console.error('Error playing audio:', error)
    errorMessage.value = '音声の再生に失敗しました: ' + (error as Error).message
    isLoading.value = false
  }
}

const pauseAudio = () => {
  console.log('音声停止実行')
  if (synth.value) {
    if (synth.value.speaking) {
      synth.value.cancel() // pause()ではなくcancel()で完全停止
    }
    isPlaying.value = false
    isLoading.value = false
    errorMessage.value = '' // エラーメッセージをクリア
  }
}

const stopAudio = () => {
  console.log('音声完全停止')
  if (synth.value) {
    synth.value.cancel()
    isPlaying.value = false
    isLoading.value = false
    currentTime.value = 0
    errorMessage.value = '' // エラーメッセージをクリア
  }
}

const updateProgress = () => {
  if (isPlaying.value && duration.value > 0 && synth.value?.speaking) {
    currentTime.value += 0.1
    if (currentTime.value >= duration.value) {
      currentTime.value = duration.value
      isPlaying.value = false
      isLoading.value = false
    } else {
      setTimeout(updateProgress, 100)
    }
  } else if (!synth.value?.speaking && isPlaying.value) {
    // 音声が実際には止まっているのに状態がisPlayingの場合
    isPlaying.value = false
    isLoading.value = false
  }
}

const seekBackward = () => {
  currentTime.value = Math.max(0, currentTime.value - 10)
}

const seekForward = () => {
  currentTime.value = Math.min(duration.value, currentTime.value + 10)
}

const updateVolume = () => {
  if (utterance.value) {
    utterance.value.volume = volume.value / 100
  }
}

const setPlaybackRate = (rate: number) => {
  playbackRate.value = rate
  if (utterance.value) {
    utterance.value.rate = rate
  }
}

const handleProgressClick = (event: MouseEvent) => {
  if (!progressBar.value || duration.value === 0) return
  
  const rect = progressBar.value.getBoundingClientRect()
  const clickX = event.clientX - rect.left
  const width = rect.width
  const percentage = (clickX / width) * 100
  
  currentTime.value = (percentage / 100) * duration.value
}

const formatTime = (seconds: number): string => {
  const mins = Math.floor(seconds / 60)
  const secs = Math.floor(seconds % 60)
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const closePlayer = () => {
  stopAudio()
  emit('close')
}

// Lifecycle
onMounted(() => {
  initSpeechSynthesis()
})

onUnmounted(() => {
  stopAudio()
})

// Watch for prop changes
watch(() => props.isVisible, (newVal) => {
  if (!newVal) {
    stopAudio()
    errorMessage.value = ''
  }
})

watch(() => props.audioGuide, () => {
  if (isPlaying.value || isLoading.value) {
    stopAudio()
  }
  errorMessage.value = ''
})
</script>

<style scoped>
/* Custom slider styles */
.slider::-webkit-slider-thumb {
  appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: linear-gradient(45deg, #06b6d4, #2563eb);
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.slider::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: linear-gradient(45deg, #06b6d4, #2563eb);
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}
</style>
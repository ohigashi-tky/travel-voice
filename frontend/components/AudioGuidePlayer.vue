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
        <button 
          @click="testSpeech" 
          class="mt-2 px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600 transition-colors"
        >
          テスト再生
        </button>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mt-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg">
        <p class="text-red-700 dark:text-red-400 text-sm">{{ errorMessage }}</p>
        <div class="mt-2 flex gap-2">
          <button 
            @click="initSpeechSynthesis" 
            class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors"
          >
            再初期化
          </button>
          <button 
            @click="testSpeech" 
            class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors"
          >
            テスト再生
          </button>
        </div>
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

// Audio tracking
const audioStartTime = ref(0)
const audioText = ref('')
const audioSegments = ref<string[]>([])
const currentSegmentIndex = ref(0)
const actualDuration = ref(0) // 実際の音声の長さを記録


// Computed
const progress = computed(() => {
  if (duration.value === 0) return 0
  return (currentTime.value / duration.value) * 100
})

// Methods
const initSpeechSynthesis = () => {
  console.log('🔧 音声合成エンジン初期化開始')
  
  // ブラウザ対応チェック
  if (!('speechSynthesis' in window)) {
    console.error('❌ ブラウザが音声合成に対応していません')
    errorMessage.value = 'お使いのブラウザは音声合成機能に対応していません。Chrome、Firefox、Safariなどのモダンブラウザをお使いください。'
    return
  }
  
  // SpeechSynthesisオブジェクトを取得
  const synthesis = window.speechSynthesis
  synth.value = synthesis
  
  // 既存の音声をクリア
  synthesis.cancel()
  
  console.log('🔍 ブラウザ情報:', {
    userAgent: navigator.userAgent.substring(0, 50),
    platform: navigator.platform,
    speechSynthesis: typeof synthesis
  })
  
  // 音声リストを取得する関数
  const loadVoices = () => {
    try {
      voices.value = synthesis.getVoices()
      console.log('🎤 利用可能音声数:', voices.value.length)
      
      if (voices.value.length > 0) {
        const japaneseVoices = voices.value.filter(voice => 
          voice.lang.includes('ja') || voice.lang.includes('JP')
        )
        console.log('🇯🇵 日本語音声数:', japaneseVoices.length)
        
        // 日本語音声の詳細をログ出力
        japaneseVoices.forEach((voice, index) => {
          console.log(`  ${index + 1}. ${voice.name} (${voice.lang}) - ${voice.localService ? 'ローカル' : 'リモート'}`)
        })
      } else {
        console.warn('⚠️ 音声リストが空です。ブラウザが音声を読み込み中の可能性があります。')
      }
      
      return voices.value.length > 0
    } catch (error) {
      console.error('❌ 音声リスト取得エラー:', error)
      return false
    }
  }
  
  // 初回読み込み
  const initialLoad = loadVoices()
  
  // voiceschangedイベントリスナー
  const voicesChangedHandler = () => {
    console.log('🔄 音声リスト更新イベント')
    loadVoices()
  }
  
  synthesis.addEventListener('voiceschanged', voicesChangedHandler)
  
  // フォールバック: 音声がまだ読み込まれていない場合の再試行
  if (!initialLoad) {
    console.log('🔄 音声リストの遅延読み込みを試行中...')
    
    setTimeout(() => {
      if (voices.value.length === 0) {
        console.log('🔄 第1回再試行')
        loadVoices()
      }
    }, 500)
    
    setTimeout(() => {
      if (voices.value.length === 0) {
        console.log('🔄 第2回再試行')
        loadVoices()
      }
    }, 1500)
    
    setTimeout(() => {
      if (voices.value.length === 0) {
        console.warn('⚠️ 音声リストの読み込みに失敗しました。デフォルト音声で再生します。')
      } else {
        console.log('✅ 遅延読み込み成功')
      }
    }, 3000)
  }
  
  console.log('✅ 音声合成エンジン初期化完了')
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

const splitTextIntoSegments = (text: string): string[] => {
  // 文を句読点で分割し、音声のシークを可能にする
  const sentences = text.split(/[。！？]/g).filter(s => s.trim().length > 0)
  return sentences.map(s => s.trim() + '。')
}

const generateSimpleAudioContent = () => {
  if (!props.spotName) {
    return 'こんにちは。音声ガイドへようこそ。'
  }
  
  // シンプルで確実に再生される短いテキスト
  const simpleTexts: Record<string, string> = {
    '東京スカイツリー': 'こんにちは。東京スカイツリーの音声ガイドです。高さ634メートルのこの電波塔は、東京の新しいシンボルとして2012年に開業しました。展望デッキからは東京の街並みを360度見渡すことができ、晴れた日には富士山も見えます。スカイツリーの高さ634メートルは、むさしという語呂合わせで、武蔵国に由来しています。',
    
    '浅草寺': 'こんにちは。浅草寺の音声ガイドです。正式名称は金龍山浅草寺といい、1400年近い歴史を持つ東京最古のお寺です。雷門の大きな提灯は高さ3.9メートル、重さ約700キログラムで、浅草のシンボルとなっています。仲見世通りでは人形焼きや雷おこしなどの伝統的なお菓子を楽しむことができます。',
    
    '明治神宮': 'こんにちは。明治神宮の音声ガイドです。明治天皇と昭憲皇太后を祀るこの神社は、1920年に創建されました。都心にありながら豊かな自然に囲まれた神聖な空間です。全国から献木された約10万本の木々によって造営された人工の森で、毎年初詣の参拝者数は日本一を誇ります。',
    
    '大阪城': 'こんにちは。大阪城の音声ガイドです。豊臣秀吉が1583年に築城したこの名城は、大阪のシンボルとして親しまれています。現在の天守閣は1931年に再建されたもので、高さ約55メートルから大阪の街並みを一望できます。桜の名所としても有名で、春には約3000本の桜が城を彩ります。金の鯱鉾が輝く美しい天守閣は、大阪の歴史と文化を物語っています。',
    
    '通天閣': 'こんにちは。通天閣の音声ガイドです。1912年に建設されたこの展望塔は、新世界のシンボルとして100年以上愛され続けています。高さ103メートルの塔からは大阪の街並みを360度見渡すことができ、夜にはライトアップされた美しい姿を楽しめます。足の裏を撫でると幸運が訪れるという黄金のビリケンさんでも有名です。',
    
    '海遊館': 'こんにちは。海遊館の音声ガイドです。1990年に開館した世界最大級の水族館で、太平洋を再現した巨大な水槽が自慢です。ジンベエザメやマンタが悠々と泳ぐ姿は圧巻で、まるで海の中にいるような感動を味わえます。ペンギンやアザラシなど様々な海の生き物たちとの出会いが待っています。大阪湾に面した美しい立地も魅力の一つです。',
    
    '清水寺': 'こんにちは。清水寺の音声ガイドです。778年に開創された京都最古の寺院で、世界文化遺産に登録されています。有名な清水の舞台は高さ13メートルあり、139本のケヤキの柱が釘を一本も使わずに組まれています。春の桜、秋の紅葉の季節には特に美しく、京都の街並みを一望できます。音羽の滝では三筋の水が流れ、学問成就、恋愛成就、延命長寿のご利益があると言われています。',
    
    '金閣寺': 'こんにちは。金閣寺の音声ガイドです。正式名称は鹿苑寺といい、室町幕府三代将軍足利義満の別荘として1397年に建てられました。金箔で覆われた三階建ての楼閣は、まさに京都を代表する美しさです。池に映る逆さ金閣も見どころの一つで、四季折々の風景と調和した姿は息をのむ美しさです。世界文化遺産に登録され、年間を通じて多くの参拝者が訪れます。',
    
    '伏見稲荷大社': 'こんにちは。伏見稲荷大社の音声ガイドです。全国に約3万社ある稲荷神社の総本宮で、千本鳥居で有名です。実際には約1万基の鳥居が山全体に建てられており、朱色のトンネルが約4キロメートル続きます。商売繁盛、五穀豊穣の神様として信仰され、お稲荷さんの使いである狐の石像も数多く見ることができます。',
    
    '札幌時計台': 'こんにちは。札幌時計台の音声ガイドです。正式名称は旧札幌農学校演武場といい、1878年に建設された北海道のシンボルです。クラーク博士で有名な札幌農学校の象徴的建物で、現在も正確に時を刻み続けています。赤い屋根と白い壁の美しいコントラストは、札幌の街の中心部で多くの人に愛され続けています。内部では札幌の歴史を学ぶことができます。',
    
    '函館山': 'こんにちは。函館山の音声ガイドです。標高334メートルのこの山からの夜景は、世界三大夜景の一つに数えられています。津軽海峡と函館湾に挟まれた独特の地形が作り出すくびれた夜景は、まるで宝石をちりばめたような美しさです。ロープウェイで約3分の空中散歩も楽しめ、春から秋にかけては登山も人気です。',
    
    '小樽運河': 'こんにちは。小樽運河の音声ガイドです。1923年に完成したこの運河は、北海道開拓時代の面影を今に伝える歴史的な場所です。石造りの倉庫群と運河沿いに並ぶガス灯が作り出すノスタルジックな風景は、多くの人を魅了します。特に夕暮れ時から夜にかけてのライトアップされた景色は格別で、ロマンチックな散歩コースとして人気です。'
  }
  
  return simpleTexts[props.spotName] || `こんにちは。${props.spotName}の音声ガイドです。この美しい観光地の魅力をお楽しみください。`
}

const togglePlayPause = async () => {
  console.log('🔄 再生/停止ボタンクリック')
  
  // エラーメッセージをクリア
  errorMessage.value = ''
  
  if (!synth.value) {
    console.log('❌ 音声合成エンジンが利用できません')
    errorMessage.value = '音声合成エンジンが利用できません'
    return
  }
  
  if (isPlaying.value) {
    console.log('⏸️ 停止実行')
    pauseAudio()
  } else {
    console.log('▶️ 再生実行')
    await playAudio()
  }
}

const playAudio = async () => {
  console.log('🚀 === 音声再生開始 ===')
  
  // 基本的なチェック
  if (!props.audioGuide || !props.spotName) {
    console.error('❌ 音声ガイドまたは観光地名がありません')
    errorMessage.value = '音声ガイドの情報が不足しています'
    isLoading.value = false
    return
  }
  
  // ブラウザ対応チェック
  if (!('speechSynthesis' in window)) {
    console.error('❌ ブラウザが音声合成に対応していません')
    errorMessage.value = 'お使いのブラウザは音声合成に対応していません'
    isLoading.value = false
    return
  }
  
  try {
    isLoading.value = true
    errorMessage.value = ''
    
    // 音声合成エンジンを取得
    const synthesis = window.speechSynthesis
    
    // 既存の音声を完全に停止
    synthesis.cancel()
    await new Promise(resolve => setTimeout(resolve, 150))
    
    // 音声テキストを生成
    const audioText = generateSimpleAudioContent()
    console.log('📝 音声テキスト長:', audioText.length)
    console.log('📝 音声テキスト開始:', audioText.substring(0, 50) + '...')
    
    if (!audioText || audioText.length === 0) {
      throw new Error('音声テキストが生成できませんでした')
    }
    
    // シンプルなutteranceを作成
    const newUtterance = new SpeechSynthesisUtterance(audioText)
    
    // 基本設定を適用
    newUtterance.volume = Math.max(0.1, Math.min(1.0, volume.value / 100))
    newUtterance.rate = Math.max(0.5, Math.min(2.0, playbackRate.value))
    newUtterance.pitch = 1.0
    newUtterance.lang = 'ja-JP'
    
    console.log('🔧 音声設定:', {
      volume: newUtterance.volume,
      rate: newUtterance.rate,
      textLength: audioText.length
    })
    
    // 音声を選択（日本語優先）
    const availableVoices = synthesis.getVoices()
    console.log('🎤 利用可能音声数:', availableVoices.length)
    
    if (availableVoices.length > 0) {
      // 日本語音声を検索
      const japaneseVoice = availableVoices.find(voice => 
        voice.lang.startsWith('ja') || voice.lang.includes('JP')
      )
      
      if (japaneseVoice) {
        newUtterance.voice = japaneseVoice
        console.log('✅ 日本語音声選択:', japaneseVoice.name, japaneseVoice.lang)
      } else {
        console.log('⚠️ 日本語音声が見つかりません、デフォルト音声を使用')
      }
    } else {
      console.log('⚠️ 音声リストが空です')
    }
    
    // duration設定（既に測定済みの場合は実際の長さを使用）
    const chars = audioText.length
    
    if (actualDuration.value > 0) {
      // 既に実際の長さが分かっている場合はそれを使用
      duration.value = actualDuration.value
      console.log('⏱️ 既知の実際の長さを使用:', actualDuration.value, '秒')
    } else {
      // 初回は推定値を使用（実測データに基づく修正済み計算式）
      // 実測: 33秒表示で実際29秒 → 29/33 = 0.88倍に調整
      const estimatedDuration = Math.max(12, Math.round(chars * 0.19))
      duration.value = estimatedDuration
      console.log('⏱️ 初期推定時間:', estimatedDuration, '秒 (文字数:', chars, ', 実測29秒ベース計算) - 再生後に実際の長さで更新')
    }
    
    currentTime.value = 0
    audioStartTime.value = 0
    
    // 成功フラグ
    let hasStarted = false
    
    // イベントハンドラー（実際の音声長を測定）
    newUtterance.onstart = () => {
      console.log('✅ 音声再生開始イベント！')
      hasStarted = true
      isPlaying.value = true
      isLoading.value = false
      audioStartTime.value = Date.now()
      
      updateProgress()
    }
    
    newUtterance.onend = () => {
      const endTime = Date.now()
      const realDuration = (endTime - audioStartTime.value) / 1000
      
      console.log('✅ 音声再生終了')
      console.log('⏱️ 実際の音声長:', realDuration, '秒')
      console.log('📊 推定との差:', Math.abs(realDuration - duration.value), '秒')
      
      // 実際の長さを記録
      actualDuration.value = realDuration
      duration.value = realDuration
      currentTime.value = realDuration
      
      isPlaying.value = false
      isLoading.value = false
    }
    
    newUtterance.onerror = (event) => {
      console.error('❌ 音声エラー:', event.error, event)
      if (event.error !== 'interrupted' && event.error !== 'canceled') {
        errorMessage.value = `音声エラー: ${event.error}`
      }
      isPlaying.value = false
      isLoading.value = false
      hasStarted = false
    }
    
    // utteranceを保存
    utterance.value = newUtterance
    
    // 音声再生を実行
    console.log('🎵 synthesis.speak() 実行...')
    synthesis.speak(newUtterance)
    
    // フォールバック検証（3秒後）
    setTimeout(() => {
      console.log('🔍 フォールバック検証:', {
        isLoading: isLoading.value,
        hasStarted,
        speaking: synthesis.speaking,
        pending: synthesis.pending
      })
      
      if (isLoading.value && !hasStarted) {
        if (synthesis.speaking) {
          console.log('✅ 音声再生を手動検出！')
          isPlaying.value = true
          isLoading.value = false
          audioStartTime.value = Date.now()
          updateProgress()
        } else if (synthesis.pending) {
          console.log('⏳ 音声が待機中...')
          // もう少し待つ
        } else {
          console.error('❌ 音声再生が開始されませんでした')
          isLoading.value = false
          errorMessage.value = '音声再生が開始されません。ブラウザの設定やマイクの権限を確認してください。'
        }
      }
    }, 3000)
    
  } catch (error) {
    console.error('❌ playAudio 重大エラー:', error)
    errorMessage.value = `音声再生エラー: ${(error as Error).message}`
    isLoading.value = false
    isPlaying.value = false
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
  if (isPlaying.value && duration.value > 0) {
    if (synth.value?.speaking && audioStartTime.value > 0) {
      // 実際の経過時間を基準にプログレスを更新
      const elapsed = (Date.now() - audioStartTime.value) / 1000
      const segmentTime = currentSegmentIndex.value * (duration.value / audioSegments.value.length)
      currentTime.value = Math.min(segmentTime + elapsed, duration.value)
    }
    
    if (currentTime.value >= duration.value) {
      currentTime.value = duration.value
      isPlaying.value = false
      isLoading.value = false
    } else if (synth.value?.speaking) {
      setTimeout(updateProgress, 100)
    }
  } else if (!synth.value?.speaking && isPlaying.value) {
    isPlaying.value = false
    isLoading.value = false
  }
}

const seekBackward = async () => {
  const newTime = Math.max(0, currentTime.value - 10)
  await seekToTime(newTime)
}

const seekForward = async () => {
  const newTime = Math.min(duration.value, currentTime.value + 10)
  await seekToTime(newTime)
}

const seekToTime = async (targetTime: number) => {
  currentTime.value = targetTime
  
  if (isPlaying.value && audioSegments.value.length > 0) {
    // 再生中の場合は、新しい位置から再開
    console.log('シーク実行:', targetTime)
    await playAudio()
  }
}

const updateVolume = async () => {
  console.log('音量変更:', volume.value)
  
  // 新しい音声が作成される際に適用される音量を設定
  if (utterance.value) {
    utterance.value.volume = volume.value / 100
  }
  
  // 再生中の場合は音量を即座に反映するため、現在位置から再開
  if (isPlaying.value && synth.value?.speaking) {
    console.log('音量変更のため音声を再開します')
    const currentTimeBackup = currentTime.value
    
    // 音声を停止
    synth.value.cancel()
    
    // 少し待ってから同じ位置で再開
    setTimeout(async () => {
      currentTime.value = currentTimeBackup
      await playAudio()
    }, 100)
  }
}

const setPlaybackRate = (rate: number) => {
  const oldRate = playbackRate.value
  playbackRate.value = rate
  
  if (utterance.value) {
    utterance.value.rate = rate
  }
  
  // 実際の長さが分かっている場合は、速度変更によりdurationを調整
  if (actualDuration.value > 0 && !isPlaying.value) {
    // 実際の長さをベースに速度調整
    duration.value = actualDuration.value * (1.0 / rate)
    console.log('⏱️ 速度変更:', oldRate, 'x →', rate, 'x、新しい時間:', duration.value, '秒')
  } else if (audioText.value && !isPlaying.value) {
    // まだ実際の長さが分からない場合は推定値で調整
    actualDuration.value = 0 // リセットして再測定させる
    console.log('⏱️ 速度変更のため実際の長さを再測定します')
  }
}

const handleProgressClick = async (event: MouseEvent) => {
  if (!progressBar.value || duration.value === 0) return
  
  const rect = progressBar.value.getBoundingClientRect()
  const clickX = event.clientX - rect.left
  const width = rect.width
  const percentage = Math.max(0, Math.min(100, (clickX / width) * 100))
  
  const targetTime = (percentage / 100) * duration.value
  await seekToTime(targetTime)
}

const formatTime = (seconds: number): string => {
  const mins = Math.floor(seconds / 60)
  const secs = Math.floor(seconds % 60)
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const testSpeech = () => {
  console.log('🧪 音声テスト開始')
  
  if (!('speechSynthesis' in window)) {
    console.error('❌ 音声合成が利用できません')
    errorMessage.value = '音声合成機能が利用できません'
    return
  }
  
  const synthesis = window.speechSynthesis
  synthesis.cancel()
  
  const testText = 'テスト音声です。'
  console.log('🎤 テスト音声:', testText)
  
  const testUtterance = new SpeechSynthesisUtterance(testText)
  testUtterance.volume = 0.8
  testUtterance.rate = 1.0
  testUtterance.pitch = 1.0
  testUtterance.lang = 'ja-JP'
  
  testUtterance.onstart = () => {
    console.log('✅ テスト音声再生開始')
    errorMessage.value = ''
  }
  
  testUtterance.onend = () => {
    console.log('✅ テスト音声再生終了')
  }
  
  testUtterance.onerror = (event) => {
    console.error('❌ テスト音声エラー:', event.error)
    errorMessage.value = `テスト音声エラー: ${event.error}`
  }
  
  console.log('🎵 テスト音声再生実行...')
  synthesis.speak(testUtterance)
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
  } else if (newVal && props.audioGuide) {
    // プレーヤーが開かれた時にテキストを準備してdurationを設定
    const text = generateSimpleAudioContent()
    audioText.value = text
    audioSegments.value = splitTextIntoSegments(text)
    
    // 実際の長さが既知の場合は使用、そうでなければ推定
    if (actualDuration.value > 0) {
      duration.value = actualDuration.value
    } else {
      duration.value = Math.max(12, Math.round(text.length * 0.19))
    }
    currentTime.value = 0
  }
})

watch(() => props.audioGuide, () => {
  if (isPlaying.value || isLoading.value) {
    stopAudio()
  }
  errorMessage.value = ''
  
  // 新しいオーディオガイドの時はリセット
  if (props.audioGuide) {
    const text = generateSimpleAudioContent()
    audioText.value = text
    audioSegments.value = splitTextIntoSegments(text)
    
    // 新しい観光地なので実際の長さをリセット
    actualDuration.value = 0
    
    // 初期推定値を設定（実測データに基づく修正済み計算式）
    const estimatedDuration = Math.max(12, Math.round(text.length * 0.19))
    duration.value = estimatedDuration
    currentTime.value = 0
    
    console.log('🔄 新しい観光地:', props.spotName, '推定時間:', estimatedDuration, '秒')
  }
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
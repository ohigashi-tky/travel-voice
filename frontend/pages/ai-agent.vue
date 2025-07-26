<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative flex flex-col transition-colors duration-300">

    <!-- Main Content -->
    <main 
      ref="chatContainer"
      class="flex-1 relative z-10 flex flex-col pb-0 overflow-y-auto"
      style="scroll-behavior: smooth;"
    >
      <!-- Chat Container - Full Height -->
      <div class="flex-1 bg-white dark:bg-gray-800 flex flex-col">
        
        <!-- Chat Messages -->
        <div 
          class="flex-1 p-4 space-y-4"
        >
          <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full">
            <Bot class="w-16 h-16 text-gray-300 mb-4" />
            <p class="text-gray-500 dark:text-gray-400 mb-6">観光について何でもお聞きください！</p>
            
            <!-- Sample Questions in Chat Area -->
            <div class="w-full max-w-md space-y-2">
              <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-3">よくある質問</p>
              <div class="space-y-2">
                <button
                  v-for="sample in sampleQuestions"
                  :key="sample"
                  @click="askSampleQuestion(sample)"
                  :disabled="isLoading"
                  class="w-full text-left p-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-colors text-sm text-gray-700 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ sample }}
                </button>
              </div>
            </div>
            
            <!-- スクロールテスト用のスペーサー -->
            <div class="h-96"></div>
          </div>
          
          <div 
            v-for="(message, index) in messages" 
            :key="index"
            class="flex gap-3"
            :class="message.role === 'user' ? 'justify-end' : 'justify-start'"
            :data-user-message="message.role === 'user' ? 'true' : undefined"
          >
            <!-- AI Avatar -->
            <div 
              v-if="message.role === 'assistant' || message.role === 'questions'"
              class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0"
            >
              <Bot class="w-4 h-4 text-white" />
            </div>
            
            <!-- Message Bubble -->
            <div 
              v-if="message.role !== 'questions'"
              :class="[
                message.role === 'user' 
                  ? 'max-w-xs md:max-w-md p-3 rounded-lg bg-blue-600 text-white'
                  : 'max-w-sm md:max-w-lg lg:max-w-2xl p-4 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white'
              ]"
            >
              <div 
                v-if="message.role === 'assistant'"
                class="text-sm max-w-none"
                v-html="formatMessage(message.content, message.audioGuideSpots)"
              ></div>
              <p 
                v-else
                class="text-sm whitespace-pre-wrap"
              >{{ message.content }}</p>
            </div>

            <!-- Related Questions Buttons -->
            <div 
              v-if="message.role === 'questions' && message.questions && message.questions.length > 0"
              class="max-w-sm md:max-w-lg lg:max-w-2xl space-y-2 mb-8"
            >
              <button
                v-for="(question, qIndex) in message.questions"
                :key="qIndex"
                @click="askRelatedQuestion(question)"
                class="w-full text-left bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-blue-900/40 dark:hover:to-indigo-900/40 border border-blue-200 dark:border-blue-700 rounded-lg px-4 py-3 text-sm text-blue-700 dark:text-blue-300 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md"
              >
                <span class="flex items-center gap-2">
                  <span class="text-blue-500">💭</span>
                  <span>{{ question }}</span>
                </span>
              </button>
              <div :class="isLastQuestionsMessage(index) ? 'h-6' : 'h-24'"></div>
            </div>
            
            <!-- User Avatar -->
            <div 
              v-if="message.role === 'user'"
              class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0"
            >
              <User class="w-4 h-4 text-gray-600 dark:text-gray-300" />
            </div>
          </div>
          
          <!-- Loading Message -->
          <div v-if="isLoading" class="flex gap-3 justify-start">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
              <Bot class="w-4 h-4 text-white" />
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
              <div class="flex space-x-1">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
              </div>
            </div>
          </div>
          
          <!-- Loading時のスペーサー（適切なスクロールのため） -->
          <div v-if="isLoading" :style="{ height: dynamicSpacerHeight + 'px' }"></div>
        </div>
        
      </div>
    </main>
    
    <!-- Fixed Input Area -->
    <div class="fixed left-0 right-0 bottom-14 z-30 flex justify-center pointer-events-none transition-all duration-500">
      <div :class="[
        'bg-white/70 dark:bg-gray-800/70 backdrop-blur-md rounded-lg shadow-lg mx-auto my-2 border border-gray-200 dark:border-gray-700 p-1 w-full pointer-events-auto transition-all duration-500 ease-in-out',
        isActive ? 'max-w-sm' : 'max-w-[150px]'
      ]">
        <form @submit.prevent="sendMessage" class="flex gap-2" style="background:transparent;">
          <input
            v-model="userInput"
            type="text"
            placeholder="質問してください..."
            @focus="handleFocus"
            @blur="handleBlur"
            :class="[
              'flex-1 rounded-lg bg-transparent outline-none focus:outline-none transition-all duration-500 ease-in-out',
              isActive ? 'px-2 py-1 text-base' : 'px-1 py-0.5 text-sm'
            ]"
            :disabled="isLoading"
          />
          <button
            v-if="isActive || userInput"
            type="submit"
            :disabled="!userInput.trim() || isLoading"
            :class="[
              'rounded-full bg-blue-600 shadow flex items-center justify-center transition-transform duration-150 hover:scale-105 disabled:opacity-50',
              isActive ? 'h-8 min-w-[36px] px-4' : 'h-7 min-w-[28px] px-2'
            ]"
          >
            <Send :class="isActive ? 'w-4 h-4 text-white' : 'w-3 h-3 text-white'" />
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick, onMounted, computed, watch, onUnmounted } from 'vue'
import { ArrowLeft, Bot, User, Send } from 'lucide-vue-next'
import { useTouristSpots } from '~/composables/useTouristSpots'
import { useAiChatStore } from '~/stores/aiChat'
// marked import removed - using custom markdown parser

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'AIエージェント - おうち旅行'
})

// Reactive variables
const userInput = ref('')
const chatContainer = ref<HTMLElement>()
const isActive = ref(false)

// Store
const aiChatStore = useAiChatStore()

// 動的スペーサー高さ（チャットコンテナの高さに合わせて調整）
const dynamicSpacerHeight = computed(() => {
  if (typeof window === 'undefined') return 600 // SSR時のデフォルト
  
  // チャットコンテナの高さを基準にする
  if (chatContainer.value) {
    const containerHeight = chatContainer.value.clientHeight
    // チャットコンテナの90%の高さをスペーサーにする
    return Math.max(500, containerHeight * 0.9)
  }
  
  // フォールバック: ビューポートベースの計算
  const viewportHeight = window.innerHeight
  const headerHeight = 64
  const inputHeight = 80
  
  // 利用可能なチャット表示領域の高さ
  const availableHeight = viewportHeight - headerHeight - inputHeight
  
  // スペーサー高さはチャット領域の90%〜最大800px
  return Math.min(800, Math.max(500, availableHeight * 0.9))
})

// Store経由でmessagesとisLoadingにアクセス
const messages = computed(() => aiChatStore.messages)
const isLoading = computed(() => aiChatStore.isLoading)

const sampleQuestions = [
  '東京で桜の名所を教えて',
  '大阪のおすすめグルメスポットは？',
  '京都の寺院巡りのコースを教えて',
  '北海道の冬の観光スポットは？',
  '家族連れにおすすめの観光地は？',
  '一人旅におすすめの場所を教えて'
]


// Tourist spots composable
const { spots: allSpots, fetchSpots } = useTouristSpots()

// 動的に観光地データを取得してキーワードマッピングを生成
const audioGuideSpots = ref<Array<{id: number, name: string, keywords: string[]}>>([])

// 観光地データを初期化する関数
const initializeAudioGuideSpots = async () => {
  try {
    await fetchSpots()
    // 観光地データからキーワードマッピングを生成
    audioGuideSpots.value = allSpots.value.map(spot => ({
      id: spot.id,
      name: spot.name,
      keywords: generateKeywords(spot.name)
    }))
  } catch (error) {
    console.error('Failed to initialize audio guide spots:', error)
    audioGuideSpots.value = []
  }
}

// 観光地名からキーワードを生成する関数
const generateKeywords = (spotName: string): string[] => {
  const keywords = [spotName] // 基本は観光地名そのもの
  
  // 一般的な略称や別名を追加
  const aliasMap: Record<string, string[]> = {
    '東京スカイツリー': ['スカイツリー'],
    '浅草寺': ['浅草'],
    '金閣寺': ['鹿苑寺'],
    '伏見稲荷大社': ['伏見稲荷', '千本鳥居'],
    '札幌時計台': ['時計台'],
    'トヨタ産業技術記念館': ['トヨタ記念館'],
    '太宰府天満宮': ['太宰府'],
    '福岡城跡': ['福岡城'],
    '博多駅': ['博多'],
    '原爆ドーム': ['平和記念公園'],
    '厳島神社': ['宮島'],
    '広島城': ['鯉城']
  }
  
  if (aliasMap[spotName]) {
    keywords.push(...aliasMap[spotName])
  }
  
  return keywords
}

// AI回答から音声ガイド対応観光地を検出する関数
const detectAudioGuideSpots = (content: string) => {
  const detectedSpots = []
  
  // より具体的なキーワードを優先するため、キーワードの長さでソート
  const sortedSpots = audioGuideSpots.value.map(spot => ({
    ...spot,
    keywords: spot.keywords.sort((a, b) => b.length - a.length) // 長いキーワードを優先
  }))
  
  for (const spot of sortedSpots) {
    for (const keyword of spot.keywords) {
      if (content.includes(keyword)) {
        // 重複を避ける
        if (!detectedSpots.find(s => s.id === spot.id)) {
          detectedSpots.push({ id: spot.id, name: spot.name, matchedKeyword: keyword })
        }
        break
      }
    }
  }
  
  return detectedSpots
}

// シンプルなマークダウンフォーマット関数（音声ガイドボタン挿入機能付き）
const formatMessage = (content: string, detectedSpots?: Array<{id: number, name: string, matchedKeyword?: string}>) => {
  let html = content
  
  // 関連質問セクションを削除（独立したメッセージとして表示するため）
  if (html.includes('🤔 関連質問') || html.includes('🤔関連質問')) {
    // 見出し形式と通常形式の両方に対応
    const relatedPattern = /###?\s*🤔\s*関連質問[\s\S]*/
    html = html.replace(relatedPattern, '').trim()
  }
  
  // エスケープ処理
  html = html.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
  
  // 音声ガイド対応観光地の後にボタンを挿入（重複を避ける）
  const processedSpots = new Set()
  if (detectedSpots && detectedSpots.length > 0) {
    detectedSpots.forEach(detectedSpot => {
      // 重複を避ける
      if (processedSpots.has(detectedSpot.id)) return
      
      // 検出時にマッチしたキーワードを使用、なければマスターデータから検索
      let keyword = detectedSpot.matchedKeyword
      if (!keyword) {
        const masterSpot = audioGuideSpots.value.find((s: any) => s.id === detectedSpot.id)
        if (masterSpot) {
          keyword = masterSpot.keywords.find((k: string) => html.includes(k))
        }
      }
      
      if (keyword && html.includes(keyword)) {
        // キーワードの後に改行がある場合、そこにボタンを挿入（1回のみ）
        const pattern = new RegExp(`(${keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}[^\\n]*)(\\n)`)
        html = html.replace(pattern, `$1$2<div class="mt-1 mb-3"><button onclick="navigateToSpot(${detectedSpot.id})" class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"><span>📍</span><span>${detectedSpot.name}を詳しく知る</span><span>→</span></button></div>`)
        processedSpots.add(detectedSpot.id)
      }
    })
  }
  
  // マークダウンパターンをHTMLに変換
  // ## タイトル
  html = html.replace(/^## (.+)$/gm, '<h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 mt-4 first:mt-0">$1</h2>')
  
  // ### 見出し
  html = html.replace(/^### (.+)$/gm, '<h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-2 mt-3">$1</h3>')
  
  // **太字**
  html = html.replace(/\*\*(.+?)\*\*/g, '<strong class="font-semibold text-gray-900 dark:text-white">$1</strong>')
  
  // 通常の箇条書き
  html = html.replace(/^- (.+)$/gm, '<li class="leading-relaxed ml-4">• $1</li>')
  
  // 複数の改行を段落に変換
  html = html.replace(/\n\n/g, '</p><p class="mb-2 leading-relaxed">')
  
  // 最初と最後の段落タグを追加
  html = '<p class="mb-2 leading-relaxed">' + html + '</p>'
  
  // 空の段落を削除
  html = html.replace(/<p class="mb-2 leading-relaxed"><\/p>/g, '')
  
  // 単一の改行をbrタグに変換
  html = html.replace(/\n/g, '<br>')
  
  return html
}

// ユーザー質問をチャット上部に表示するスクロール関数
const scrollToUserQuestion = async () => {
  await nextTick() // DOM更新を待つ
  
  // ページ全体をスクロールする（最もシンプルで確実な方法）
  const userMessages = document.querySelectorAll('[data-user-message="true"]')
  
  if (userMessages.length > 0) {
    const lastUserMessage = userMessages[userMessages.length - 1] as HTMLElement
    
    // 要素の位置を取得
    const rect = lastUserMessage.getBoundingClientRect()
    const targetPosition = window.pageYOffset + rect.top - 20 // 20pxの余白
    
    // スムーズにスクロール
    window.scrollTo({
      top: targetPosition,
      behavior: 'smooth'
    })
  }
}

// 最後の関連質問メッセージかどうかを判定
const isLastQuestionsMessage = (index: number) => {
  // 現在のメッセージが最後の関連質問で、かつ次にユーザーメッセージがある場合は余白を小さくする
  const currentMessage = messages.value[index]
  const nextMessage = messages.value[index + 1]
  
  return currentMessage?.role === 'questions' && nextMessage?.role === 'user'
}

const askSampleQuestion = (question: string) => {
  userInput.value = question
  sendMessage()
}

// 関連質問クリック処理のグローバル関数
const askRelatedQuestion = (question: string) => {
  userInput.value = question
  sendMessage()
}

const sendMessage = async () => {
  if (!userInput.value.trim() || aiChatStore.isLoading) return

  const userMessage = userInput.value.trim()
  userInput.value = ''

  await nextTick()

  // ユーザーメッセージ送信直後のスクロール準備
  await nextTick()
  await nextTick()
  
  setTimeout(async () => {
    await scrollToUserQuestion()
  }, 100)

  try {
    // ストア経由でメッセージ送信
    const result = await aiChatStore.sendMessage(userMessage)
    
    if (result.success && result.content) {
      // AI回答から音声ガイド対応観光地を検出
      const detectedSpots = detectAudioGuideSpots(result.content)
      
      // AI回答を追加（音声ガイドスポット情報付き）
      aiChatStore.addAssistantMessage(result.content, detectedSpots)

      // 関連質問を抽出して独立したメッセージとして追加
      const relatedQuestions = extractRelatedQuestions(result.content)
      
      // 関連質問が見つからない場合はより具体的な質問を使用
      const fallbackQuestions = [
        'この場所の詳しい行き方を教えて',
        '予算はどのくらい必要ですか',
        '他におすすめの時期はありますか',
        '混雑を避けるコツはありますか',
        '近くの観光スポットも教えて',
        '地元ならではの楽しみ方は？'
      ]
      
      // ランダムに3つ選択してバリエーションを増やす
      const randomQuestions = fallbackQuestions
        .sort(() => Math.random() - 0.5)
        .slice(0, 3)
      
      const finalQuestions = relatedQuestions.length > 0 ? relatedQuestions : randomQuestions
      
      // 関連質問を追加
      aiChatStore.addQuestionsMessage(finalQuestions)
    }
    // エラーの場合はストア側で既にエラーメッセージが追加されている

  } catch (error) {
    // 予期しないエラーの処理（ストア側でも処理されるが念のため）
    console.error('Unexpected error in sendMessage:', error)
  }
}

// 関連質問を抽出する関数
const extractRelatedQuestions = (content: string) => {
  const questions = []
  
  // より確実な正規表現でのマッチング
  const relatedQuestionsPattern = /###?\s*🤔\s*関連質問([\s\S]*?)$/i
  const match = content.match(relatedQuestionsPattern)
  
  if (match && match[1]) {
    const questionsText = match[1].trim()
    
    // 各行を処理
    const lines = questionsText.split('\n')
    for (const line of lines) {
      const trimmedLine = line.trim()
      if (trimmedLine.startsWith('- ')) {
        const question = trimmedLine.replace('- ', '').trim()
        if (question && question.length > 0) {
          questions.push(question)
        }
      }
    }
  } else {
    // フォールバック: 行ごとの処理
    const lines = content.split('\n')
    let inRelatedSection = false
    
    for (let i = 0; i < lines.length; i++) {
      const line = lines[i].trim()
      
      if (line.includes('🤔') && line.includes('関連質問')) {
        inRelatedSection = true
        continue
      }
      
      if (inRelatedSection && line.startsWith('- ')) {
        const question = line.replace('- ', '').trim()
        if (question) {
          questions.push(question)
        }
      }
    }
  }
  
  return questions
}

// 観光地詳細ページへの遷移関数
const navigateToSpot = (spotId: number) => {
  navigateTo(`/spots/${spotId}`)
}

// 会話履歴をクリア（必要に応じて使用）
const clearConversation = () => {
  aiChatStore.clearMessages()
}

function handleFocus() {
  isActive.value = true
}
function handleBlur() {
  if (!userInput.value) isActive.value = false
}
watch(userInput, (val) => {
  if (val) isActive.value = true
  else isActive.value = false
})


// グローバルウィンドウに関数を追加
onMounted(async () => {
  // 観光地データを初期化
  await initializeAudioGuideSpots()
  
  // ストアから会話履歴を復元
  aiChatStore.loadFromStorage()
  
  // グローバル関数として観光地詳細への遷移を設定
  if (typeof window !== 'undefined') {
    (window as any).navigateToSpot = navigateToSpot
    
    // ウィンドウリサイズ時に動的スペーサー高さを再計算
    const handleResize = () => {
      // computedプロパティは自動的に再計算される
    }
    
    window.addEventListener('resize', handleResize)
    
    // クリーンアップ
    onUnmounted(() => {
      window.removeEventListener('resize', handleResize)
    })
  }
  
  await nextTick()
})
</script>

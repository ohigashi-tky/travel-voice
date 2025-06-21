<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Main Content -->
    <main class="flex-1 relative z-10 flex flex-col pb-32 pt-16">
      <!-- Chat Container - Full Height -->
      <div class="flex-1 bg-white dark:bg-gray-800 flex flex-col">
        
        <!-- Chat Messages -->
        <div 
          ref="chatContainer"
          class="flex-1 overflow-y-auto p-4 space-y-4"
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
          </div>
          
          <div 
            v-for="(message, index) in messages" 
            :key="index"
            class="flex gap-3"
            :class="message.role === 'user' ? 'justify-end' : 'justify-start'"
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
                v-html="formatMessage(message.content)"
              ></div>
              <p 
                v-else
                class="text-sm whitespace-pre-wrap"
              >{{ message.content }}</p>
            </div>

            <!-- Related Questions Buttons -->
            <div 
              v-if="message.role === 'questions'"
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
          <div v-if="isLoading" class="h-96"></div>
        </div>
        
      </div>
    </main>
    
    <!-- Fixed Input Area -->
    <div class="fixed bottom-20 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 z-30">
      <form @submit.prevent="sendMessage" class="flex gap-2 max-w-4xl mx-auto">
        <input
          v-model="userInput"
          type="text"
          placeholder="観光について質問してください..."
          class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          :disabled="isLoading"
        />
        <button
          type="submit"
          :disabled="!userInput.trim() || isLoading"
          class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg transition-colors flex items-center gap-2"
        >
          <Send class="w-4 h-4" />
          送信
        </button>
      </form>
    </div>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick, onMounted } from 'vue'
import { ArrowLeft, Bot, User, Send } from 'lucide-vue-next'
// marked import removed - using custom markdown parser
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'AIエージェント - Travel Voice'
})

// Reactive variables
const activeTab = ref('ai')
const userInput = ref('')
const isLoading = ref(false)
const chatContainer = ref<HTMLElement>()

interface Message {
  role: 'user' | 'assistant' | 'questions'
  content: string
  questions?: string[]
}

const messages = ref<Message[]>([])

const sampleQuestions = [
  '東京で桜の名所を教えて',
  '大阪のおすすめグルメスポットは？',
  '京都の寺院巡りのコースを教えて',
  '北海道の冬の観光スポットは？',
  '家族連れにおすすめの観光地は？',
  '一人旅におすすめの場所を教えて'
]

const goHome = () => {
  navigateTo('/')
}

// シンプルなマークダウンフォーマット関数
const formatMessage = (content: string) => {
  let html = content
  
  // 関連質問セクションを削除（独立したメッセージとして表示するため）
  if (html.includes('🤔 関連質問') || html.includes('🤔関連質問')) {
    // 見出し形式と通常形式の両方に対応
    const relatedPattern = /###?\s*🤔\s*関連質問[\s\S]*/
    html = html.replace(relatedPattern, '').trim()
  }
  
  // エスケープ処理
  html = html.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
  
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

const scrollToBottom = async () => {
  await nextTick()
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
}

const scrollToLastUserMessage = async () => {
  await nextTick()
  if (chatContainer.value) {
    // チャット領域の高さを計算
    const containerHeight = chatContainer.value.clientHeight
    // 入力欄のスペース（m-5 = 20px）を考慮
    const reservedSpace = 20
    // 目標位置を計算（最新のユーザーメッセージが見える位置 + m-5のマージン）
    const targetScrollTop = chatContainer.value.scrollHeight - containerHeight + reservedSpace
    chatContainer.value.scrollTop = Math.max(0, targetScrollTop)
  }
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
  if (!userInput.value.trim() || isLoading.value) return

  const userMessage = userInput.value.trim()
  userInput.value = ''

  // Add user message
  messages.value.push({
    role: 'user',
    content: userMessage
  })

  await scrollToLastUserMessage()
  isLoading.value = true

  try {
    // Call Real Chat API
    const response = await $fetch('/api/chat', {
      method: 'POST',
      body: {
        message: userMessage,
        conversation: messages.value
      }
    })

    // Add AI response
    if (response.content) {
      console.log('AI Response content:', response.content)
      
      // AI回答を追加
      messages.value.push({
        role: 'assistant',
        content: response.content
      })

      // 関連質問を抽出して独立したメッセージとして追加
      const relatedQuestions = extractRelatedQuestions(response.content)
      console.log('Extracted related questions:', relatedQuestions)
      
      // テスト用：関連質問が見つからない場合は固定の質問を追加
      if (relatedQuestions.length > 0) {
        const questionsMessage = {
          role: 'questions',
          content: '',
          questions: relatedQuestions
        }
        messages.value.push(questionsMessage)
        console.log('Added questions message to chat:', questionsMessage)
      } else {
        console.log('No related questions found, adding test questions')
        // テスト用の関連質問
        const testQuestionsMessage = {
          role: 'questions',
          content: '',
          questions: [
            'この観光地の営業時間は？',
            'アクセス方法を教えて',
            '近くのおすすめグルメは？'
          ]
        }
        messages.value.push(testQuestionsMessage)
        console.log('Added test questions message to chat:', testQuestionsMessage)
      }
      
    } else if (response.error) {
      messages.value.push({
        role: 'assistant',
        content: `エラー: ${response.error}${response.details ? ` (${response.details})` : ''}`
      })
    }

  } catch (error) {
    console.error('AI Chat Error:', error)
    messages.value.push({
      role: 'assistant',
      content: '申し訳ございません。現在AIサービスに接続できません。しばらく経ってから再度お試しください。'
    })
  } finally {
    isLoading.value = false
    await scrollToBottom()
  }
}

// 関連質問を抽出する関数
const extractRelatedQuestions = (content: string) => {
  console.log('Extracting from content:', content)
  const questions = []
  
  // より確実な正規表現でのマッチング
  const relatedQuestionsPattern = /###?\s*🤔\s*関連質問([\s\S]*?)$/i
  const match = content.match(relatedQuestionsPattern)
  
  if (match && match[1]) {
    console.log('Found related questions section with regex')
    const questionsText = match[1].trim()
    console.log('Questions text:', questionsText)
    
    // 各行を処理
    const lines = questionsText.split('\n')
    for (const line of lines) {
      const trimmedLine = line.trim()
      if (trimmedLine.startsWith('- ')) {
        const question = trimmedLine.replace('- ', '').trim()
        if (question && question.length > 0) {
          questions.push(question)
          console.log('Added question:', question)
        }
      }
    }
  } else {
    console.log('No related questions section found with regex, trying line-by-line')
    
    // フォールバック: 行ごとの処理
    const lines = content.split('\n')
    let inRelatedSection = false
    
    for (let i = 0; i < lines.length; i++) {
      const line = lines[i].trim()
      
      if (line.includes('🤔') && line.includes('関連質問')) {
        inRelatedSection = true
        console.log('Found related questions section at line:', i)
        continue
      }
      
      if (inRelatedSection && line.startsWith('- ')) {
        const question = line.replace('- ', '').trim()
        if (question) {
          questions.push(question)
          console.log('Added question:', question)
        }
      }
    }
  }
  
  console.log('Final questions array:', questions)
  return questions
}
</script>
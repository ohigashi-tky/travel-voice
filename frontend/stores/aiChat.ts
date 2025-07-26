import { defineStore } from 'pinia'

interface ChatMessage {
  role: 'user' | 'assistant' | 'questions'
  content: string
  questions?: string[]
  audioGuideSpots?: Array<{id: number, name: string}>
}

interface AiChatState {
  messages: ChatMessage[]
  isLoading: boolean
}

export const useAiChatStore = defineStore('aiChat', {
  state: (): AiChatState => ({
    messages: [],
    isLoading: false
  }),

  getters: {
    // APIに送信用の会話履歴（questionsタイプを除外）
    conversationForAPI(): Array<{role: string, content: string}> {
      return this.messages
        .filter(msg => msg.role === 'user' || msg.role === 'assistant')
        .map(msg => ({
          role: msg.role,
          content: msg.content
        }))
    },

    // 最新のメッセージ
    latestMessage(): ChatMessage | null {
      return this.messages.length > 0 ? this.messages[this.messages.length - 1] : null
    },

    // 会話履歴があるかどうか
    hasMessages(): boolean {
      return this.messages.length > 0
    }
  },

  actions: {
    // メッセージを追加
    addMessage(message: ChatMessage) {
      this.messages.push(message)
      this.saveToStorage()
    },

    // ユーザーメッセージを追加
    addUserMessage(content: string) {
      this.addMessage({
        role: 'user',
        content
      })
    },

    // アシスタントメッセージを追加
    addAssistantMessage(content: string, audioGuideSpots?: Array<{id: number, name: string}>) {
      this.addMessage({
        role: 'assistant',
        content,
        audioGuideSpots
      })
    },

    // 関連質問メッセージを追加
    addQuestionsMessage(questions: string[]) {
      this.addMessage({
        role: 'questions',
        content: '',
        questions
      })
    },

    // AIに質問を送信
    async sendMessage(userMessage: string) {
      // ユーザーメッセージを追加
      this.addUserMessage(userMessage)
      
      this.isLoading = true
      
      try {
        // APIに送信
        const response = await $fetch('/api/chat', {
          method: 'POST',
          body: {
            message: userMessage,
            conversation: this.conversationForAPI
          }
        })

        // AI応答を処理
        if (typeof response === 'object' && response !== null && 'content' in response && response.content) {
          return {
            success: true,
            content: response.content as string
          }
        } else if (typeof response === 'object' && response !== null && 'error' in response && response.error) {
          const errorMessage = `エラー: ${response.error}${'details' in response && response.details ? ` (${response.details})` : ''}`
          this.addAssistantMessage(errorMessage)
          return {
            success: false,
            error: errorMessage
          }
        }
      } catch (error) {
        const errorMessage = '申し訳ございません。現在AIサービスに接続できません。しばらく経ってから再度お試しください。'
        this.addAssistantMessage(errorMessage)
        return {
          success: false,
          error: errorMessage
        }
      } finally {
        this.isLoading = false
      }
    },

    // 会話履歴をクリア
    clearMessages() {
      this.messages = []
      this.clearStorage()
    },

    // ローカルストレージに保存
    saveToStorage() {
      if (typeof window !== 'undefined') {
        try {
          localStorage.setItem('ai-agent-conversation', JSON.stringify(this.messages))
        } catch (error) {
          console.warn('Failed to save conversation to localStorage:', error)
        }
      }
    },

    // ローカルストレージから復元
    loadFromStorage() {
      if (typeof window !== 'undefined') {
        try {
          const saved = localStorage.getItem('ai-agent-conversation')
          if (saved) {
            const parsed = JSON.parse(saved)
            if (Array.isArray(parsed)) {
              this.messages = parsed
            }
          }
        } catch (error) {
          console.warn('Failed to load conversation from localStorage:', error)
          this.messages = []
        }
      }
    },

    // ローカルストレージをクリア
    clearStorage() {
      if (typeof window !== 'undefined') {
        localStorage.removeItem('ai-agent-conversation')
      }
    }
  }
})
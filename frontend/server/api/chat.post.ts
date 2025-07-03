import { createError, defineEventHandler, readBody } from 'h3'

export default defineEventHandler(async (event) => {
  setHeader(event, 'Content-Type', 'application/json')
  
  try {
    console.log('Chat API called')
    
    const body = await readBody(event)
    console.log('Request body:', JSON.stringify(body, null, 2))
    
    const { message, conversation } = body || {}

    if (!message) {
      console.log('No message provided')
      return {
        error: 'Message is required'
      }
    }

    console.log('Processing message:', message)

    // OpenRouter API設定
    const OPENROUTER_API_KEY = process.env.NUXT_OPENROUTER_API_KEY || process.env.OPENROUTER_API_KEY
    const OPENROUTER_API_URL = process.env.OPENROUTER_API_URL || 'https://openrouter.ai/api/v1/chat/completions'
    const MODEL = process.env.NUXT_OPENROUTER_MODEL || process.env.OPENROUTER_MODEL || 'google/gemini-2.5-flash-lite-preview-06-17'

    if (!OPENROUTER_API_KEY) {
      console.error('OPENROUTER_API_KEY is not set')
      return {
        error: 'APIキーエラー',
        details: 'OpenRouterのAPIキーが取得できません。環境変数OPENROUTER_API_KEYを設定してください。'
      }
    }

    // 会話履歴から文脈情報を抽出
    let contextInfo = ''
    if (Array.isArray(conversation) && conversation.length > 0) {
      const recentMessages = conversation.slice(-4) // 最新4メッセージを確認
      const assistantMessages = recentMessages.filter(msg => msg.role === 'assistant')
      
      if (assistantMessages.length > 0) {
        const lastAssistantMessage = assistantMessages[assistantMessages.length - 1]
        if (lastAssistantMessage && lastAssistantMessage.content) {
          // 前回の回答から観光地名、地名、施設名を抽出
          const locationMatches = lastAssistantMessage.content.match(/[ぁ-ん一-龯ー]+[城|寺|神社|公園|駅|橋|山|島|湖|川|通り|街|区|市|府|県|タワー|美術館|博物館|水族館|動物園]/g)
          const locationNames = locationMatches ? [...new Set(locationMatches)] : []
          
          if (locationNames.length > 0) {
            contextInfo = `\n【前回の回答で言及した場所】: ${locationNames.join('、')}`
            console.log('Extracted locations from previous response:', locationNames)
          }
        }
      }
    }

    // システムプロンプト
    const systemPrompt = `あなたは日本の観光情報に詳しいAIアシスタントです。以下の点を心がけて回答してください：

【重要】関連質問では「この場所」「ここ」「そこ」などの指示語は絶対に使用せず、必ず具体的な場所名・施設名を明記してください。${contextInfo}

【回答フォーマット】
- 必ずマークダウン形式で回答してください
- **700文字以内**で簡潔に回答する
- 回答の冒頭には「## 🗾 [質問に関連したタイトル]」の形式でタイトルを付ける
- 各セクションには「### 📍 見出し」の形式で見出しを付ける
- 箇条書きには「- 」を使用する
- 重要な情報は**太字**で強調する
- 回答の最後に「### 🤔 関連質問」として、**今回の回答内容に直接関連した具体的で実用的な質問を3つ**提示する
- **関連質問では「この場所」「ここ」「そこ」などの指示語は絶対に使わず、必ず具体的な場所名・施設名・料理名を明記する**

【関連質問の作り方】
- **今回の回答で言及した具体的な場所名・施設名を必ず使用する**
- **複数の場所を紹介した場合**: 「上野公園の営業時間は？」「新宿御苑へのアクセス方法は？」など、**具体的な場所名を含めた質問**にする
- **単一の場所を紹介した場合**: その場所の詳細情報（営業時間、料金、アクセス、混雑状況など）
- **グルメを紹介した場合**: 「○○で食べられるお店はどこですか？」など、**具体的な料理名や店名を含める**
- **季節の情報を含む場合**: その季節の具体的な楽しみ方や注意点
- **予算・時間に言及した場合**: より詳しい費用内訳や所要時間の質問
- **会話の流れを考慮**: 前回言及した場所がある場合は、それらの場所名を優先的に使用する
- **「この場所」「ここ」「そこ」「その場所」「アクセス方法」など曖昧な表現は絶対に使用禁止**
- **必ず「○○駅の」「△△公園の」「××寺の」など具体的な固有名詞を含める**
- 関連質問の例：❌「この場所の営業時間は？」→ ✅「清水寺の拝観時間は？」

【内容】
1. 日本の観光地、文化、歴史、グルメについて正確で詳しい情報を提供する
2. 季節に応じたおすすめや注意点を含める
3. 具体的で実用的なアドバイスを提供する
4. 日本語で自然で親しみやすい口調で回答する
5. 安全な旅行のための注意点も適切に含める
6. 予算や時間に応じた提案も行う

Travel Voiceアプリのユーザーとして、音声ガイド機能も活用してより深く観光地を楽しめることも適宜紹介してください。

【回答例】
## 🗾 東京の桜の名所ガイド

### 📍 おすすめスポット
- **上野公園**: 約1000本の桜が咲き誇る都内屈指の名所
- **新宿御苑**: 約65種類の桜が楽しめる
- **千鳥ヶ淵**: 夜桜ライトアップが美しい

### 📍 ベストシーズン
**3月下旬〜4月上旬**が見頃です。

### 🤔 関連質問
- 上野公園の花見で場所取りのコツはありますか
- 新宿御苑の入園料と開園時間を教えてください  
- 千鳥ヶ淵の夜桜ライトアップは何時までですか`

    // メッセージ履歴を準備
    const messages = [
      {
        role: 'system',
        content: systemPrompt
      }
    ]

    // 会話履歴があれば追加（最大10メッセージ）
    if (Array.isArray(conversation) && conversation.length > 0) {
      messages.push(...conversation.slice(-10))
    }

    // ユーザーメッセージを追加
    messages.push({
      role: 'user',
      content: message
    })

    console.log('Calling OpenRouter API with messages:', messages.length, 'messages')

    // OpenRouter APIを呼び出し
    const apiResponse = await fetch(OPENROUTER_API_URL, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${OPENROUTER_API_KEY}`,
        'Content-Type': 'application/json',
        'HTTP-Referer': 'https://travel-voice.app',
        'X-Title': 'Travel Voice App'
      },
      body: JSON.stringify({
        model: MODEL,
        messages: messages,
        temperature: 0.7,
        max_tokens: 1000,
        top_p: 1,
        frequency_penalty: 0,
        presence_penalty: 0
      })
    })

    console.log('OpenRouter API response status:', apiResponse.status)

    if (!apiResponse.ok) {
      const errorText = await apiResponse.text()
      console.error('OpenRouter API Error:', apiResponse.status, errorText)
      
      return {
        error: 'AI service error',
        details: `Status: ${apiResponse.status}, Response: ${errorText}`
      }
    }

    const data = await apiResponse.json()
    console.log('OpenRouter API response data:', JSON.stringify(data, null, 2))
    
    if (!data.choices || !data.choices[0] || !data.choices[0].message) {
      console.error('Invalid response format from OpenRouter:', data)
      return {
        error: 'Invalid AI response format',
        details: 'Missing choices or message in response'
      }
    }

    const aiContent = data.choices[0].message.content
    // console.log('AI response content:', aiContent)

    return {
      content: aiContent,
      model: MODEL,
      usage: data.usage || {}
    }

  } catch (error) {
    console.error('Chat API Error:', error)
    
    return {
      error: 'Internal server error',
      details: error instanceof Error ? error.message : 'Unknown error'
    }
  }
})
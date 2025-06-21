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
    const OPENROUTER_API_KEY = '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'
    const MODEL = 'google/gemini-2.5-flash-lite-preview-06-17'

    // システムプロンプト
    const systemPrompt = `あなたは日本の観光情報に詳しいAIアシスタントです。以下の点を心がけて回答してください：

【回答フォーマット】
- 必ずマークダウン形式で回答してください
- **700文字以内**で簡潔に回答する
- 回答の冒頭には「## 🗾 [質問に関連したタイトル]」の形式でタイトルを付ける
- 各セクションには「### 📍 見出し」の形式で見出しを付ける
- 箇条書きには「- 」を使用する
- 重要な情報は**太字**で強調する
- 回答の最後に「### 🤔 関連質問」として関連する質問を3つ提示する

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

### 📍 ベストシーズン
**3月下旬〜4月上旬**が見頃です。

### 🤔 関連質問
- 桜の開花予想はいつ頃発表されますか
- 夜桜が美しいスポットはありますか
- 花見の際の持ち物は何が必要ですか`

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
    const apiResponse = await fetch('https://openrouter.ai/api/v1/chat/completions', {
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
    console.log('AI response content:', aiContent)

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
export default defineEventHandler(async (event) => {
  try {
    console.log('Test chat API called')
    
    const body = await readBody(event)
    const { message } = body || {}

    console.log('Received message:', message)

    // テスト用の固定レスポンス
    return {
      content: `テストレスポンス: 「${message}」について回答します。\n\nこんにちは！Travel Voice AIです。日本の観光情報について、何でもお聞かせください。観光地の詳細、おすすめルート、季節の見どころ、グルメ情報など、お答えします！\n\nTravel Voiceアプリの音声ガイド機能も活用いただくことで、より深く観光地を楽しんでいただけます。`,
      model: 'test-model',
      usage: { total_tokens: 50 }
    }

  } catch (error) {
    console.error('Test chat API error:', error)
    return {
      error: 'Test API error',
      details: error.message
    }
  }
})
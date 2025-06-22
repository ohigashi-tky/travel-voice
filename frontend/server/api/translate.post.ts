import { createError, defineEventHandler, readBody } from 'h3'

export default defineEventHandler(async (event) => {
  setHeader(event, 'Content-Type', 'application/json')
  
  try {
    const body = await readBody(event)
    const { text, target, source = 'ja' } = body || {}

    if (!text || !target) {
      throw createError({
        statusCode: 400,
        statusMessage: 'Text and target language are required'
      })
    }

    // 基本的な翻訳マッピング（実際のプロジェクトではGoogle Translate APIなどを使用）
    const translations = {
      'ja-en': {
        '東京スカイツリー': 'Tokyo Skytree',
        '浅草寺': 'Sensoji Temple',
        '大阪城': 'Osaka Castle',
        '清水寺': 'Kiyomizu-dera Temple',
        '札幌時計台': 'Sapporo Clock Tower',
        '金閣寺': 'Kinkaku-ji Temple',
        '名古屋城': 'Nagoya Castle',
        '熱田神宮': 'Atsuta Shrine',
        'トヨタ産業技術記念館': 'Toyota Commemorative Museum of Industry and Technology',
        '太宰府天満宮': 'Dazaifu Tenmangu Shrine',
        '福岡城跡': 'Fukuoka Castle Ruins',
        '博多駅': 'Hakata Station',
        '展望台': 'Observatory',
        '寺院': 'Temple',
        '歴史建造物': 'Historic Building',
        '神社': 'Shrine',
        '博物館': 'Museum',
        '観光エリア': 'Tourist Area',
        '東京都': 'Tokyo',
        '大阪府': 'Osaka',
        '京都府': 'Kyoto',
        '北海道': 'Hokkaido',
        '愛知県': 'Aichi',
        '福岡県': 'Fukuoka',
        // 観光地の説明文
        '高さ634mの世界最高クラスの電波塔。展望デッキからは東京の絶景を一望できます。': 'A world-class TV tower with a height of 634m. You can enjoy panoramic views of Tokyo from the observation deck.',
        '東京最古の寺院。雷門と仲見世通りで有名な東京を代表する観光地です。': 'Tokyo\'s oldest temple. A representative tourist spot in Tokyo famous for Kaminarimon Gate and Nakamise Street.',
        '豊臣秀吉が築城した名城。美しい天守閣と桜の名所として親しまれています。': 'A famous castle built by Toyotomi Hideyoshi. Loved for its beautiful castle keep and cherry blossom spots.',
        '778年開創の京都最古の寺院。有名な清水の舞台と美しい景色で知られています。': 'Kyoto\'s oldest temple founded in 778. Known for the famous Kiyomizu Stage and beautiful scenery.',
        '旧札幌農学校演武場として1878年に建設された北海道のシンボル的建造物です。': 'A symbolic building of Hokkaido built in 1878 as the former Sapporo Agricultural School drill hall.',
        '足利義満の別荘として建てられた金箔で覆われた美しい楼閣。世界文化遺産です。': 'A beautiful pavilion covered in gold leaf, built as Ashikaga Yoshimitsu\'s villa. It is a World Cultural Heritage site.',
        '徳川家康が築城した名古屋のシンボル。金の鯱鉾で有名な日本三大名城の一つです。': 'A symbol of Nagoya built by Tokugawa Ieyasu. One of Japan\'s three great castles, famous for its golden shachihoko.',
        '三種の神器の一つ草薙剣を祀る由緒ある神社。1900年の歴史を誇る格式高い神宮です。': 'A prestigious shrine that enshrines Kusanagi-no-Tsurugi, one of the Three Sacred Treasures. A prestigious shrine with 1900 years of history.',
        'トヨタグループ発祥の地に建つ産業技術博物館。自動車産業の歴史と技術を学べます。': 'An industrial technology museum built at the birthplace of Toyota Group. You can learn about the history and technology of the automotive industry.',
        '学問の神様菅原道真公を祀る由緒ある神社。受験合格や学業成就を願う多くの参拝者が訪れます。': 'A prestigious shrine dedicated to Sugawara no Michizane, the god of learning. Many worshippers visit to pray for exam success and academic achievement.',
        '黒田長政が築城した福岡藩の居城跡。現在は舞鶴公園として桜の名所にもなっています。': 'The ruins of Fukuoka Domain\'s castle built by Kuroda Nagamasa. Now it is Maizuru Park and has become a famous cherry blossom spot.',
        '九州の玄関口として知られる福岡の中心駅。周辺にはグルメや買い物スポットが充実しています。': 'The central station of Fukuoka known as the gateway to Kyushu. The surrounding area is rich in gourmet and shopping spots.'
      }
    }

    // 翻訳を実行
    let translatedText = text
    
    if (target === 'en' && source === 'ja') {
      translatedText = translations['ja-en'][text] || text
    }

    // より高度な翻訳が必要な場合は、Google Translate APIやOpenAI APIを使用
    if (!translations['ja-en'][text] && target === 'en') {
      try {
        // OpenRouter APIを使用した翻訳
        const OPENROUTER_API_KEY = process.env.OPENROUTER_API_KEY
        const OPENROUTER_API_URL = process.env.OPENROUTER_API_URL || 'https://openrouter.ai/api/v1/chat/completions'
        
        if (!OPENROUTER_API_KEY) {
          console.error('OPENROUTER_API_KEY is not set for translation')
          return {
            translatedText: text,
            originalText: text,
            targetLanguage: target,
            sourceLanguage: source
          }
        }
        
        const response = await fetch(OPENROUTER_API_URL, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${OPENROUTER_API_KEY}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            model: 'google/gemini-2.5-flash-lite-preview-06-17',
            messages: [
              {
                role: 'system',
                content: 'You are a professional Japanese to English translator specializing in tourism content. Translate the given Japanese text to natural, fluent English. Maintain the original meaning and tone.'
              },
              {
                role: 'user',
                content: `Translate this Japanese text to English: "${text}"`
              }
            ],
            temperature: 0.3,
            max_tokens: 200
          })
        })

        if (response.ok) {
          const data = await response.json()
          if (data.choices && data.choices[0] && data.choices[0].message) {
            translatedText = data.choices[0].message.content.trim()
            // Remove any quotes or extra formatting
            translatedText = translatedText.replace(/^["']|["']$/g, '')
          }
        }
      } catch (aiError) {
        console.error('AI translation error:', aiError)
        // フォールバック: 基本的な英訳を試す
        translatedText = text
      }
    }

    return {
      translatedText,
      originalText: text,
      targetLanguage: target,
      sourceLanguage: source
    }

  } catch (error) {
    console.error('Translation API Error:', error)
    throw createError({
      statusCode: 500,
      statusMessage: 'Translation failed'
    })
  }
})
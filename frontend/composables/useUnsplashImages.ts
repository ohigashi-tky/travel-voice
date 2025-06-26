import { ref } from 'vue'

interface UnsplashImage {
  id: string
  urls: {
    small: string
    regular: string
    full: string
  }
  alt_description: string
  user: {
    name: string
    username: string
    profile_url: string
  }
  download_url: string
}

interface UnsplashResponse {
  images: UnsplashImage[]
  total: number
  total_pages: number
  error?: string
}

// 画像キャッシュ
const imageCache = new Map<string, UnsplashImage[]>()

export const useUnsplashImages = () => {
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  // 観光地名から適切な検索キーワードを生成
  const generateSearchQuery = (spotName: string): string => {
    const searchMap: Record<string, string> = {
      // 東京
      '東京スカイツリー': 'tokyo skytree',
      '浅草寺': 'sensoji temple tokyo',
      '明治神宮': 'meiji shrine tokyo',
      '東京タワー': 'tokyo tower',
      '銀座': 'ginza tokyo shopping',
      '渋谷': 'shibuya crossing tokyo',
      '新宿': 'shinjuku tokyo',
      '上野公園': 'ueno park tokyo',
      
      // 大阪
      '大阪城': 'osaka castle',
      '通天閣': 'tsutenkaku tower osaka',
      '海遊館': 'kaiyukan aquarium osaka',
      '道頓堀': 'dotonbori osaka',
      '心斎橋': 'shinsaibashi osaka',
      
      // 京都
      '清水寺': 'kiyomizu temple kyoto',
      '金閣寺': 'kinkaku-ji golden pavilion kyoto',
      '伏見稲荷大社': 'fushimi inari shrine kyoto',
      '嵐山': 'arashiyama bamboo kyoto',
      '二条城': 'nijo castle kyoto',
      
      // 北海道
      '札幌時計台': 'sapporo clock tower',
      '函館山': 'mount hakodate hokkaido',
      '小樽運河': 'otaru canal hokkaido',
      'すすきの': 'susukino sapporo',
      
      // 愛知
      '名古屋城': 'nagoya castle',
      '熱田神宮': 'atsuta shrine nagoya',
      'トヨタ産業技術記念館': 'toyota museum nagoya',
      
      // 福岡
      '太宰府天満宮': 'dazaifu tenmangu shrine',
      '福岡城跡': 'fukuoka castle ruins',
      '博多駅': 'hakata station fukuoka',
      
      // 広島
      '原爆ドーム': 'atomic bomb dome hiroshima',
      '厳島神社': 'itsukushima shrine miyajima',
      '広島城': 'hiroshima castle'
    }

    return searchMap[spotName] || spotName
  }

  // 画像を検索
  const searchImages = async (spotName: string, useCache = true): Promise<UnsplashImage[]> => {
    const query = generateSearchQuery(spotName)
    
    // キャッシュをチェック
    if (useCache && imageCache.has(query)) {
      return imageCache.get(query)!
    }

    isLoading.value = true
    error.value = null

    try {
      console.log(`Searching Unsplash for: "${query}"`)
      const response = await $fetch<UnsplashResponse>('/api/unsplash/search', {
        method: 'POST',
        body: {
          query,
          per_page: 5
        }
      })

      console.log('Unsplash response received:', { 
        hasError: !!response.error, 
        imageCount: response.images?.length || 0 
      })

      if (response.error) {
        throw new Error(response.error)
      }

      const images = response.images || []
      
      // キャッシュに保存
      if (images.length > 0) {
        imageCache.set(query, images)
        console.log(`Cached ${images.length} images for "${query}"`)
      }

      return images
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to fetch images'
      error.value = errorMessage
      console.error('Unsplash search error:', { query, error: errorMessage })
      return []
    } finally {
      isLoading.value = false
    }
  }

  // 高品質なフォールバック画像マップ
  const fallbackImages: Record<string, string> = {
    '東京スカイツリー': 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=800&h=600&fit=crop&auto=format&q=80',
    '浅草寺': 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&h=600&fit=crop&auto=format&q=80',
    '大阪城': 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=800&h=600&fit=crop&auto=format&q=80',
    '清水寺': 'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=800&h=600&fit=crop&auto=format&q=80',
    '金閣寺': 'https://images.unsplash.com/photo-1478436127897-769e1b3f0f36?w=800&h=600&fit=crop&auto=format&q=80',
    '厳島神社': 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=800&h=600&fit=crop&auto=format&q=80',
    '原爆ドーム': 'https://images.unsplash.com/photo-1590736969955-71cc94901144?w=800&h=600&fit=crop&auto=format&q=80',
    '広島城': 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=800&h=600&fit=crop&auto=format&q=80'
  }

  // 特定の観光地の最初の画像URLを取得
  const getSpotImageUrl = async (spotName: string, size: 'small' | 'regular' | 'full' = 'regular'): Promise<string> => {
    console.log(`Getting image URL for: ${spotName}`)
    
    try {
      const images = await searchImages(spotName)
      if (images.length > 0) {
        const imageUrl = images[0].urls[size]
        console.log(`Successfully got Unsplash image for ${spotName}:`, imageUrl)
        return imageUrl
      } else {
        console.log(`No Unsplash images found for ${spotName}, using fallback`)
      }
    } catch (err) {
      console.warn(`Unsplash API failed for ${spotName}, using fallback:`, err)
    }
    
    // フォールバック画像（高品質バージョン）
    const fallbackUrl = fallbackImages[spotName] || 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&h=600&fit=crop&auto=format&q=80'
    console.log(`Using fallback image for ${spotName}:`, fallbackUrl)
    return fallbackUrl
  }

  // 画像の適切なクレジット表記を生成
  const getImageCredit = (image: UnsplashImage): string => {
    return `Photo by ${image.user.name} on Unsplash`
  }

  // Unsplashの利用規約に従ってダウンロードを追跡
  const trackDownload = async (downloadUrl: string): Promise<void> => {
    try {
      // Unsplashのダウンロード追跡API（利用規約で必須）
      await fetch(downloadUrl)
    } catch (err) {
      console.error('Failed to track download:', err)
    }
  }

  return {
    isLoading,
    error,
    searchImages,
    getSpotImageUrl,
    getImageCredit,
    trackDownload
  }
}
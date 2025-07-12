import type { TouristSpot } from '~/types'

// ハードコーディング禁止 - 全てのデータはtravel_spotsテーブルから取得

export const useTouristSpots = () => {
  const spots = ref<TouristSpot[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // データの重複を除去するヘルパー関数
  const deduplicateSpots = (spotsList: TouristSpot[]) => {
    const seen = new Map()
    return spotsList.filter(spot => {
      // IDベースで重複判定（IDが同じなら最初のものを優先）
      const key = spot.id
      if (seen.has(key)) {
        return false
      }
      seen.set(key, true)
      return true
    })
  }

  // APIから観光スポットデータを取得
  const fetchSpots = async () => {
    loading.value = true
    error.value = null
    
    try {
      const config = useRuntimeConfig()
      let apiBaseUrl
      if (process.server) {
        apiBaseUrl = config.apiBaseServer
      } else {
        const isLocalDev = window.location.hostname === 'localhost' && window.location.port === '3000'
        apiBaseUrl = isLocalDev ? 'http://localhost:8000' : config.public.apiBase
      }
      const response = await $fetch(`${apiBaseUrl}/api/travel-spots`)
      
      // travel_spotsテーブルのデータのみ使用
      spots.value = deduplicateSpots(response.data || response)
    } catch (err) {
      error.value = 'Failed to fetch tourist spots'
      console.error('Error fetching tourist spots:', err)
      
      // フォールバック禁止: ハードコーディングデータは使用しない
      spots.value = []
    } finally {
      loading.value = false
    }
  }

  // APIから県別スポットを取得
  const fetchSpotsByPrefecture = async (prefecture: string) => {
    loading.value = true
    error.value = null
    
    try {
      const config = useRuntimeConfig()
      let apiBaseUrl
      if (process.server) {
        apiBaseUrl = config.apiBaseServer
      } else {
        const isLocalDev = window.location.hostname === 'localhost' && window.location.port === '3000'
        apiBaseUrl = isLocalDev ? 'http://localhost:8000' : config.public.apiBase
      }
      const response = await $fetch(`${apiBaseUrl}/api/travel-spots`)
      
      // 県別フィルタリング
      const filteredSpots = (response.data || response).filter((spot: any) => spot.prefecture === prefecture)
      return filteredSpots
    } catch (err) {
      error.value = 'Failed to fetch prefecture spots'
      console.error('Error fetching prefecture spots:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  // 県別でフィルタリング（ローカルデータから）
  const getSpotsByPrefecture = (prefecture: string) => {
    return spots.value.filter(spot => spot.prefecture === prefecture)
  }

  // カテゴリ別でフィルタリング
  const getSpotsByCategory = (category: string) => {
    return spots.value.filter(spot => spot.category === category)
  }

  // IDで検索
  const getSpotById = (id: number) => {
    const foundSpot = spots.value.find(spot => spot.id === id)
    return foundSpot
  }

  // ランダムに指定数取得
  const getRandomSpots = (count: number) => {
    const shuffled = [...spots.value].sort(() => 0.5 - Math.random())
    return shuffled.slice(0, count)
  }

  // AIで分析された人気スポットを取得
  const popularSpots = ref<TouristSpot[]>([])
  const popularSpotsLoading = ref(false)
  const popularSpotsError = ref<string | null>(null)

  const fetchPopularSpots = async () => {
    popularSpotsLoading.value = true
    popularSpotsError.value = null

    try {
      const config = useRuntimeConfig()
      let apiBaseUrl
      if (process.server) {
        apiBaseUrl = config.apiBaseServer
      } else {
        const isLocalDev = window.location.hostname === 'localhost' && window.location.port === '3000'
        apiBaseUrl = isLocalDev ? 'http://localhost:8000' : config.public.apiBase
      }
      
      const response = await $fetch<{
        success: boolean
        data: TouristSpot[]
        fallback?: boolean
        message?: string
      }>(`${apiBaseUrl}/api/popular-spots`)

      if (response.success) {
        // 人気スポットも重複除去
        popularSpots.value = deduplicateSpots(response.data)
        if (response.fallback) {
          console.warn('Using fallback popular spots:', response.message)
        }
      } else {
        throw new Error('Failed to fetch popular spots')
      }
    } catch (err) {
      console.error('Failed to fetch popular spots:', err)
      popularSpotsError.value = 'Failed to fetch popular spots'
      
      // Fallback to random spots (重複除去済みのデータから)
      popularSpots.value = getRandomSpots(5)
    } finally {
      popularSpotsLoading.value = false
    }
  }

  const getPopularSpots = () => {
    if (popularSpots.value.length === 0) {
      fetchPopularSpots()
    }
    return popularSpots.value
  }

  // 検索機能
  const searchSpots = (query: string) => {
    if (!query.trim()) return []
    
    const lowerQuery = query.toLowerCase()
    return spots.value.filter(spot =>
      spot.name.toLowerCase().includes(lowerQuery) ||
      spot.prefecture.toLowerCase().includes(lowerQuery) ||
      spot.category.toLowerCase().includes(lowerQuery) ||
      spot.description.toLowerCase().includes(lowerQuery)
    )
  }

  return {
    spots: readonly(spots),
    loading: readonly(loading),
    error: readonly(error),
    popularSpots: readonly(popularSpots),
    popularSpotsLoading: readonly(popularSpotsLoading),
    popularSpotsError: readonly(popularSpotsError),
    fetchSpots,
    fetchSpotsByPrefecture,
    fetchPopularSpots,
    getSpotsByPrefecture,
    getSpotsByCategory,
    getSpotById,
    getRandomSpots,
    getPopularSpots,
    searchSpots
  }
}
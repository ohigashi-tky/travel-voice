import { ref, computed } from 'vue'

export interface Prefecture {
  id: number
  name: string
  name_kana: string
  region: string
  display_order: number
  is_available: boolean
  order_in_region?: number
  featured_order?: number
  region_order?: number
  images?: Array<{
    id: number
    image_url: string
    image_type: 'icon' | 'banner' | 'thumbnail'
    display_order: number
  }>
  created_at: string
  updated_at: string
}

export interface PrefecturesByRegion {
  [region: string]: Prefecture[]
}

export const usePrefectures = () => {
  const config = useRuntimeConfig()
  const prefectures = ref<Prefecture[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // 全都道府県取得
  const fetchPrefectures = async (availableOnly = false) => {
    try {
      loading.value = true
      error.value = null

      // Railway production: use backend URL
      const apiBase = process.client && window.location.hostname !== 'localhost' ? 'https://travel-voice-production.up.railway.app' : config.public.apiBase
      const endpoint = availableOnly 
        ? `${apiBase}/api/prefectures/available`
        : `${apiBase}/api/prefectures`

      const response = await $fetch<{success: boolean, data: Prefecture[]}>(endpoint)
      
      if (response.success) {
        prefectures.value = response.data
      } else {
        throw new Error('Failed to fetch prefectures')
      }
    } catch (err) {
      console.error('Error fetching prefectures:', err)
      error.value = 'Failed to load prefectures'
    } finally {
      loading.value = false
    }
  }

  // 地域別都道府県取得
  const fetchPrefecturesByRegion = async () => {
    try {
      loading.value = true
      error.value = null

      // Railway production: use backend URL
      const apiBase = process.client && window.location.hostname !== 'localhost' ? 'https://travel-voice-production.up.railway.app' : config.public.apiBase
      const response = await $fetch<{success: boolean, data: PrefecturesByRegion}>(`${apiBase}/api/prefectures/by-region`)
      
      if (response.success) {
        return response.data
      } else {
        throw new Error('Failed to fetch prefectures by region')
      }
    } catch (err) {
      console.error('Error fetching prefectures by region:', err)
      error.value = 'Failed to load prefectures by region'
      return {}
    } finally {
      loading.value = false
    }
  }

  // 主要都道府県取得
  const fetchFeaturedPrefectures = async () => {
    try {
      loading.value = true
      error.value = null

      // Railway production: use backend URL
      const apiBase = process.client && window.location.hostname !== 'localhost' ? 'https://travel-voice-production.up.railway.app' : config.public.apiBase
      const response = await $fetch<{success: boolean, data: Prefecture[]}>(`${apiBase}/api/prefectures/featured`)
      
      if (response.success) {
        return response.data
      } else {
        throw new Error('Failed to fetch featured prefectures')
      }
    } catch (err) {
      console.error('Error fetching featured prefectures:', err)
      error.value = 'Failed to load featured prefectures'
      return []
    } finally {
      loading.value = false
    }
  }

  // 都道府県別観光地取得（名前）
  const fetchPrefectureSpots = async (prefectureName: string) => {
    try {
      // Railway production: use backend URL
      const apiBase = process.client && window.location.hostname !== 'localhost' ? 'https://travel-voice-production.up.railway.app' : config.public.apiBase
      const response = await $fetch<{success: boolean, data: {prefecture: Prefecture, spots: any[]}}>(`${apiBase}/api/prefectures/name/${prefectureName}/spots`)
      
      if (response.success) {
        return response.data
      } else {
        throw new Error('Failed to fetch prefecture spots')
      }
    } catch (err) {
      console.error('Error fetching prefecture spots:', err)
      throw err
    }
  }

  // 都道府県別観光地取得（ID）
  const fetchPrefectureSpotsById = async (prefectureId: string) => {
    try {
      // Railway production: use backend URL
      const apiBase = process.client && window.location.hostname !== 'localhost' ? 'https://travel-voice-production.up.railway.app' : config.public.apiBase
      const response = await $fetch<{success: boolean, data: {prefecture: Prefecture, spots: any[]}}>(`${apiBase}/api/prefectures/${prefectureId}/spots`)
      
      if (response.success) {
        return response.data
      } else {
        throw new Error('Failed to fetch prefecture spots')
      }
    } catch (err) {
      console.error('Error fetching prefecture spots:', err)
      throw err
    }
  }

  // 利用可能な都道府県のみ
  const availablePrefectures = computed(() => 
    prefectures.value.filter(p => p.is_available)
  )

  // 地域別グループ化（APIで取得したデータをそのまま使用）
  const prefecturesByRegion = computed(() => {
    const grouped: PrefecturesByRegion = {}
    prefectures.value.forEach(prefecture => {
      if (!grouped[prefecture.region]) {
        grouped[prefecture.region] = []
      }
      grouped[prefecture.region].push(prefecture)
    })
    
    // APIで設定されたソート順を使用
    Object.keys(grouped).forEach(region => {
      grouped[region].sort((a, b) => {
        // order_in_regionでソート
        if (a.order_in_region && b.order_in_region) {
          return a.order_in_region - b.order_in_region
        }
        // fallback to display_order
        return a.display_order - b.display_order
      })
    })
    
    return grouped
  })

  // 主要都道府県（APIから取得）
  const featuredPrefectures = ref<Prefecture[]>([])

  // 都道府県のルートパスを取得（動的ページと個別ページの併用）
  const getPrefectureRoutePath = (prefectureName: string) => {
    // 動的ページを使用する都道府県
    const dynamicRouteMap: Record<string, string> = {
      '東京都': 'prefecture/13',
      '大阪府': 'prefecture/27',
      '京都府': 'prefecture/26',
      '北海道': 'prefecture/1',
      '沖縄県': 'prefecture/47',
      '福岡県': 'prefecture/40',
      '愛知県': 'prefecture/23',
      '神奈川県': 'prefecture/14',
      '広島県': 'prefecture/34',
      '愛媛県': 'prefecture/38',
      '山口県': 'prefecture/35',
      '鹿児島県': 'prefecture/46',
      '埼玉県': 'prefecture/11',
      '千葉県': 'prefecture/12',
      '兵庫県': 'prefecture/28',
      '静岡県': 'prefecture/22',
      '福島県': 'prefecture/7',
      '新潟県': 'prefecture/15',
      '徳島県': 'prefecture/36'
    }
    
    if (dynamicRouteMap[prefectureName]) {
      return dynamicRouteMap[prefectureName]
    }
    
    // その他の都道府県（現在はすべて動的ページを使用）
    return null
  }

  return {
    prefectures,
    availablePrefectures,
    prefecturesByRegion,
    featuredPrefectures,
    loading,
    error,
    fetchPrefectures,
    fetchPrefecturesByRegion,
    fetchFeaturedPrefectures,
    fetchPrefectureSpots,
    fetchPrefectureSpotsById,
    getPrefectureRoutePath
  }
}
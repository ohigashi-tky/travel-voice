import { ref, computed } from 'vue'

interface Prefecture {
  id: number
  name: string
  featured_order?: number
  region?: string
  region_order?: number
  order_in_region?: number
}

interface RegionalPrefecture {
  region: string
  prefectures: Prefecture[]
}

interface TopPageData {
  featured_prefectures: Prefecture[]
  regional_prefectures: RegionalPrefecture[]
}

const topPageData = ref<TopPageData | null>(null)
const isLoading = ref(false)
const error = ref<string | null>(null)

// メモリキャッシュ用のタイムスタンプ
let cacheTimestamp = 0
const CACHE_DURATION = 5 * 60 * 1000 // 5分間キャッシュ

export const useTopPageData = () => {
  const config = useRuntimeConfig()

  const fetchTopPageData = async (forceRefresh = false) => {
    // キャッシュが有効で強制リフレッシュでない場合はキャッシュを返す
    const now = Date.now()
    if (!forceRefresh && topPageData.value && (now - cacheTimestamp) < CACHE_DURATION) {
      return
    }

    isLoading.value = true
    error.value = null

    try {
      const response = await $fetch<{
        success: boolean
        data: TopPageData
        error?: string
      }>(`${config.public.apiBase}/api/top-page/data`)

      if (response.success && response.data) {
        topPageData.value = response.data
        cacheTimestamp = now
      } else {
        throw new Error(response.error || 'データの取得に失敗しました')
      }
    } catch (err: any) {
      console.error('Top page data fetch error:', err)
      error.value = err.message || 'データの取得に失敗しました'
      
      // フォールバック: エラー時は従来のAPIを使用
      try {
        await fetchDataFallback()
      } catch (fallbackErr: any) {
        console.error('Fallback fetch error:', fallbackErr)
        error.value = 'データの取得に失敗しました。しばらく後でお試しください。'
      }
    } finally {
      isLoading.value = false
    }
  }

  // フォールバック用の従来API使用
  const fetchDataFallback = async () => {
    const [featuredResponse, regionalResponse] = await Promise.all([
      $fetch<{success: boolean, data: Prefecture[]}>(`${config.public.apiBase}/api/prefectures/featured`),
      $fetch<{success: boolean, data: RegionalPrefecture[]}>(`${config.public.apiBase}/api/prefectures/by-region`)
    ])

    if (featuredResponse.success && regionalResponse.success) {
      topPageData.value = {
        featured_prefectures: featuredResponse.data,
        regional_prefectures: regionalResponse.data
      }
      cacheTimestamp = Date.now()
    } else {
      throw new Error('フォールバックデータの取得に失敗しました')
    }
  }

  // キャッシュをクリア
  const clearCache = () => {
    topPageData.value = null
    cacheTimestamp = 0
  }

  // Computed values
  const featuredPrefectures = computed(() => topPageData.value?.featured_prefectures || [])
  const regionalPrefectures = computed(() => topPageData.value?.regional_prefectures || [])

  return {
    // Data
    topPageData: readonly(topPageData),
    featuredPrefectures,
    regionalPrefectures,
    
    // State
    isLoading: readonly(isLoading),
    error: readonly(error),
    
    // Actions
    fetchTopPageData,
    clearCache,
    
    // Utility
    isCached: computed(() => topPageData.value !== null && (Date.now() - cacheTimestamp) < CACHE_DURATION)
  }
}
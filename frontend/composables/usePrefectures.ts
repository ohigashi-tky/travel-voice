import { ref, computed } from 'vue'

export interface Prefecture {
  id: number
  name: string
  name_kana: string
  region: string
  display_order: number
  is_available: boolean
  emoji?: string
  route_path?: string
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

  // 都道府県絵文字マッピング（暫定的にフロントエンドで管理）
  const prefectureEmojis: Record<string, string> = {
    '北海道': '🐻',
    '青森県': '🍎',
    '岩手県': '🗻',
    '宮城県': '🌾',
    '秋田県': '🌸',
    '山形県': '🍒',
    '福島県': '🌸',
    '茨城県': '🌾',
    '栃木県': '🍓',
    '群馬県': '🗻',
    '埼玉県': '🌸',
    '千葉県': '🌊',
    '東京都': '🗼',
    '神奈川県': '⛩️',
    '新潟県': '🍚',
    '富山県': '🗻',
    '石川県': '🦀',
    '福井県': '🦖',
    '山梨県': '🍇',
    '長野県': '🗻',
    '岐阜県': '🏯',
    '静岡県': '🗻',
    '愛知県': '🏯',
    '三重県': '🦞',
    '滋賀県': '🏯',
    '京都府': '⛩️',
    '大阪府': '🏯',
    '兵庫県': '🏯',
    '奈良県': '🦌',
    '和歌山県': '🐼',
    '鳥取県': '🏜️',
    '島根県': '⛩️',
    '岡山県': '🍑',
    '広島県': '⛩️',
    '山口県': '🌊',
    '徳島県': '🌀',
    '香川県': '🍜',
    '愛媛県': '🍊',
    '高知県': '🐟',
    '福岡県': '🏯',
    '佐賀県': '🏺',
    '長崎県': '⛩️',
    '熊本県': '🐻',
    '大分県': '♨️',
    '宮崎県': '🌺',
    '鹿児島県': '🌋',
    '沖縄県': '🏖️'
  }

  // ルートパスマッピング（暫定的にフロントエンドで管理）
  const prefectureRoutePaths: Record<string, string> = {
    '東京都': 'tokyo',
    '大阪府': 'osaka',
    '京都府': 'kyoto',
    '北海道': 'hokkaido',
    '福岡県': 'fukuoka',
    '神奈川県': 'kanagawa',
    '愛知県': 'aichi',
    '埼玉県': 'saitama',
    '千葉県': 'chiba',
    '兵庫県': 'hyogo',
    '静岡県': 'shizuoka',
    '広島県': 'hiroshima',
    '福島県': 'fukushima',
    '愛媛県': 'ehime',
    '新潟県': 'niigata',
    '山口県': 'yamaguchi',
    '徳島県': 'tokushima',
    '鹿児島県': 'kagoshima'
  }

  // 全都道府県取得
  const fetchPrefectures = async (availableOnly = false) => {
    try {
      loading.value = true
      error.value = null

      const endpoint = availableOnly 
        ? `${config.public.apiBase}/api/prefectures/available`
        : `${config.public.apiBase}/api/prefectures`

      const response = await $fetch<{success: boolean, data: Prefecture[]}>(endpoint)
      
      if (response.success) {
        // 絵文字とルートパスを追加
        prefectures.value = response.data.map(prefecture => ({
          ...prefecture,
          emoji: prefectureEmojis[prefecture.name] || '🏛️',
          route_path: prefectureRoutePaths[prefecture.name] || prefecture.name.toLowerCase()
        }))
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

      const response = await $fetch<{success: boolean, data: PrefecturesByRegion}>(`${config.public.apiBase}/api/prefectures/by-region`)
      
      if (response.success) {
        // 各都道府県に絵文字とルートパスを追加
        const enrichedData: PrefecturesByRegion = {}
        for (const [region, prefectureList] of Object.entries(response.data)) {
          enrichedData[region] = prefectureList.map(prefecture => ({
            ...prefecture,
            emoji: prefectureEmojis[prefecture.name] || '🏛️',
            route_path: prefectureRoutePaths[prefecture.name] || prefecture.name.toLowerCase()
          }))
        }
        
        // 地域を地理的順序（北から南へ）でソートして返却
        const regionOrder = [
          '北海道・東北', '関東', '中部', '近畿', '中国', '四国', '九州・沖縄'
        ]
        
        const orderedData: PrefecturesByRegion = {}
        regionOrder.forEach(region => {
          if (enrichedData[region]) {
            orderedData[region] = enrichedData[region]
          }
        })
        
        return orderedData
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

  // 都道府県別観光地取得
  const fetchPrefectureSpots = async (prefectureName: string) => {
    try {
      const response = await $fetch<{success: boolean, data: {prefecture: Prefecture, spots: any[]}}>(`${config.public.apiBase}/api/prefectures/name/${prefectureName}/spots`)
      
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

  // 地域別グループ化（人口の多い順）
  const prefecturesByRegion = computed(() => {
    const grouped: PrefecturesByRegion = {}
    prefectures.value.forEach(prefecture => {
      if (!grouped[prefecture.region]) {
        grouped[prefecture.region] = []
      }
      grouped[prefecture.region].push(prefecture)
    })
    
    // 各地域内で人口順ソート
    const regionPopulationOrder: Record<string, string[]> = {
      '関東': ['東京都', '神奈川県', '埼玉県', '千葉県', '茨城県', '栃木県', '群馬県'],
      '近畿': ['大阪府', '兵庫県', '京都府', '奈良県', '和歌山県', '滋賀県', '三重県'],
      '中部': ['愛知県', '静岡県', '新潟県', '長野県', '岐阜県', '福井県', '山梨県', '石川県', '富山県'],
      '九州・沖縄': ['福岡県', '熊本県', '鹿児島県', '長崎県', '大分県', '宮崎県', '佐賀県', '沖縄県'],
      '北海道・東北': ['北海道', '宮城県', '福島県', '青森県', '岩手県', '山形県', '秋田県'],
      '中国': ['広島県', '岡山県', '山口県', '島根県', '鳥取県'],
      '四国': ['愛媛県', '香川県', '徳島県', '高知県']
    }
    
    Object.keys(grouped).forEach(region => {
      const populationOrder = regionPopulationOrder[region] || []
      grouped[region].sort((a, b) => {
        const aIndex = populationOrder.indexOf(a.name)
        const bIndex = populationOrder.indexOf(b.name)
        
        // 両方が人口順リストに含まれている場合
        if (aIndex !== -1 && bIndex !== -1) {
          return aIndex - bIndex
        }
        // 片方だけが含まれている場合、含まれているものを先に
        if (aIndex !== -1) return -1
        if (bIndex !== -1) return 1
        // 両方とも含まれていない場合、名前順
        return a.name.localeCompare(b.name)
      })
    })
    
    // 地域を地理的順序（北から南へ）でソートして返却
    const regionOrder = [
      '北海道・東北', '関東', '中部', '近畿', '中国', '四国', '九州・沖縄'
    ]
    
    const orderedGrouped: PrefecturesByRegion = {}
    regionOrder.forEach(region => {
      if (grouped[region]) {
        orderedGrouped[region] = grouped[region]
      }
    })
    
    return orderedGrouped
  })

  // 主要都道府県（指定された順番で12都道府県）
  const featuredPrefectures = computed(() => {
    const orderedNames = [
      '東京都', '大阪府', '京都府', '北海道', '福岡県', '愛知県', 
      '神奈川県', '広島県', '兵庫県', '徳島県', '山口県', '鹿児島県'
    ]
    
    const result = []
    orderedNames.forEach(name => {
      const prefecture = availablePrefectures.value.find(p => p.name === name)
      if (prefecture) {
        result.push(prefecture)
      }
    })
    
    return result
  })

  // 都道府県名でルートパス取得
  const getPrefectureRoutePath = (prefectureName: string): string => {
    return prefectureRoutePaths[prefectureName] || prefectureName.toLowerCase()
  }

  // 都道府県名で絵文字取得
  const getPrefectureEmoji = (prefectureName: string): string => {
    return prefectureEmojis[prefectureName] || '🏛️'
  }

  return {
    // データ
    prefectures: readonly(prefectures),
    loading: readonly(loading),
    error: readonly(error),
    
    // 計算プロパティ
    availablePrefectures,
    prefecturesByRegion,
    featuredPrefectures,
    
    // メソッド
    fetchPrefectures,
    fetchPrefecturesByRegion,
    fetchPrefectureSpots,
    getPrefectureRoutePath,
    getPrefectureEmoji
  }
}
export interface TouristSpot {
  id: number
  name: string
  description: string
  overview?: string
  highlights?: string[]
  history?: string
  category: string
  prefecture: string
  city?: string
  address?: string
  latitude?: number
  longitude?: number
  images?: string[]
  access_info?: string
  public_transport?: Array<{
    type: string
    route: string
    station: string
    time: string
  }>
  car_access?: Array<{
    from: string
    exit: string
    time: string
  }>
  parking_info?: string
  walking_info?: string
  website?: string
  phone?: string
  opening_hours?: string
  admission_fee?: string
  is_active?: boolean
  created_at?: string
  updated_at?: string
  // Legacy fields for backward compatibility
  imageUrl?: string
}

// 本来はAPIから取得すべきデータ
// 将来的にはLaravelのAPIエンドポイントから取得
const TOURIST_SPOTS_DATA: TouristSpot[] = [
  {
    id: 1,
    name: '東京スカイツリー',
    description: '高さ634mの世界最高クラスの電波塔。展望デッキからは東京の絶景を一望できます。',
    category: '展望台',
    prefecture: '東京都',
    imageUrl: 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=800&h=600&fit=crop&auto=format&q=80',
    overview: '東京スカイツリーは、東京都墨田区押上にある電波塔で、2012年に開業しました。高さ634mは世界第2位の高さを誇り、東京の新たなランドマークとして親しまれています。',
    highlights: ['展望デッキ（350m・450m）', 'スカイツリータウン', 'ライトアップ', 'プラネタリウム'],
    history: '2008年に着工し、2012年に完成。武蔵国の旧国名に因んで634m（ムサシ）の高さに設計されました。建設には最新の制振技術が使われ、日本の伝統的な建築技法も取り入れられています。',
    images: [
      'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600&h=400&fit=crop&auto=format'
    ]
  },
  {
    id: 2,
    name: '浅草寺',
    description: '東京最古の寺院。雷門と仲見世通りで有名な東京を代表する観光地です。',
    category: '寺院',
    prefecture: '東京都',
    imageUrl: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&h=600&fit=crop&auto=format&q=80',
    overview: '浅草寺は628年に創建された東京最古の寺院です。雷門から仲見世通りを通って本堂に至る参道は、常に多くの参拝者と観光客で賑わっています。',
    highlights: ['雷門', '仲見世通り', '本堂', '五重塔'],
    history: '推古天皇36年（628年）、隅田川で漁をしていた檜前浜成・竹成兄弟の網にかかった観音像を本尊として祀ったのが始まりとされています。江戸時代には徳川家の祈願所として栄えました。',
    images: [
      'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=600&h=400&fit=crop&auto=format'
    ]
  },
  {
    id: 101,
    name: '大阪城',
    description: '豊臣秀吉が築城した名城。美しい天守閣と桜の名所として親しまれています。',
    category: '歴史建造物',
    prefecture: '大阪府',
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format',
    overview: '大阪城は豊臣秀吉によって1583年に築城された日本の名城の一つです。現在の天守閣は1931年に再建されたもので、大阪のシンボルとして親しまれています。',
    highlights: ['天守閣', '大阪城公園', '桜の名所', '歴史博物館'],
    history: '1583年、豊臣秀吉が石山本願寺の跡地に築城を開始。当時は「三国無双」と称される壮大な城でした。江戸時代には徳川幕府の直轄地となり、明治維新後は陸軍の施設として使用されました。',
    images: [
      'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&h=400&fit=crop&auto=format'
    ]
  },
  {
    id: 201,
    name: '清水寺',
    description: '778年開創の京都最古の寺院。有名な清水の舞台と美しい景色で知られています。',
    category: '寺院',
    prefecture: '京都府',
    imageUrl: 'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=400&h=300&fit=crop&auto=format',
    overview: '清水寺は京都東山にある法相宗の寺院で、「清水の舞台」で有名です。1994年にユネスコ世界文化遺産に登録されており、年間を通じて多くの観光客が訪れます。',
    highlights: ['清水の舞台', '音羽の滝', '地主神社', '三重塔'],
    history: '延暦17年（798年）、坂上田村麻呂によって建立されました。平安時代から「清水詣」として庶民に親しまれ、江戸時代には西国三十三箇所観音霊場の第16番札所として栄えました。',
    images: [
      'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1478436127897-769e1b3f0f36?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=600&h=400&fit=crop&auto=format'
    ]
  },
  {
    id: 301,
    name: '札幌時計台',
    description: '旧札幌農学校演武場として1878年に建設された北海道のシンボル的建造物です。',
    category: '歴史建造物',
    prefecture: '北海道',
    imageUrl: 'https://images.unsplash.com/photo-1607619662634-3ac55ec0e216?w=400&h=300&fit=crop&auto=format',
    overview: '札幌時計台は正式名称を「旧札幌農学校演武場」といい、1878年に建設された北海道の代表的な観光スポットです。現在は重要文化財に指定されています。',
    highlights: ['時計台の鐘', '歴史展示', 'クラーク博士の資料', 'コンサートホール'],
    history: '明治11年（1878年）、札幌農学校（現在の北海道大学）の演武場として建設されました。時計は明治14年に設置され、以来140年以上にわたって札幌の街に時を告げ続けています。',
    images: [
      'https://images.unsplash.com/photo-1607619662634-3ac55ec0e216?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1494522855154-9297ac14b55f?w=600&h=400&fit=crop&auto=format'
    ]
  },
  {
    id: 202,
    name: '金閣寺',
    description: '足利義満の別荘として建てられた金箔で覆われた美しい楼閣。世界文化遺産です。',
    category: '寺院',
    prefecture: '京都府',
    imageUrl: 'https://images.unsplash.com/photo-1478436127897-769e1b3f0f36?w=400&h=300&fit=crop&auto=format',
    overview: '金閣寺（鹿苑寺）は室町幕府三代将軍足利義満の別荘として1397年に建てられました。金箔で覆われた三層の楼閣が池に映る美しい姿は、京都を代表する風景です。',
    highlights: ['金閣（舎利殿）', '鏡湖池', '庭園', '夕佳亭'],
    history: '応永4年（1397年）、足利義満が西園寺家の山荘を譲り受けて「北山殿」を造営。義満の死後、禅寺となり鹿苑寺と名付けられました。昭和25年に放火により焼失しましたが、昭和30年に再建されました。',
    images: [
      'https://images.unsplash.com/photo-1478436127897-769e1b3f0f36?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=600&h=400&fit=crop&auto=format'
    ]
  },
  // 愛知県
  {
    id: 401,
    name: '名古屋城',
    description: '徳川家康が築城した名古屋のシンボル。金の鯱鉾で有名な日本三大名城の一つです。',
    category: '歴史建造物',
    prefecture: '愛知県',
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format'
  },
  // 福岡県
  {
    id: 501,
    name: '太宰府天満宮',
    description: '学問の神様菅原道真公を祀る由緒ある神社。受験合格や学業成就を願う多くの参拝者が訪れます。',
    category: '神社',
    prefecture: '福岡県',
    imageUrl: 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format'
  },
  // 広島県
  {
    id: 601,
    name: '原爆ドーム',
    description: '平和の象徴として世界中に知られる広島の代表的なランドマーク。ユネスコ世界文化遺産です。',
    category: '歴史建造物',
    prefecture: '広島県',
    imageUrl: 'https://images.unsplash.com/photo-1590736969955-71cc94901144?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 602,
    name: '厳島神社',
    description: '海に浮かぶ朱色の大鳥居で有名な日本三景の一つ。満潮時の美しさは格別です。',
    category: '神社',
    prefecture: '広島県',
    imageUrl: 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format'
  },
  // 愛媛県
  {
    id: 701,
    name: '道後温泉',
    description: '日本最古の温泉の一つ。夏目漱石の小説にも登場する歴史ある温泉街です。',
    category: '温泉',
    prefecture: '愛媛県',
    imageUrl: 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&h=300&fit=crop&auto=format'
  }
  // TODO: 他の都道府県のデータも追加予定
  // 現在はLaravelバックエンドのAPIエンドポイント開発中
]

export const useTouristSpots = () => {
  const spots = ref<TouristSpot[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // APIから観光スポットデータを取得
  const fetchSpots = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await $fetch('http://localhost:8000/api/tourist-spots')
      
      // データ変換: API のデータ構造に imageUrl を追加（後方互換性のため）
      spots.value = response.map((spot: any) => ({
        ...spot,
        imageUrl: spot.images?.[0] || 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=400&h=300&fit=crop&auto=format'
      }))
    } catch (err) {
      error.value = 'Failed to fetch tourist spots'
      console.error('Error fetching tourist spots:', err)
      
      // フォールバック: エラー時はモックデータを使用
      spots.value = TOURIST_SPOTS_DATA
    } finally {
      loading.value = false
    }
  }

  // APIから県別スポットを取得
  const fetchSpotsByPrefecture = async (prefecture: string) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await $fetch(`http://localhost:8000/api/tourist-spots/prefecture/${encodeURIComponent(prefecture)}`)
      
      return response.map((spot: any) => ({
        ...spot,
        imageUrl: spot.images?.[0] || 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=400&h=300&fit=crop&auto=format'
      }))
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
    return spots.value.find(spot => spot.id === id)
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
      const baseURL = config.public.apiBaseUrl || 'http://localhost:8000/api'
      
      const response = await $fetch<{
        success: boolean
        data: TouristSpot[]
        fallback?: boolean
        message?: string
      }>(`${baseURL}/popular-spots`)

      if (response.success) {
        popularSpots.value = response.data
        if (response.fallback) {
          console.warn('Using fallback popular spots:', response.message)
        }
      } else {
        throw new Error('Failed to fetch popular spots')
      }
    } catch (err) {
      console.error('Failed to fetch popular spots:', err)
      popularSpotsError.value = 'Failed to fetch popular spots'
      
      // Fallback to random spots
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
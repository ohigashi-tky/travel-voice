<template>
  <div class="relative w-full max-w-7xl mx-auto">
    <!-- Google Maps Container -->
    <div 
      id="google-map" 
      ref="mapContainer"
      class="w-full h-[600px] rounded-3xl overflow-hidden shadow-2xl border border-white/20"
      style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #1d4ed8 100%);"
    >
      <!-- Loading State -->
      <div v-if="isLoading" class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-900 to-indigo-900">
        <div class="text-center">
          <div class="w-16 h-16 bg-gradient-to-br from-cyan-400 to-purple-600 rounded-2xl mx-auto mb-4 flex items-center justify-center animate-pulse">
            <Map class="w-8 h-8 text-white" />
          </div>
          <h3 class="text-white font-bold text-xl mb-2">高品質地図を読み込み中...</h3>
          <p class="text-blue-200">Google Maps による正確な日本地図</p>
        </div>
      </div>
      
      <!-- Error State -->
      <div v-if="hasError" class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-900 to-red-800">
        <div class="text-center max-w-md mx-auto p-6">
          <div class="w-16 h-16 bg-red-600 rounded-2xl mx-auto mb-4 flex items-center justify-center">
            <AlertTriangle class="w-8 h-8 text-white" />
          </div>
          <h3 class="text-white font-bold text-xl mb-2">地図の読み込みエラー</h3>
          <p class="text-red-200 text-sm mb-4">Google Maps APIキーを確認してください</p>
          <button 
            @click="retryLoad" 
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            再試行
          </button>
        </div>
      </div>
    </div>
    
    <!-- Prefecture Info Panel -->
    <Transition
      enter-active-class="transition-all duration-500"
      enter-from-class="opacity-0 transform translate-y-8 scale-95"
      enter-to-class="opacity-100 transform translate-y-0 scale-100"
      leave-active-class="transition-all duration-300"
      leave-from-class="opacity-100 transform translate-y-0 scale-100"
      leave-to-class="opacity-0 transform translate-y-8 scale-95"
    >
      <div
        v-if="selectedPrefecture"
        class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20"
      >
        <div class="glass-card rounded-3xl p-6 backdrop-blur-xl bg-white/10 border border-white/20 text-center min-w-80 shadow-2xl">
          <div class="flex items-center justify-center gap-3 mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-cyan-400 to-purple-600 rounded-xl flex items-center justify-center">
              <MapPin class="w-6 h-6 text-white" />
            </div>
            <h3 class="text-white font-black text-2xl">{{ selectedPrefecture.name }}</h3>
          </div>
          <p class="text-blue-100 text-base mb-4 leading-relaxed">{{ selectedPrefecture.description }}</p>
          <div class="flex gap-3">
            <button 
              @click="navigateToPrefecture"
              class="flex-1 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-cyan-500/25 transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2"
            >
              <Play class="w-4 h-4" />
              ガイド開始
            </button>
            <button 
              @click="selectedPrefecture = null"
              class="px-4 py-3 bg-white/10 text-white rounded-xl hover:bg-white/20 transition-colors"
            >
              <X class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { Map, AlertTriangle, MapPin, Play, X } from 'lucide-vue-next'

interface PrefectureInfo {
  name: string
  code: string
  description: string
  lat: number
  lng: number
}

const emit = defineEmits<{
  prefectureSelected: [prefecture: string]
}>()

const config = useRuntimeConfig()
const mapContainer = ref<HTMLElement>()
const isLoading = ref(true)
const hasError = ref(false)
const selectedPrefecture = ref<PrefectureInfo | null>(null)
const map = ref<google.maps.Map | null>(null)
const dataLayer = ref<google.maps.Data | null>(null)

// Google Maps API key と Map ID (環境変数から取得)
const GOOGLE_MAPS_API_KEY = config.public.googleMapsApiKey
const GOOGLE_MAPS_MAP_ID = config.public.googleMapsMapId

// 都道府県情報（簡潔版）
const prefectureData: Record<string, PrefectureInfo> = {
  'hokkaido': { name: '北海道', code: '01', description: '雄大な自然と新鮮な海の幸、四季の美しさが楽しめる北の大地', lat: 43.2203, lng: 142.8635 },
  'aomori': { name: '青森県', code: '02', description: 'りんごとねぶた祭りで有名、本州最北端の自然豊かな県', lat: 40.5856, lng: 140.6408 },
  'iwate': { name: '岩手県', code: '03', description: '広大な自然と温泉、宮沢賢治ゆかりの文学の故郷', lat: 39.7036, lng: 141.1527 },
  'miyagi': { name: '宮城県', code: '04', description: '伊達政宗と牛タンで有名、東北の中心都市仙台がある県', lat: 38.7222, lng: 140.7275 },
  'akita': { name: '秋田県', code: '05', description: '美人の里として知られ、きりたんぽと日本酒が自慢', lat: 39.1855, lng: 140.1023 },
  'yamagata': { name: '山形県', code: '06', description: 'さくらんぼと温泉の宝庫、四季の移ろいが美しい県', lat: 38.2404, lng: 140.3633 },
  'fukushima': { name: '福島県', code: '07', description: '桃と温泉、歴史ある会津若松など魅力溢れる県', lat: 37.7503, lng: 140.4676 },
  'ibaraki': { name: '茨城県', code: '08', description: '袋田の滝と偕楽園で有名、自然と歴史が調和する県', lat: 36.3418, lng: 140.4468 },
  'tochigi': { name: '栃木県', code: '09', description: '日光東照宮と華厳の滝、歴史と自然の宝庫', lat: 36.5658, lng: 139.8836 },
  'gunma': { name: '群馬県', code: '10', description: '草津温泉と伊香保温泉、温泉王国として親しまれる県', lat: 36.3910, lng: 139.0603 },
  'saitama': { name: '埼玉県', code: '11', description: '小江戸川越と秩父の自然、東京に隣接する魅力的な県', lat: 35.8569, lng: 139.6489 },
  'chiba': { name: '千葉県', code: '12', description: '成田空港と東京ディズニーリゾート、海と緑豊かな県', lat: 35.6051, lng: 140.1233 },
  'tokyo': { name: '東京都', code: '13', description: '日本の首都として現代と伝統が融合する国際都市', lat: 35.6762, lng: 139.6503 },
  'kanagawa': { name: '神奈川県', code: '14', description: '横浜中華街と鎌倉大仏、海と山に囲まれた県', lat: 35.4478, lng: 139.6425 },
  'niigata': { name: '新潟県', code: '15', description: 'コシヒカリと日本酒、雪国の美しい自然が魅力', lat: 37.9026, lng: 139.0232 },
  'toyama': { name: '富山県', code: '16', description: '立山連峰と富山湾の絶景、薬の都として知られる県', lat: 36.6953, lng: 137.2113 },
  'ishikawa': { name: '石川県', code: '17', description: '金沢の古都文化と能登半島の自然、伝統工芸が息づく県', lat: 36.5946, lng: 136.6256 },
  'fukui': { name: '福井県', code: '18', description: '恐竜化石と若狭湾、歴史と自然が共存する県', lat: 35.9436, lng: 136.1882 },
  'yamanashi': { name: '山梨県', code: '19', description: '富士山と武田信玄、果物とワインの郷', lat: 35.6642, lng: 138.5683 },
  'nagano': { name: '長野県', code: '20', description: '信州の美しい山々と善光寺、自然と歴史の宝庫', lat: 36.2048, lng: 138.2529 },
  'gifu': { name: '岐阜県', code: '21', description: '飛騨高山と白川郷、日本の原風景が残る県', lat: 35.3912, lng: 136.7223 },
  'shizuoka': { name: '静岡県', code: '22', description: '富士山と伊豆半島、温泉とお茶の名産地', lat: 34.9756, lng: 138.3828 },
  'aichi': { name: '愛知県', code: '23', description: '名古屋城と自動車産業、中部地方の中心都市', lat: 35.1802, lng: 136.9066 },
  'mie': { name: '三重県', code: '24', description: '伊勢神宮と熊野古道、日本の心のふるさと', lat: 34.7303, lng: 136.5086 },
  'shiga': { name: '滋賀県', code: '25', description: '琵琶湖と近江八景、水と歴史に恵まれた県', lat: 35.0045, lng: 135.8686 },
  'kyoto': { name: '京都府', code: '26', description: '千年の都として栄えた古都、寺社仏閣と伝統文化の宝庫', lat: 35.0211, lng: 135.7556 },
  'osaka': { name: '大阪府', code: '27', description: '食い倒れの街、関西の中心として賑わう商業都市', lat: 34.6937, lng: 135.5023 },
  'hyogo': { name: '兵庫県', code: '28', description: '神戸港と姫路城、国際都市と歴史が調和する県', lat: 34.6913, lng: 135.1830 },
  'nara': { name: '奈良県', code: '29', description: '古都奈良と奈良公園の鹿、日本文化発祥の地', lat: 34.6851, lng: 135.8048 },
  'wakayama': { name: '和歌山県', code: '30', description: '熊野三山と高野山、聖地と自然が織りなす県', lat: 34.2261, lng: 135.1675 },
  'tottori': { name: '鳥取県', code: '31', description: '鳥取砂丘と大山、自然の造形美が圧巻の県', lat: 35.5038, lng: 134.2384 },
  'shimane': { name: '島根県', code: '32', description: '出雲大社と石見銀山、神話と歴史のロマン溢れる県', lat: 35.4723, lng: 133.0505 },
  'okayama': { name: '岡山県', code: '33', description: '岡山城と後楽園、桃太郎伝説の故郷', lat: 34.6617, lng: 133.9351 },
  'hiroshima': { name: '広島県', code: '34', description: '平和の象徴と厳島神社、歴史と自然の調和する県', lat: 34.3963, lng: 132.4596 },
  'yamaguchi': { name: '山口県', code: '35', description: '萩の古い町並みと角島大橋、歴史と絶景の県', lat: 34.1860, lng: 131.4706 },
  'tokushima': { name: '徳島県', code: '36', description: '阿波踊りと渦潮、伝統芸能と自然現象が魅力', lat: 34.0658, lng: 134.5594 },
  'kagawa': { name: '香川県', code: '37', description: '讃岐うどんと金毘羅さん、四国で最も小さな県', lat: 34.3401, lng: 134.0430 },
  'ehime': { name: '愛媛県', code: '38', description: '道後温泉と松山城、文学と歴史の薫る県', lat: 33.8416, lng: 132.7660 },
  'kochi': { name: '高知県', code: '39', description: '坂本龍馬と四万十川、自由と自然の精神が息づく県', lat: 33.5597, lng: 133.5311 },
  'fukuoka': { name: '福岡県', code: '40', description: '九州の玄関口、博多ラーメンと太宰府天満宮で有名', lat: 33.6064, lng: 130.4181 },
  'saga': { name: '佐賀県', code: '41', description: '有田焼と吉野ヶ里遺跡、伝統工芸と古代文化の県', lat: 33.2494, lng: 130.2989 },
  'nagasaki': { name: '長崎県', code: '42', description: '平和の願いとグラバー園、国際交流の歴史ある県', lat: 32.7603, lng: 129.8777 },
  'kumamoto': { name: '熊本県', code: '43', description: '熊本城と阿蘇山、雄大な自然と歴史が自慢', lat: 32.7898, lng: 130.7417 },
  'oita': { name: '大分県', code: '44', description: '別府温泉と由布院、日本一の温泉県', lat: 33.2382, lng: 131.6126 },
  'miyazaki': { name: '宮崎県', code: '45', description: '日向神話と宮崎牛、神秘と美食の南国', lat: 31.9117, lng: 131.4240 },
  'kagoshima': { name: '鹿児島県', code: '46', description: '桜島と屋久島、活火山と世界自然遺産の県', lat: 31.5602, lng: 130.5581 },
  'okinawa': { name: '沖縄県', code: '47', description: '美しい海とユニークな文化、南国リゾートの楽園', lat: 26.2124, lng: 127.6792 }
}

const initializeMap = async () => {
  try {
    isLoading.value = true
    hasError.value = false

    if (!GOOGLE_MAPS_API_KEY) {
      throw new Error('Google Maps API キーが設定されていません')
    }

    // Google Maps APIを動的に読み込み
    if (!window.google) {
      await loadGoogleMapsAPI()
    }

    if (!mapContainer.value) return

    // 地図の初期化
    const mapOptions: google.maps.MapOptions = {
      center: { lat: 36.2048, lng: 138.2529 }, // 日本の中心部
      zoom: 6,
      mapTypeId: google.maps.MapTypeId.TERRAIN,
      restriction: {
        latLngBounds: {
          north: 46,
          south: 24,
          west: 123,
          east: 146
        },
        strictBounds: false
      },
      // 不要なコントロールを非表示
      mapTypeControl: false,        // 左上の「地図」「航空写真」ボタン
      fullscreenControl: false,     // 右上の四角ボタン（フルスクリーン）
      streetViewControl: false,     // 右下の人型マーク（ストリートビュー）
      rotateControl: false,         // 右下のダイヤアイコン（回転）
      scaleControl: false,          // スケール表示
      zoomControl: true,            // ズームコントロールは残す
      gestureHandling: 'greedy',    // スムーズな操作
      styles: [
        {
          "featureType": "water",
          "elementType": "geometry",
          "stylers": [
            { "color": "#1e3a8a" },
            { "lightness": 17 }
          ]
        },
        {
          "featureType": "landscape",
          "elementType": "geometry",
          "stylers": [
            { "color": "#f5f5f2" },
            { "lightness": 20 }
          ]
        },
        {
          "featureType": "administrative.country",
          "elementType": "geometry.stroke",
          "stylers": [
            { "color": "#0891b2" },
            { "weight": 2 }
          ]
        },
        {
          "featureType": "administrative.province",
          "elementType": "geometry.stroke",
          "stylers": [
            { "color": "#059669" },
            { "weight": 1.5 }
          ]
        }
      ]
    }

    // Map IDが設定されている場合は使用
    if (GOOGLE_MAPS_MAP_ID) {
      mapOptions.mapId = GOOGLE_MAPS_MAP_ID
    }

    map.value = new google.maps.Map(mapContainer.value, mapOptions)

    // データレイヤーを追加
    dataLayer.value = new google.maps.Data()
    dataLayer.value.setMap(map.value)

    // 都道府県の境界データを読み込み
    await loadPrefectureBoundaries()

    isLoading.value = false

  } catch (error) {
    console.error('地図の初期化に失敗しました:', error)
    hasError.value = true
    isLoading.value = false
  }
}

const loadGoogleMapsAPI = (): Promise<void> => {
  return new Promise((resolve, reject) => {
    if (window.google) {
      resolve()
      return
    }

    const script = document.createElement('script')
    script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_API_KEY}&libraries=geometry`
    script.async = true
    script.defer = true
    script.onload = () => resolve()
    script.onerror = () => reject(new Error('Google Maps APIの読み込みに失敗しました'))
    document.head.appendChild(script)
  })
}

const loadPrefectureBoundaries = async () => {
  if (!dataLayer.value) return

  try {
    const { getJapanGeoJson } = useJapanGeoData()
    const geoJsonData = getJapanGeoJson()
    
    dataLayer.value.addGeoJson(geoJsonData)

    // スタイルの設定（デフォルトは非表示、ホバー時のみ表示）
    dataLayer.value.setStyle((feature) => {
      const prefectureName = feature.getProperty('NAME_1') as string
      const isHovered = selectedPrefecture.value?.name === prefectureName
      const isTokyo = prefectureName === '東京都'

      // デフォルトは完全に透明（非表示）
      if (!isHovered) {
        return {
          fillOpacity: 0,
          strokeOpacity: 0,
          clickable: true // クリックは有効にする
        }
      }

      // ホバー時のみ表示
      return {
        fillColor: isTokyo ? '#f59e0b' : '#06b6d4',
        fillOpacity: isTokyo ? 0.7 : 0.5,
        strokeColor: isTokyo ? '#d97706' : '#0891b2',
        strokeWeight: isTokyo ? 3 : 2,
        strokeOpacity: 0.8
      }
    })

    // クリックイベントの設定
    dataLayer.value.addListener('click', (event: google.maps.Data.MouseEvent) => {
      const prefectureName = event.feature.getProperty('NAME_1') as string
      
      console.log('Clicked prefecture:', prefectureName) // デバッグ用
      console.log('Available prefecture keys:', Object.keys(prefectureData)) // デバッグ用
      
      // prefectureData から一致する都道府県を検索（より柔軟なマッチング）
      const prefectureKey = Object.keys(prefectureData).find(key => 
        prefectureData[key].name === prefectureName
      )
      
      console.log('Found prefecture key:', prefectureKey) // デバッグ用
      
      if (prefectureKey) {
        selectedPrefecture.value = prefectureData[prefectureKey]
      }
      
      // 都道府県データがなくても直接イベント発火（重要な修正）
      console.log('Emitting prefecture-selected event:', prefectureName) // デバッグ用
      emit('prefectureSelected', prefectureName)
    })

    // マウスオーバーイベント
    dataLayer.value.addListener('mouseover', (event: google.maps.Data.MouseEvent) => {
      const prefectureName = event.feature.getProperty('NAME_1') as string
      const prefectureKey = Object.keys(prefectureData).find(key => 
        prefectureData[key].name === prefectureName
      )
      
      if (prefectureKey) {
        selectedPrefecture.value = prefectureData[prefectureKey]
      }
      
      // ホバー時のスタイル（都道府県エリアを強調表示）
      const isTokyo = prefectureName === '東京都'
      dataLayer.value?.overrideStyle(event.feature, {
        fillColor: isTokyo ? '#f59e0b' : '#06b6d4',
        fillOpacity: isTokyo ? 0.7 : 0.5,
        strokeColor: isTokyo ? '#d97706' : '#0891b2',
        strokeWeight: isTokyo ? 3 : 2,
        strokeOpacity: 0.8
      })
    })

    // マウスアウトイベント  
    dataLayer.value.addListener('mouseout', (event: google.maps.Data.MouseEvent) => {
      // スタイルを元に戻す（透明に）
      dataLayer.value?.overrideStyle(event.feature, {
        fillOpacity: 0,
        strokeOpacity: 0
      })
      
      // パネルが表示されている場合、ユーザーがダイアログ内にいない場合のみ自動クローズ
      if (selectedPrefecture.value) {
        setTimeout(() => {
          // 3秒経過してもまだ同じ都道府県が選択されている場合のみ閉じる
          if (selectedPrefecture.value && selectedPrefecture.value.name === event.feature.getProperty('NAME_1')) {
            selectedPrefecture.value = null
          }
        }, 3000)
      }
    })

  } catch (error) {
    console.error('都道府県境界データの読み込みに失敗しました:', error)
  }
}

const navigateToPrefecture = () => {
  if (selectedPrefecture.value) {
    console.log('Navigating from info panel to:', selectedPrefecture.value.name) // デバッグ用
    
    // パネルを即座に閉じてからナビゲーション
    const prefectureName = selectedPrefecture.value.name
    selectedPrefecture.value = null
    
    // イベントを即座に発火（遅延なし）
    console.log('Emitting prefectureSelected event immediately:', prefectureName)
    emit('prefectureSelected', prefectureName)
  }
}

const retryLoad = () => {
  initializeMap()
}

// ライフサイクル
onMounted(async () => {
  await initializeMap()
})

// ブラウザAPIの型定義
declare global {
  interface Window {
    google: any
  }
}
</script>

<style scoped>
.glass-card {
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
}
</style>
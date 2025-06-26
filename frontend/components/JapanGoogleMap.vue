<template>
  <div class="google-map-container">
    <ClientOnly>
      <div 
        ref="mapContainer" 
        class="w-full h-80 rounded-lg shadow-md bg-gray-100 dark:bg-gray-700"
        style="min-height: 320px;"
      ></div>
      <template #fallback>
        <div class="w-full h-80 rounded-lg shadow-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
          <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
            <p class="text-gray-600 dark:text-gray-400 text-sm">地図を読み込み中...</p>
          </div>
        </div>
      </template>
    </ClientOnly>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'

interface Props {
  spotName: string
  latitude?: number
  longitude?: number
  zoom?: number
}

const props = withDefaults(defineProps<Props>(), {
  zoom: 15
})

const mapContainer = ref<HTMLElement>()
let map: google.maps.Map | null = null
const config = useRuntimeConfig()
const apiKey = config.public.googleMapsApiKey || 'AIzaSyDBTzeSepixQFP2y2pQcNciOSj8kYlDzh4'

// 観光地の座標データ
const spotCoordinates: Record<string, { lat: number; lng: number }> = {
  '東京スカイツリー': { lat: 35.7101, lng: 139.8107 },
  '浅草寺': { lat: 35.7148, lng: 139.7967 },
  '明治神宮': { lat: 35.6762, lng: 139.6993 },
  '大阪城': { lat: 34.6873, lng: 135.5262 },
  '通天閣': { lat: 34.6523, lng: 135.5061 },
  '海遊館': { lat: 34.6547, lng: 135.4281 },
  '清水寺': { lat: 34.9949, lng: 135.7851 },
  '金閣寺': { lat: 35.0394, lng: 135.7292 },
  '伏見稲荷大社': { lat: 34.9671, lng: 135.7727 },
  '札幌時計台': { lat: 43.0642, lng: 141.3469 },
  '函館山': { lat: 41.7640, lng: 140.6982 },
  '小樽運河': { lat: 43.1907, lng: 140.9947 },
  '名古屋城': { lat: 35.1856, lng: 136.8997 },
  '熱田神宮': { lat: 35.1281, lng: 136.9075 },
  'トヨタ産業技術記念館': { lat: 35.1954, lng: 136.8805 },
  '原爆ドーム': { lat: 34.3955, lng: 132.4536 },
  '厳島神社': { lat: 34.2969, lng: 132.3196 },
  '広島城': { lat: 34.4026, lng: 132.4594 },
  '太宰府天満宮': { lat: 33.5217, lng: 130.5332 },
  '福岡城跡': { lat: 33.5837, lng: 130.3791 },
  '博多駅': { lat: 33.5904, lng: 130.4017 }
}

const loadGoogleMaps = () => {
  return new Promise<void>((resolve, reject) => {
    // サーバーサイドでは何もしない
    if (import.meta.server) {
      resolve()
      return
    }

    if (window.google && window.google.maps) {
      resolve()
      return
    }

    // 既存のスクリプトがあるかチェック
    const existingScript = document.querySelector('script[src*="maps.googleapis.com"]')
    if (existingScript) {
      // 既存のスクリプトが読み込み完了を待つ
      existingScript.addEventListener('load', () => resolve())
      existingScript.addEventListener('error', () => reject(new Error('Failed to load Google Maps')))
      return
    }

    const script = document.createElement('script')
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&callback=initGoogleMaps`
    script.async = true
    script.defer = true
    
    // グローバルコールバック関数を設定
    ;(window as any).initGoogleMaps = () => {
      resolve()
      delete (window as any).initGoogleMaps
    }
    
    script.onerror = () => reject(new Error('Failed to load Google Maps'))
    
    document.head.appendChild(script)
  })
}

const initializeMap = async () => {
  // サーバーサイドでは何もしない
  if (import.meta.server || !mapContainer.value) return

  try {
    console.log('🗺️ Initializing Google Maps for:', props.spotName)
    console.log('🔑 API Key:', apiKey ? 'Set' : 'Not set')
    
    await loadGoogleMaps()
    console.log('✅ Google Maps API loaded successfully')

    // 座標を取得（props優先、次に座標データ、最後にデフォルト）
    const coordinates = props.latitude && props.longitude 
      ? { lat: props.latitude, lng: props.longitude }
      : spotCoordinates[props.spotName] || { lat: 35.6762, lng: 139.6993 } // 東京駅をデフォルト

    console.log('📍 Coordinates for', props.spotName, ':', coordinates)

    // マップを初期化
    map = new google.maps.Map(mapContainer.value, {
      center: coordinates,
      zoom: props.zoom,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      styles: [
        {
          featureType: 'poi',
          elementType: 'labels',
          stylers: [{ visibility: 'on' }]
        }
      ]
    })

    console.log('🗺️ Map initialized successfully')

    // 観光地にマーカーを追加
    const marker = new google.maps.Marker({
      position: coordinates,
      map: map,
      title: props.spotName,
      icon: {
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
          <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <circle cx="16" cy="16" r="12" fill="#DC2626" stroke="#FFFFFF" stroke-width="2"/>
            <circle cx="16" cy="16" r="6" fill="#FFFFFF"/>
          </svg>
        `),
        scaledSize: new google.maps.Size(32, 32),
        anchor: new google.maps.Point(16, 16)
      }
    })

    // 情報ウィンドウを追加
    const infoWindow = new google.maps.InfoWindow({
      content: `
        <div style="padding: 8px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
          <h3 style="margin: 0 0 4px 0; color: #1F2937; font-size: 16px; font-weight: 600;">${props.spotName}</h3>
          <p style="margin: 0; color: #6B7280; font-size: 14px;">観光スポット</p>
        </div>
      `
    })

    marker.addListener('click', () => {
      infoWindow.open(map, marker)
    })

    // 周辺の駅や観光地を検索
    const service = new google.maps.places.PlacesService(map)
    
    // 駅を検索
    service.nearbySearch({
      location: coordinates,
      radius: 1000,
      type: 'transit_station'
    }, (results, status) => {
      if (status === google.maps.places.PlacesServiceStatus.OK && results) {
        results.slice(0, 5).forEach(place => {
          if (place.geometry?.location) {
            new google.maps.Marker({
              position: place.geometry.location,
              map: map,
              title: place.name,
              icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                  <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" fill="#2563EB" stroke="#FFFFFF" stroke-width="2"/>
                    <rect x="8" y="8" width="8" height="8" fill="#FFFFFF" rx="1"/>
                  </svg>
                `),
                scaledSize: new google.maps.Size(24, 24),
                anchor: new google.maps.Point(12, 12)
              }
            })
          }
        })
      }
    })


  } catch (error) {
    console.error('Failed to initialize Google Maps:', error)
  }
}

onMounted(() => {
  initializeMap()
})

watch(() => [props.spotName, props.latitude, props.longitude], () => {
  initializeMap()
})
</script>

<style scoped>
.google-map-container {
  width: 100%;
}
</style>
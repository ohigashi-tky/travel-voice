<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 relative overflow-hidden">
    <!-- Dynamic Background -->
    <div class="absolute inset-0">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 via-purple-600/20 to-pink-600/20 animate-gradient-x"></div>
      <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-br from-cyan-400/20 to-blue-600/20 rounded-full filter blur-3xl animate-float"></div>
      <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-gradient-to-br from-purple-400/20 to-pink-600/20 rounded-full filter blur-3xl animate-float-delay"></div>
    </div>

    <!-- Fixed Back Button (always visible) -->
    <BackButton />

    <!-- Header -->
    <header class="relative z-10 p-6 pt-20">
      <nav class="flex items-center justify-center max-w-7xl mx-auto">
        <h1 class="text-2xl font-black text-white">
          <span class="bg-gradient-to-r from-cyan-400 to-white bg-clip-text text-transparent">
            {{ prefectureName }}
          </span>
        </h1>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 px-4 sm:px-6 lg:px-8 pb-20">
      <div class="max-w-7xl mx-auto">
        
        <!-- Prefecture Hero -->
        <div class="text-center mb-16">
          <div class="mb-8">
            <img 
              :src="prefectureImage" 
              alt="Prefecture Image"
              class="w-full max-w-2xl mx-auto rounded-3xl shadow-2xl"
            />
          </div>
          
          <h2 class="text-5xl md:text-7xl font-black text-white mb-6 animate-fade-in-up">
            {{ prefectureName }}
            <br>
            <span class="bg-gradient-to-r from-cyan-400 via-blue-400 to-purple-400 bg-clip-text text-transparent animate-shimmer">
              GUIDE
            </span>
          </h2>
          <p class="text-xl text-blue-100 max-w-3xl mx-auto">
            {{ prefectureDescription }}
          </p>
        </div>

        <!-- Tourist Spots or Coming Soon -->
        <div v-if="touristSpots.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
          <div 
            v-for="(spot, index) in touristSpots" 
            :key="spot.id"
            class="group relative"
            :style="{ animationDelay: `${index * 0.2}s` }"
          >
            <!-- Glow Effect -->
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/20 to-purple-600/20 rounded-3xl filter blur-xl group-hover:blur-2xl transition-all duration-500 opacity-0 group-hover:opacity-100"></div>
            
            <!-- Card -->
            <div class="relative glass-card rounded-3xl p-8 backdrop-blur-xl bg-white/5 border border-white/10 group-hover:bg-white/10 transition-all duration-500 transform group-hover:scale-105 animate-fade-in-up">
              
              <!-- Spot Image -->
              <div class="aspect-video rounded-2xl mb-6 overflow-hidden">
                <img 
                  :src="generateSpotImage(spot.name, spot.category)" 
                  :alt="spot.name"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Content -->
              <div class="space-y-4">
                <h3 class="text-2xl font-bold text-white group-hover:text-cyan-400 transition-colors">
                  {{ spot.name }}
                </h3>
                
                <p class="text-gray-300 leading-relaxed line-clamp-3">
                  {{ spot.description }}
                </p>
                
                <!-- Category Tag -->
                <div class="flex flex-wrap gap-2">
                  <span class="px-3 py-1 bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-blue-200 rounded-full text-sm backdrop-blur-sm border border-blue-400/20">
                    {{ spot.category }}
                  </span>
                </div>
                
                <!-- Action Button -->
                <button 
                  @click="goToSpotDetail(spot.id)"
                  class="w-full mt-6 py-4 bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-500 text-white font-bold rounded-2xl hover:shadow-lg hover:shadow-cyan-500/25 transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-3"
                >
                  <Icon name="lucide:eye" class="w-5 h-5" />
                  詳細を見る
                  <Icon name="lucide:arrow-right" class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Coming Soon Message -->
        <div v-else class="text-center py-20">
          <div class="glass-card rounded-3xl p-12 backdrop-blur-xl bg-white/5 border border-white/10 max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-gradient-to-br from-cyan-400 to-purple-600 rounded-2xl mx-auto mb-6 flex items-center justify-center">
              <Icon name="lucide:clock" class="w-10 h-10 text-white" />
            </div>
            <h3 class="text-3xl font-bold text-white mb-4">準備中です</h3>
            <p class="text-gray-300 leading-relaxed mb-6">
              {{ prefectureName }}の音声ガイドコンテンツを鋭意制作中です。<br>
              現在は東京都のガイドをお楽しみいただけます。
            </p>
            <button 
              @click="navigateTo('/tokyo')"
              class="px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-2xl hover:shadow-lg hover:shadow-cyan-500/25 transform hover:scale-105 transition-all duration-300"
            >
              東京ガイドを体験する
            </button>
          </div>
        </div>

        <!-- Audio Guide Player -->
        <AudioGuidePlayer 
          :audio-guide="currentAudioGuide"
          :spot-name="currentSpot?.name || null"
          :is-visible="!!currentAudioGuide"
          @close="closePlayer"
        />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import type { TouristSpot } from '~/types'

const route = useRoute()
const prefectureName = decodeURIComponent(route.params.name as string)

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: `${prefectureName} - Discover Japan`
})

// Removed useImageGeneration composable

const touristSpots = ref<TouristSpot[]>([])

const prefectureImage = computed(() => {
  const imageMap: Record<string, string> = {
    '東京都': '',
    '大阪府': '',
    '京都府': '',
    '北海道': '',
    '奈良県': '',
    '広島県': '',
    '福岡県': '',
    '沖縄県': ''
  }
  
  return imageMap[prefectureName] || ''
})

const prefectureDescription = computed(() => {
  const descriptions: Record<string, string> = {
    '東京都': '現代と伝統が融合する日本の首都。スカイツリー、浅草寺、明治神宮など、多彩な魅力に満ちています。',
    '北海道': '雄大な自然と美味しい食べ物の宝庫。四季折々の美しい景色と新鮮な海の幸が楽しめます。',
    '大阪府': '食い倒れの街として知られる関西の中心都市。大阪城や道頓堀など、歴史と現代が交差する魅力的な街です。',
    '京都府': '千年の都として栄えた古都。数多くの寺社仏閣と伝統文化が今も息づく美しい街並みが魅力です。',
    '奈良県': '日本の古都として知られ、東大寺や春日大社などの歴史的建造物と、鹿で有名な奈良公園があります。',
    '広島県': '平和の象徴として世界に知られる都市。厳島神社や原爆ドームなど、重要な歴史的意義を持つ場所があります。',
    '福岡県': '九州の玄関口として栄える都市。博多ラーメンや明太子などの美味しいグルメと、太宰府天満宮などの歴史スポットが魅力です。',
    '沖縄県': '美しい海と独特の文化が魅力の南国の楽園。首里城や美ら海水族館など、本土とは異なる魅力に満ちています。'
  }
  return descriptions[prefectureName] || `${prefectureName}の美しい自然と文化を音声ガイドと共に発見しましょう。`
})

const generateSpotImage = (spotName: string, category: string) => {
  // 観光地ごとの実際の画像URLを返す
  const imageMap: Record<string, string> = {
    '東京スカイツリー': '',
    '浅草寺': '',
    '明治神宮': '',
    '大阪城': '',
    '通天閣': '',
    '海遊館': '',
    '清水寺': '',
    '金閣寺': '',
    '伏見稲荷大社': '',
    '札幌時計台': '',
    '函館山': '',
    '小樽運河': ''
  }
  
  return imageMap[spotName] || ''
}


const goToSpotDetail = (spotId: number) => {
  navigateTo(`/spots/${spotId}`)
}

// Fetch tourist spots for this prefecture
onMounted(async () => {
  try {
    if (prefectureName === '大阪府') {
      // 大阪府のモックデータ
      touristSpots.value = [
        {
          id: 101,
          name: '大阪城',
          description: '豊臣秀吉が築城した名城。美しい天守閣と桜の名所として親しまれています。',
          category: '歴史建造物',
          prefecture: '大阪府',
          address: '大阪府大阪市中央区大阪城1-1',
          latitude: 34.6873,
          longitude: 135.5262,
          imageUrl: '',
          openingHours: '9:00-17:00',
          admissionFee: '大人600円',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString()
        },
        {
          id: 102,
          name: '通天閣',
          description: '新世界のシンボルタワー。ビリケンさんで有名な大阪を代表する観光地です。',
          category: '展望台',
          prefecture: '大阪府',
          address: '大阪府大阪市浪速区恵美須東1-18-6',
          latitude: 34.6523,
          longitude: 135.5061,
          imageUrl: '',
          openingHours: '8:30-21:30',
          admissionFee: '大人900円',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString()
        },
        {
          id: 103,
          name: '海遊館',
          description: '世界最大級の水族館。ジンベエザメやマンタが泳ぐ太平洋水槽は圧巻です。',
          category: '水族館',
          prefecture: '大阪府',
          address: '大阪府大阪市港区海岸通1-1-10',
          latitude: 34.6547,
          longitude: 135.4281,
          imageUrl: '',
          openingHours: '10:00-20:00',
          admissionFee: '大人2700円',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString()
        }
      ]
    } else if (prefectureName === '京都府') {
      // 京都府のモックデータ
      touristSpots.value = [
        {
          id: 201,
          name: '清水寺',
          description: '778年開創の京都最古の寺院。有名な清水の舞台と美しい景色で知られています。',
          category: '寺院',
          prefecture: '京都府',
          address: '京都府京都市東山区清水1-294',
          latitude: 34.9949,
          longitude: 135.7851,
          imageUrl: '',
          openingHours: '6:00-18:00',
          admissionFee: '大人400円',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString()
        },
        {
          id: 202,
          name: '金閣寺',
          description: '足利義満の別荘として建てられた金箔で覆われた美しい楼閣。世界文化遺産です。',
          category: '寺院',
          prefecture: '京都府',
          address: '京都府京都市北区金閣寺町1',
          latitude: 35.0394,
          longitude: 135.7292,
          imageUrl: '',
          openingHours: '9:00-17:00',
          admissionFee: '大人500円',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString()
        },
        {
          id: 203,
          name: '伏見稲荷大社',
          description: '全国の稲荷神社の総本宮。千本鳥居の美しい朱色のトンネルで有名です。',
          category: '神社',
          prefecture: '京都府',
          address: '京都府京都市伏見区深草藪之内町68',
          latitude: 34.9671,
          longitude: 135.7727,
          imageUrl: '',
          openingHours: '24時間',
          admissionFee: '無料',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString()
        }
      ]
    } else if (prefectureName === '北海道') {
      // 北海道のモックデータ
      touristSpots.value = [
        {
          id: 301,
          name: '札幌時計台',
          description: '旧札幌農学校演武場として1878年に建設された北海道のシンボル的建造物です。',
          category: '歴史建造物',
          prefecture: '北海道',
          address: '北海道札幌市中央区北1条西2丁目',
          latitude: 43.0642,
          longitude: 141.3469,
          imageUrl: '',
          openingHours: '8:45-17:10',
          admissionFee: '大人200円',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString()
        },
        {
          id: 302,
          name: '函館山',
          description: '世界三大夜景の一つに数えられる美しい夜景スポット。津軽海峡を一望できます。',
          category: '展望台',
          prefecture: '北海道',
          address: '北海道函館市函館山',
          latitude: 41.7640,
          longitude: 140.6982,
          imageUrl: '',
          openingHours: '10:00-21:00（ロープウェイ）',
          admissionFee: 'ロープウェイ往復大人1500円',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString()
        },
        {
          id: 303,
          name: '小樽運河',
          description: '1923年完成の歴史ある運河。石造倉庫群とガス灯が織りなすロマンチックな景観です。',
          category: '歴史的景観',
          prefecture: '北海道',
          address: '北海道小樽市港町',
          latitude: 43.1907,
          longitude: 140.9947,
          imageUrl: '',
          openingHours: '24時間',
          admissionFee: '無料',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString()
        }
      ]
    } else {
      const { $api } = useNuxtApp()
      const spots = await $api<TouristSpot[]>(`/tourist-spots?prefecture=${encodeURIComponent(prefectureName)}`)
      touristSpots.value = spots
    }
  } catch (error) {
    console.error('Error fetching tourist spots:', error)
    // For non-supported prefectures, show empty state
    touristSpots.value = []
  }
})
</script>
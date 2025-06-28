<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Page Title -->
    <div class="bg-white dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center relative">
          <button 
            @click="goHome"
            class="absolute left-0 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors duration-300 group"
          >
            <ArrowLeft class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" />
            <span class="text-sm font-medium">戻る</span>
          </button>
          <div class="text-center">
            <div class="text-4xl mb-2">{{ categoryEmoji }}</div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-wide transition-colors duration-300">
              <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent">
                {{ categoryName }}
              </span>
            </h1>
            <p class="text-gray-600 dark:text-gray-300 text-sm mt-2">{{ filteredSpots.length }}件の観光地</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 pb-24">
      <div class="max-w-7xl mx-auto py-6">
        
        <!-- Loading State -->
        <div v-if="isLoading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="text-gray-600 dark:text-gray-300 mt-4">読み込み中...</p>
        </div>

        <!-- Tourist Spots Grid -->
        <div v-else-if="filteredSpots.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div 
            v-for="spot in filteredSpots" 
            :key="spot.id"
            @click="goToSpotDetail(spot.id)"
            class="bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 dark:border-gray-700 cursor-pointer group"
          >
            <!-- Spot Image -->
            <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 relative overflow-hidden">
              <PlacePhotoImage 
                :spot-name="spot.name" :place-id="spot.place_id"
                :alt="spot.name"
                image-class="group-hover:scale-105 transition-transform duration-300"
              >
                <div class="absolute top-3 right-3">
                  <span class="bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white px-2 py-1 rounded-lg text-xs font-medium">
                    {{ spot.prefecture }}
                  </span>
                </div>
                <div class="absolute top-3 left-3">
                  <span class="bg-blue-500/90 text-white px-2 py-1 rounded-lg text-xs font-medium">
                    {{ spot.category }}
                  </span>
                </div>
              </PlacePhotoImage>
            </div>

            <!-- Spot Info -->
            <div class="p-4">
              <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                {{ spot.name }}
              </h3>
              <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2">
                {{ spot.description }}
              </p>
              
              <!-- Audio Guide Indicator -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400">
                  <span>🎧</span>
                  <span>音声ガイド対応</span>
                </div>
                <div class="text-gray-400 text-xs">
                  詳細を見る →
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <div class="text-6xl mb-4">{{ categoryEmoji }}</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            {{ categoryName }}の観光地が見つかりません
          </h3>
          <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300 mb-6">
            現在、このカテゴリの観光地は準備中です
          </p>
          <button 
            @click="goHome"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          >
            ホームに戻る
          </button>
        </div>

      </div>
    </main>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { ArrowLeft } from 'lucide-vue-next'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

// Reactive variables
const activeTab = ref('guide')
const isLoading = ref(true)
const categoryName = ref('')
const categoryEmoji = ref('🏛️')

// Get category from query params
const route = useRoute()

// All spots data (same as in index.vue and spots/[id].vue)
const allSpots = [
  {
    id: 1,
    name: '東京スカイツリー',
    description: '高さ634mの世界最高クラスの電波塔。展望デッキからは東京の絶景を一望できます。',
    category: '展望台',
    prefecture: '東京都',
    imageUrl: 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 2,
    name: '浅草寺',
    description: '東京最古の寺院。雷門と仲見世通りで有名な東京を代表する観光地です。',
    category: '寺院',
    prefecture: '東京都',
    imageUrl: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 3,
    name: '明治神宮',
    description: '明治天皇と昭憲皇太后を祀る神社。都心にありながら豊かな森に囲まれた神聖な空間です。',
    category: '神社',
    prefecture: '東京都',
    imageUrl: 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 101,
    name: '大阪城',
    description: '豊臣秀吉が築城した名城。美しい天守閣と桜の名所として親しまれています。',
    category: '歴史建造物',
    prefecture: '大阪府',
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 102,
    name: '通天閣',
    description: '新世界のシンボルタワー。ビリケンさんで有名な大阪を代表する観光地です。',
    category: '展望台',
    prefecture: '大阪府',
    imageUrl: 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 103,
    name: '海遊館',
    description: '世界最大級の水族館。ジンベエザメやマンタが泳ぐ太平洋水槽は圧巻です。',
    category: '水族館',
    prefecture: '大阪府',
    imageUrl: 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 201,
    name: '清水寺',
    description: '778年開創の京都最古の寺院。有名な清水の舞台と美しい景色で知られています。',
    category: '寺院',
    prefecture: '京都府',
    imageUrl: 'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 202,
    name: '金閣寺',
    description: '足利義満の別荘として建てられた金箔で覆われた美しい楼閣。世界文化遺産です。',
    category: '寺院',
    prefecture: '京都府',
    imageUrl: 'https://images.unsplash.com/photo-1478436127897-769e1b3f0f36?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 203,
    name: '伏見稲荷大社',
    description: '全国の稲荷神社の総本宮。千本鳥居の美しい朱色のトンネルで有名です。',
    category: '神社',
    prefecture: '京都府',
    imageUrl: 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 301,
    name: '札幌時計台',
    description: '旧札幌農学校演武場として1878年に建設された北海道のシンボル的建造物です。',
    category: '歴史建造物',
    prefecture: '北海道',
    imageUrl: 'https://images.unsplash.com/photo-1607619662634-3ac55ec0e216?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 302,
    name: '函館山',
    description: '世界三大夜景の一つに数えられる美しい夜景スポット。津軽海峡を一望できます。',
    category: '展望台',
    prefecture: '北海道',
    imageUrl: 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 303,
    name: '小樽運河',
    description: '1923年完成の歴史ある運河。石造倉庫群とガス灯が織りなすロマンチックな景観です。',
    category: '歴史的景観',
    prefecture: '北海道',
    imageUrl: 'https://images.unsplash.com/photo-1494522855154-9297ac14b55f?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 401,
    name: '名古屋城',
    description: '徳川家康が築城した名古屋のシンボル。金の鯱鉾で有名な日本三大名城の一つです。',
    category: '歴史建造物',
    prefecture: '愛知県',
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 402,
    name: '熱田神宮',
    description: '三種の神器の一つ草薙剣を祀る由緒ある神社。1900年の歴史を誇る格式高い神宮です。',
    category: '神社',
    prefecture: '愛知県',
    imageUrl: 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 403,
    name: 'トヨタ産業技術記念館',
    description: 'トヨタグループ発祥の地に建つ産業技術博物館。自動車産業の歴史と技術を学べます。',
    category: '博物館',
    prefecture: '愛知県',
    imageUrl: 'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 501,
    name: '太宰府天満宮',
    description: '学問の神様菅原道真公を祀る由緒ある神社。受験合格や学業成就を願う多くの参拝者が訪れます。',
    category: '神社',
    prefecture: '福岡県',
    imageUrl: 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 502,
    name: '福岡城跡',
    description: '黒田長政が築城した福岡藩の居城跡。現在は舞鶴公園として桜の名所にもなっています。',
    category: '歴史建造物',
    prefecture: '福岡県',
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 503,
    name: '博多駅',
    description: '九州の玄関口として知られる福岡の中心駅。周辺にはグルメや買い物スポットが充実しています。',
    category: '観光エリア',
    prefecture: '福岡県',
    imageUrl: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=400&h=300&fit=crop&auto=format'
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
  {
    id: 603,
    name: '広島城',
    description: '毛利輝元が築いた名城。現在は歴史博物館として広島の歴史を学ぶことができます。',
    category: '歴史建造物',
    prefecture: '広島県',
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format'
  },
  // 愛媛県
  {
    id: 801,
    name: '道後温泉',
    description: '日本最古の温泉の一つ。千と千尋の神隠しのモデルとしても有名な歴史ある温泉地です。',
    category: '温泉',
    prefecture: '愛媛県',
    imageUrl: 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 802,
    name: '松山城',
    description: '松山市の中心部、勝山山頂に建つ現存12天守の一つ。美しい桜の名所としても知られています。',
    category: '歴史建造物',
    prefecture: '愛媛県',
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 803,
    name: '今治城',
    description: '瀬戸内海に面した水城として築かれた美しい城。藤堂高虎が築城した海岸平城です。',
    category: '歴史建造物',
    prefecture: '愛媛県',
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 804,
    name: '内子町',
    description: '江戸時代から明治時代の町並みが美しく保存された重要伝統的建造物群保存地区です。',
    category: '歴史的景観',
    prefecture: '愛媛県',
    imageUrl: 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 805,
    name: '石鎚山',
    description: '西日本最高峰の霊峰。四季折々の美しい自然と登山が楽しめる愛媛県のシンボルです。',
    category: '自然',
    prefecture: '愛媛県',
    imageUrl: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=300&fit=crop&auto=format'
  },
  {
    id: 806,
    name: 'しまなみ海道',
    description: '本州と四国を結ぶ美しい橋の連続。サイクリングの聖地として世界的に有名です。',
    category: '観光エリア',
    prefecture: '愛媛県',
    imageUrl: 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=400&h=300&fit=crop&auto=format'
  }
]

// カテゴリごとのemoji
const categoryEmojiMap = {
  '寺院': '⛩️',
  '歴史建造物': '🏯',
  '神社': '🕊️',
  '展望台': '🗼',
  '博物館': '🏛️',
  '観光エリア': '🌆',
  '水族館': '🐟',
  '歴史的景観': '🏞️',
  '温泉': '♨️',
  '自然': '🌲'
}

// Filter spots by category
const filteredSpots = computed(() => {
  if (!categoryName.value) return []
  return allSpots.filter(spot => spot.category === categoryName.value)
})


// Set page title dynamically
useHead(() => ({
  title: `${categoryName.value} - Travel Voice`
}))

// Navigation functions
const goHome = () => {
  navigateTo('/')
}

const goToSpotDetail = (spotId) => {
  console.log('Navigating to spot:', spotId)
  navigateTo(`/spots/${spotId}`)
}

// Load category data on mount
onMounted(async () => {
  try {
    const queryCategory = route.query.name
    if (queryCategory) {
      categoryName.value = queryCategory
      categoryEmoji.value = categoryEmojiMap[queryCategory] || '🏛️'
    } else {
      // If no category specified, redirect to home
      await navigateTo('/')
      return
    }
  } catch (error) {
    console.error('Error loading category:', error)
  } finally {
    isLoading.value = false
  }
})

// Watch for route changes
watch(() => route.query.name, (newCategory) => {
  if (newCategory) {
    categoryName.value = newCategory
    categoryEmoji.value = categoryEmojiMap[newCategory] || '🏛️'
  }
})
</script>
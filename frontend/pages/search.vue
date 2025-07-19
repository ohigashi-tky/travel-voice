<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Page Title -->
    <div class="bg-white dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
          <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-wide transition-colors duration-300">
              <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent">
                検索結果
              </span>
            </h1>
            <p class="text-gray-600 dark:text-gray-300 text-sm mt-2">
              <span v-if="searchQuery">"{{ searchQuery }}" の検索結果</span>
            </p>
          </div>
        </div>
      </div>
    </div>


    <!-- Search Bar -->
    <div class="bg-gray-50 dark:bg-gray-800 py-4 border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative max-w-md mx-auto">
          <div class="relative">
            <input
              v-model="searchInput"
              @keyup.enter="performNewSearch"
              type="text"
              placeholder="観光地名を入力してください"
              class="w-full px-4 py-3 pl-12 pr-20 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
            />
            <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
            <button
              @click="performNewSearch"
              :disabled="!searchInput.trim()"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-4 py-1.5 rounded-md text-sm font-medium transition-colors"
            >
              検索
            </button>
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
          <p class="text-gray-600 dark:text-gray-300 mt-4">検索中...</p>
        </div>

        <!-- Search Results -->
        <div v-else-if="filteredSpots.length > 0">
          <!-- Filter Summary -->
          <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <div class="text-sm text-blue-700 dark:text-blue-300">
                {{ filteredSpots.length }}件の観光地が見つかりました
              </div>
              <div class="text-xs text-blue-600 dark:text-blue-400">
                検索条件: "{{ searchQuery }}"
              </div>
            </div>
          </div>

          <!-- Results Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <TouristSpotCard
              v-for="spot in filteredSpots"
              :key="spot.id"
              :spot="spot"
              :show-prefecture="true"
              :show-both-tags="true"
            />
          </div>

        </div>

        <!-- No Results State -->
        <div v-else class="text-center py-12">
          <div class="text-6xl mb-4">🔍</div>
          <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">
            検索結果が見つかりませんでした
          </h3>
          <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300 mb-6">
            <span v-if="searchQuery">"{{ searchQuery }}" に一致する観光地がありません</span>
            <br>
            別のキーワードで検索してみてください
          </p>
          
          <!-- Suggested Searches -->
          <div class="max-w-md mx-auto">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">おすすめの検索キーワード</p>
            <div class="flex flex-wrap gap-2 justify-center">
              <button
                v-for="suggestion in popularSearches"
                :key="suggestion"
                @click="searchSuggestion(suggestion)"
                class="px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full text-sm transition-colors"
              >
                {{ suggestion }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
    
    <!-- Footer -->
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { Search } from 'lucide-vue-next'
import AppHeader from '~/components/AppHeader.vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

// Reactive variables
const activeTab = ref('guide')
const isLoading = ref(true)
const searchQuery = ref('')
const searchInput = ref('')

// Get search query from URL params
const route = useRoute()

// All spots data (same as in other pages)
const allSpots = [
  {
    id: 1,
    name: '東京スカイツリー',
    description: '高さ634mの世界最高クラスの電波塔。展望デッキからは東京の絶景を一望できます。',
    category: '展望台',
    prefecture: '東京都',
    imageUrl: ''
  },
  {
    id: 2,
    name: '浅草寺',
    description: '東京最古の寺院。雷門と仲見世通りで有名な東京を代表する観光地です。',
    category: '寺院',
    prefecture: '東京都',
    imageUrl: ''
  },
  {
    id: 101,
    name: '大阪城',
    description: '豊臣秀吉が築城した名城。美しい天守閣と桜の名所として親しまれています。',
    category: '歴史建造物',
    prefecture: '大阪府',
    imageUrl: ''
  },
  {
    id: 201,
    name: '清水寺',
    description: '778年開創の京都最古の寺院。有名な清水の舞台と美しい景色で知られています。',
    category: '寺院',
    prefecture: '京都府',
    imageUrl: ''
  },
  {
    id: 301,
    name: '札幌時計台',
    description: '旧札幌農学校演武場として1878年に建設された北海道のシンボル的建造物です。',
    category: '歴史建造物',
    prefecture: '北海道',
    imageUrl: ''
  },
  {
    id: 202,
    name: '金閣寺',
    description: '足利義満の別荘として建てられた金箔で覆われた美しい楼閣。世界文化遺産です。',
    category: '寺院',
    prefecture: '京都府',
    imageUrl: ''
  },
  {
    id: 401,
    name: '名古屋城',
    description: '徳川家康が築城した名古屋のシンボル。金の鯱鉾で有名な日本三大名城の一つです。',
    category: '歴史建造物',
    prefecture: '愛知県',
    imageUrl: ''
  },
  {
    id: 402,
    name: '熱田神宮',
    description: '三種の神器の一つ草薙剣を祀る由緒ある神社。1900年の歴史を誇る格式高い神宮です。',
    category: '神社',
    prefecture: '愛知県',
    imageUrl: ''
  },
  {
    id: 403,
    name: 'トヨタ産業技術記念館',
    description: 'トヨタグループ発祥の地に建つ産業技術博物館。自動車産業の歴史と技術を学べます。',
    category: '博物館',
    prefecture: '愛知県',
    imageUrl: ''
  },
  {
    id: 501,
    name: '太宰府天満宮',
    description: '学問の神様菅原道真公を祀る由緒ある神社。受験合格や学業成就を願う多くの参拝者が訪れます。',
    category: '神社',
    prefecture: '福岡県',
    imageUrl: ''
  },
  {
    id: 502,
    name: '福岡城跡',
    description: '黒田長政が築城した福岡藩の居城跡。現在は舞鶴公園として桜の名所にもなっています。',
    category: '歴史建造物',
    prefecture: '福岡県',
    imageUrl: ''
  },
  {
    id: 503,
    name: '博多駅',
    description: '九州の玄関口として知られる福岡の中心駅。周辺にはグルメや買い物スポットが充実しています。',
    category: '観光エリア',
    prefecture: '福岡県',
    imageUrl: ''
  }
]

// Popular search suggestions
const popularSearches = ['大阪', '東京', '京都', '寺院', '歴史建造物', '神社']

// Filter spots based on search query
const filteredSpots = computed(() => {
  if (!searchQuery.value) return []
  
  const query = searchQuery.value.toLowerCase()
  return allSpots.filter(spot =>
    spot.name.toLowerCase().includes(query) ||
    spot.prefecture.toLowerCase().includes(query) ||
    spot.category.toLowerCase().includes(query) ||
    spot.description.toLowerCase().includes(query)
  )
})

// Get unique prefectures and categories for stats
const uniquePrefectures = computed(() => {
  const prefectures = filteredSpots.value.map(spot => spot.prefecture)
  return [...new Set(prefectures)]
})

const uniqueCategories = computed(() => {
  const categories = filteredSpots.value.map(spot => spot.category)
  return [...new Set(categories)]
})

// Set page title dynamically
useHead(() => ({
  title: searchQuery.value ? `"${searchQuery.value}" の検索結果 - Travel Voice` : '検索結果 - Travel Voice'
}))

// Navigation functions
const goToSpotDetail = (spotId) => {
  navigateTo(`/spots/${spotId}`)
}

const performNewSearch = () => {
  if (searchInput.value.trim()) {
    navigateTo(`/search?q=${encodeURIComponent(searchInput.value.trim())}`)
  }
}

const searchSuggestion = (suggestion) => {
  searchInput.value = suggestion
  performNewSearch()
}

// Load search data on mount
onMounted(async () => {
  try {
    const queryParam = route.query.q
    if (queryParam) {
      searchQuery.value = queryParam
      searchInput.value = queryParam
    } else {
      // If no search query, redirect to home
      await navigateTo('/')
      return
    }
  } catch (error) {
    console.error('Error loading search:', error)
  } finally {
    isLoading.value = false
  }
})

// Watch for route changes
watch(() => route.query.q, (newQuery) => {
  if (newQuery) {
    searchQuery.value = newQuery
    searchInput.value = newQuery
  }
})
</script>

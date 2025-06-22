<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />
    
    <!-- Main Content -->
    <div class="flex-1 relative z-10 pb-24 pt-16">
      <div class="p-6">
        <div class="max-w-7xl mx-auto text-center">
          <h1 class="text-7xl font-bold text-gray-800 dark:text-white mb-4 tracking-wider text-center transition-colors duration-300">
            <span class="bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 bg-clip-text text-transparent font-extrabold">
              {{ t('Travel Voice') }}
            </span>
          </h1>
          <p class="text-xl text-gray-600 dark:text-gray-300 font-medium max-w-2xl mx-auto mb-8 tracking-wide transition-colors duration-300">
            {{ t('音声ガイドで観光を楽しもう') }}
          </p>
        </div>
        
        <div class="max-w-6xl mx-auto">
          <!-- Search Section -->
          <div class="mb-8">
            <!-- Search Input with Suggestions -->
            <div class="relative max-w-md mx-auto">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  @input="onSearchInput"
                  @focus="onInputFocus"
                  @blur="onInputBlur"
                  type="text"
                  placeholder=""
                  class="w-full px-4 py-3 pl-12 pr-20 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300"
                />
                <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                
                <!-- Animated Placeholder -->
                <div 
                  v-if="!searchQuery && !isInputFocused"
                  class="absolute left-12 top-1/2 transform -translate-y-1/2 pointer-events-none h-6 overflow-hidden w-64"
                >
                  <div class="relative h-6">
                    <div 
                      v-for="(spot, index) in placeholderSpots"
                      :key="index"
                      class="absolute inset-0 text-gray-400 dark:text-gray-500 whitespace-nowrap flex items-center transition-all duration-300 ease-out"
                      :style="getPlaceholderStyle(index)"
                    >
                      {{ spot }}
                    </div>
                  </div>
                </div>
                
                <button
                  @click="performSearch"
                  :disabled="!searchQuery.trim()"
                  class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-4 py-1.5 rounded-md text-sm font-medium transition-colors"
                >
                  {{ t('検索') }}
                </button>
              </div>
              
              <!-- Search Suggestions -->
              <div 
                v-if="showSuggestions && searchSuggestions.length > 0"
                class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"
              >
                <button
                  v-for="suggestion in searchSuggestions"
                  :key="suggestion.id"
                  @mousedown="selectSuggestion(suggestion)"
                  class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                >
                  <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-lg flex-shrink-0 overflow-hidden">
                    <img 
                      :src="suggestion.imageUrl" 
                      :alt="suggestion.name"
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 dark:text-white text-sm">{{ suggestion.name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                      <span>{{ suggestion.prefecture }}</span>
                      <span>•</span>
                      <span>{{ suggestion.category }}</span>
                    </div>
                  </div>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Popular Spots Section -->
          <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-8 transition-colors duration-300">
            <div class="text-center mb-6">
              <div class="flex items-center justify-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg flex items-center justify-center">
                  <Sparkles class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-gray-800 dark:text-white text-xl font-light tracking-wide transition-colors duration-300">{{ t('人気スポット') }}</h3>
              </div>
            </div>
            
            <!-- Carousel Container -->
            <div class="relative">
              <!-- Spots Carousel -->
              <div 
                class="overflow-hidden"
                @mousedown="handleMouseDown"
                @mousemove="handleMouseMove"
                @mouseup="handleMouseUp"
                @mouseleave="handleMouseUp"
                @touchstart="handleTouchStart"
                @touchmove="handleTouchMove"
                @touchend="handleTouchEnd"
              >
                <div 
                  class="flex transition-transform duration-500 ease-in-out"
                  :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
                >
                  <div 
                    v-for="spot in recommendedSpots" 
                    :key="spot.id"
                    class="w-full flex-shrink-0 px-2"
                  >
                    <div 
                      @click="goToSpotDetail(spot.id)"
                      class="bg-white dark:bg-gray-700 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105"
                    >
                      <!-- Horizontal Layout -->
                      <div class="flex h-32">
                        <!-- Spot Image -->
                        <div class="w-48 bg-gradient-to-br from-blue-400 to-purple-500 relative flex-shrink-0">
                          <img 
                            :src="spot.imageUrl" 
                            :alt="spot.name"
                            class="w-full h-full object-cover"
                            loading="lazy"
                          />
                          <div class="absolute top-2 right-2">
                            <span class="bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white px-2 py-1 rounded text-xs font-medium">
                              {{ spot.prefecture }}
                            </span>
                          </div>
                        </div>
                        
                        <!-- Spot Info -->
                        <div class="flex-1 p-4 flex flex-col justify-between">
                          <div>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 transition-colors duration-300">
                              {{ spot.name }}
                            </h4>
                            <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 transition-colors duration-300">
                              {{ spot.description }}
                            </p>
                          </div>
                          
                          <!-- Bottom Row -->
                          <div class="flex justify-between items-center mt-2">
                            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded text-xs transition-colors duration-300">
                              {{ spot.category }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Dots Indicator -->
              <div class="flex justify-center mt-4 space-x-2">
                <div 
                  v-for="(spot, index) in recommendedSpots" 
                  :key="index"
                  :class="[
                    'w-2 h-2 rounded-full transition-colors',
                    currentIndex === index 
                      ? 'bg-blue-600' 
                      : 'bg-gray-300 dark:bg-gray-600'
                  ]"
                />
              </div>
            </div>
          </div>
          
          <!-- Prefecture Selection -->
          <div class="bg-gray-50 dark:bg-gray-800 rounded-3xl p-8 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="text-center mb-6">
              <div class="flex items-center justify-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                  <MapPin class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-gray-800 dark:text-white text-xl font-light tracking-wide transition-colors duration-300">{{ t('都道府県から探す') }}</h3>
              </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <button
                v-for="prefecture in mainPrefectures"
                :key="prefecture.name"
                @click="selectPrefecture(prefecture)"
                :class="[
                  'group p-4 rounded-xl border transition-all duration-300',
                  prefecture.available 
                    ? 'bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600 transform hover:scale-105 cursor-pointer'
                    : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed'
                ]"
                :disabled="!prefecture.available"
              >
                <div class="text-3xl mb-2">{{ prefecture.emoji }}</div>
                <h4 :class="[
                  'font-light text-sm transition-colors tracking-wide',
                  prefecture.available 
                    ? 'text-gray-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400'
                    : 'text-gray-500 dark:text-gray-400'
                ]">
                  {{ prefecture.name }}
                </h4>
              </button>
            </div>
            
            <!-- All Prefectures Button -->
            <div class="mt-6 text-center">
              <button
                @click="showPrefectureModal = true"
                class="inline-flex items-center gap-2 px-6 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
              >
                <span>すべて表示</span>
                <span class="text-sm">→</span>
              </button>
            </div>
          </div>
          
          <!-- Category Selection -->
          <div class="bg-gray-50 dark:bg-gray-800 rounded-3xl p-8 border border-gray-200 dark:border-gray-700 transition-colors duration-300 mt-8">
            <div class="text-center mb-6">
              <div class="flex items-center justify-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                  <Grid3X3 class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-gray-800 dark:text-white text-xl font-light tracking-wide transition-colors duration-300">{{ t('カテゴリから探す') }}</h3>
              </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <button
                v-for="category in categoryList"
                :key="category.name"
                @click="selectCategory(category)"
                class="group p-4 rounded-xl border bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600 transform hover:scale-105 cursor-pointer transition-all duration-300"
              >
                <div class="text-3xl mb-2">{{ category.emoji }}</div>
                <h4 class="font-light text-sm transition-colors tracking-wide text-gray-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                  {{ category.name }}
                </h4>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Prefecture Modal -->
    <div
      v-if="showPrefectureModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click="closePrefectureModal"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white">都道府県を選択</h2>
          <button
            @click="showPrefectureModal = false"
            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-2xl"
          >
            ×
          </button>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6">
          <div
            v-for="region in prefectureRegions"
            :key="region.name"
            class="mb-8 last:mb-0"
          >
            <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">{{ region.name }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
              <button
                v-for="prefecture in region.prefectures"
                :key="prefecture.name"
                @click="selectPrefectureFromModal(prefecture)"
                :disabled="!prefecture.available"
                :class="[
                  'text-left p-3 rounded-lg border transition-all duration-200',
                  prefecture.available
                    ? 'bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600 text-gray-800 dark:text-white cursor-pointer'
                    : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500 cursor-not-allowed'
                ]"
              >
                <div class="flex items-center gap-2">
                  <span class="text-lg">{{ prefecture.emoji }}</span>
                  <span class="text-sm font-medium">{{ prefecture.name }}</span>
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <AppFooter v-model="activeTab" />
    
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Sparkles, MapPin, Grid3X3, Search } from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'
import { useLanguage } from '~/composables/useLanguage'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Travel Voice - 音声で楽しむ日本全国の魅力',
  meta: [
    { name: 'description', content: '日本全国47都道府県の観光スポットを音声ガイドで楽しもう。地図から簡単アクセス、プロのガイドで深く学べる日本の歴史と文化。' }
  ]
})

// Auth store
const authStore = useAuthStore()

// Language
const { t } = useLanguage()

// Reactive variables
const activeTab = ref('top')
const currentIndex = ref(0)
const recommendedSpots = ref([])
const showPrefectureModal = ref(false)
let carouselInterval = null

// Search related variables
const searchQuery = ref('')
const showSuggestions = ref(false)
const searchSuggestions = ref([])
const isInputFocused = ref(false)

// Placeholder rotation
const placeholderSpots = ['東京スカイツリー', '大阪城', '清水寺', '太宰府天満宮', '名古屋城']
const currentPlaceholderIndex = ref(0)
let placeholderInterval = null

// Drag/swipe functionality
const isDragging = ref(false)
const startPos = ref(0)
const currentPos = ref(0)
const dragThreshold = 50

// All available spots data
const allSpots = [
  {
    id: 1,
    name: '東京スカイツリー',
    description: '高さ634mの世界最高クラスの電波塔。展望デッキからは東京の絶景を一望できます。',
    category: '展望台',
    prefecture: '東京都',
    imageUrl: 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=400&h=300&fit=crop&auto=format',
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
    imageUrl: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=400&h=300&fit=crop&auto=format',
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
      'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=600&h=400&fit=crop&auto=format'
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
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format',
    overview: '名古屋城は1610年に徳川家康の命により築城された平城です。天守閣の金の鯱鉾は名古屋の象徴として親しまれ、日本三大名城の一つに数えられています。',
    highlights: ['金の鯱鉾', '本丸御殿', '西南隅櫓', '名古屋城桜まつり'],
    history: '慶長15年（1610年）、徳川家康の九男義直の居城として築城開始。明治維新後は名古屋離宮となり、戦災で焼失後、1959年に鉄筋コンクリート造で再建されました。',
    images: [
      'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=600&h=400&fit=crop&auto=format'
    ]
  },
  {
    id: 402,
    name: '熱田神宮',
    description: '三種の神器の一つ草薙剣を祀る由緒ある神社。1900年の歴史を誇る格式高い神宮です。',
    category: '神社',
    prefecture: '愛知県',
    imageUrl: 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format',
    overview: '熱田神宮は三種の神器の一つである草薙剣を御神体とする神社で、伊勢神宮に次ぐ格式を誇ります。約1900年の歴史を持つ由緒ある神宮です。',
    highlights: ['草薙剣', '宝物館', '熱田まつり', '信長塀'],
    history: '景行天皇43年、日本武尊が草薙剣を留め置かれたのが起源とされています。織田信長が桶狭間の戦いの前に戦勝祈願をしたことでも有名です。',
    images: [
      'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=600&h=400&fit=crop&auto=format'
    ]
  },
  {
    id: 403,
    name: 'トヨタ産業技術記念館',
    description: 'トヨタグループ発祥の地に建つ産業技術博物館。自動車産業の歴史と技術を学べます。',
    category: '博物館',
    prefecture: '愛知県',
    imageUrl: 'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?w=400&h=300&fit=crop&auto=format',
    overview: 'トヨタ産業技術記念館は、トヨタグループ発祥の地に建つ産業博物館です。繊維機械から自動車への発展過程と、ものづくりの技術を体験できます。',
    highlights: ['繊維機械館', '自動車館', 'テクノランド', '実演展示'],
    history: '1994年に開館。豊田佐吉が発明した自動織機から始まり、トヨタ自動車の発展まで、日本の産業技術の歩みを展示しています。',
    images: [
      'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1494522855154-9297ac14b55f?w=600&h=400&fit=crop&auto=format'
    ]
  },
  // 福岡県
  {
    id: 501,
    name: '太宰府天満宮',
    description: '学問の神様菅原道真公を祀る由緒ある神社。受験合格や学業成就を願う多くの参拝者が訪れます。',
    category: '神社',
    prefecture: '福岡県',
    imageUrl: 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=400&h=300&fit=crop&auto=format',
    overview: '太宰府天満宮は学問の神様として親しまれる菅原道真公を祀る神社です。全国約12,000社の天満宮の総本宮として多くの参拝者が訪れます。',
    highlights: ['本殿', '飛梅', '宝物殿', '天満宮博物館'],
    history: '延喜3年（903年）に菅原道真公が薨去された後、919年に社殿が建立されました。学問・至誠・厄除けの神様として全国から信仰を集めています。',
    images: [
      'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=600&h=400&fit=crop&auto=format'
    ]
  },
  {
    id: 502,
    name: '福岡城跡',
    description: '黒田長政が築城した福岡藩の居城跡。現在は舞鶴公園として桜の名所にもなっています。',
    category: '歴史建造物',
    prefecture: '福岡県',
    imageUrl: 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=400&h=300&fit=crop&auto=format',
    overview: '福岡城は黒田長政によって築城された福岡藩52万石の居城跡です。現在は舞鶴公園として整備され、桜の名所としても親しまれています。',
    highlights: ['天守台', '多聞櫓', '舞鶴公園', '桜の名所'],
    history: '慶長6年（1601年）から7年をかけて黒田長政が築城。明治時代に廃城となりましたが、石垣や櫓などの遺構が残り、福岡市のシンボルとなっています。',
    images: [
      'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1590736969955-71cc94901144?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600&h=400&fit=crop&auto=format'
    ]
  },
  {
    id: 503,
    name: '博多駅',
    description: '九州の玄関口として知られる福岡の中心駅。周辺にはグルメや買い物スポットが充実しています。',
    category: '観光エリア',
    prefecture: '福岡県',
    imageUrl: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=400&h=300&fit=crop&auto=format',
    overview: '博多駅は九州最大のターミナル駅で、新幹線から在来線まで多くの路線が乗り入れています。駅周辺は商業施設やグルメスポットが充実した福岡の中心地です。',
    highlights: ['JR博多シティ', 'キャナルシティ博多', '博多グルメ', '屋台街'],
    history: '1889年に開業。九州の玄関口として発展し、2011年に九州新幹線が全線開通したことで、さらに重要な交通拠点となりました。',
    images: [
      'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1494522855154-9297ac14b55f?w=600&h=400&fit=crop&auto=format'
    ]
  }
]

// Initialize auth state on mount (middleware already handles authentication)
onMounted(() => {
  // Auth is already initialized by plugin and checked by middleware
  selectRandomSpots()
  startCarousel()
  startPlaceholderRotation()
})

onUnmounted(() => {
  stopCarousel()
  stopPlaceholderRotation()
})

// Select 5 random spots for recommendations
const selectRandomSpots = () => {
  const shuffled = [...allSpots].sort(() => 0.5 - Math.random())
  recommendedSpots.value = shuffled.slice(0, 5)
}

// Auto carousel functions
const startCarousel = () => {
  carouselInterval = setInterval(() => {
    nextSpot()
  }, 4000) // 4秒ごとに切り替え
}

const stopCarousel = () => {
  if (carouselInterval) {
    clearInterval(carouselInterval)
    carouselInterval = null
  }
}

// Placeholder rotation functions
const startPlaceholderRotation = () => {
  placeholderInterval = setInterval(() => {
    currentPlaceholderIndex.value = (currentPlaceholderIndex.value + 1) % placeholderSpots.length
  }, 3000) // 3秒ごとに切り替え
}

const stopPlaceholderRotation = () => {
  if (placeholderInterval) {
    clearInterval(placeholderInterval)
    placeholderInterval = null
  }
}

// Carousel navigation (infinite loop)
const nextSpot = () => {
  currentIndex.value = (currentIndex.value + 1) % recommendedSpots.value.length
}

// Navigate to spot detail page
const goToSpotDetail = (spotId) => {
  console.log('Navigating to spot:', spotId) // Debug log
  // Only navigate if not dragging
  if (!isDragging.value) {
    stopCarousel() // Stop auto-scroll when navigating
    navigateTo(`/spots/${spotId}`)
  }
}

// Mouse drag handlers
const handleMouseDown = (e) => {
  isDragging.value = true
  startPos.value = e.clientX
  stopCarousel() // Stop auto-scroll during drag
}

const handleMouseMove = (e) => {
  if (!isDragging.value) return
  e.preventDefault()
  currentPos.value = e.clientX
}

const handleMouseUp = () => {
  if (!isDragging.value) return
  
  const deltaX = currentPos.value - startPos.value
  
  if (Math.abs(deltaX) > dragThreshold) {
    if (deltaX > 0) {
      // Dragged right, go to previous
      previousSpot()
    } else {
      // Dragged left, go to next
      nextSpot()
    }
  }
  
  isDragging.value = false
  startCarousel() // Resume auto-scroll
}

// Touch handlers for mobile
const handleTouchStart = (e) => {
  isDragging.value = true
  startPos.value = e.touches[0].clientX
  stopCarousel()
}

const handleTouchMove = (e) => {
  if (!isDragging.value) return
  currentPos.value = e.touches[0].clientX
}

const handleTouchEnd = () => {
  if (!isDragging.value) return
  
  const deltaX = currentPos.value - startPos.value
  
  if (Math.abs(deltaX) > dragThreshold) {
    if (deltaX > 0) {
      previousSpot()
    } else {
      nextSpot()
    }
  }
  
  isDragging.value = false
  startCarousel()
}

// Add previous function for drag navigation
const previousSpot = () => {
  currentIndex.value = currentIndex.value === 0 
    ? recommendedSpots.value.length - 1 
    : currentIndex.value - 1
}

const mainPrefectures = [
  { name: '東京都', emoji: '🗼', available: true },
  { name: '大阪府', emoji: '🏯', available: true },
  { name: '京都府', emoji: '⛩️', available: true },
  { name: '北海道', emoji: '🐄', available: true }
]

// カテゴリリスト
const categoryList = [
  { name: '寺院', emoji: '⛩️' },
  { name: '歴史建造物', emoji: '🏯' },
  { name: '神社', emoji: '🕊️' },
  { name: '展望台', emoji: '🗼' },
  { name: '博物館', emoji: '🏛️' },
  { name: '観光エリア', emoji: '🌆' }
]

// 地域別都道府県データ（人口順）
const prefectureRegions = [
  {
    name: '北海道・東北地方',
    prefectures: [
      { name: '北海道', emoji: '🐄', available: true },
      { name: '宮城県', emoji: '🌾', available: false },
      { name: '福島県', emoji: '🍑', available: false },
      { name: '青森県', emoji: '🍎', available: false },
      { name: '岩手県', emoji: '⛰️', available: false },
      { name: '山形県', emoji: '🍒', available: false },
      { name: '秋田県', emoji: '🌾', available: false }
    ]
  },
  {
    name: '関東地方',
    prefectures: [
      { name: '東京都', emoji: '🗼', available: true },
      { name: '神奈川県', emoji: '🗻', available: false },
      { name: '埼玉県', emoji: '🌸', available: false },
      { name: '千葉県', emoji: '🌊', available: false },
      { name: '茨城県', emoji: '🥔', available: false },
      { name: '栃木県', emoji: '🍓', available: false },
      { name: '群馬県', emoji: '🏔️', available: false }
    ]
  },
  {
    name: '中部地方',
    prefectures: [
      { name: '愛知県', emoji: '🏭', available: true },
      { name: '静岡県', emoji: '🗻', available: false },
      { name: '新潟県', emoji: '🍚', available: false },
      { name: '長野県', emoji: '🏔️', available: false },
      { name: '岐阜県', emoji: '🏯', available: false },
      { name: '山梨県', emoji: '🍇', available: false },
      { name: '富山県', emoji: '🏔️', available: false },
      { name: '石川県', emoji: '🦀', available: false },
      { name: '福井県', emoji: '🦕', available: false }
    ]
  },
  {
    name: '近畿地方',
    prefectures: [
      { name: '大阪府', emoji: '🏯', available: true },
      { name: '兵庫県', emoji: '🦌', available: false },
      { name: '京都府', emoji: '⛩️', available: true },
      { name: '三重県', emoji: '🦐', available: false },
      { name: '滋賀県', emoji: '🏞️', available: false },
      { name: '奈良県', emoji: '🦌', available: false },
      { name: '和歌山県', emoji: '🍊', available: false }
    ]
  },
  {
    name: '中国地方',
    prefectures: [
      { name: '広島県', emoji: '🕊️', available: false },
      { name: '岡山県', emoji: '🍑', available: false },
      { name: '山口県', emoji: '🌉', available: false },
      { name: '島根県', emoji: '⛩️', available: false },
      { name: '鳥取県', emoji: '🏜️', available: false }
    ]
  },
  {
    name: '四国地方',
    prefectures: [
      { name: '愛媛県', emoji: '🍊', available: false },
      { name: '香川県', emoji: '🍜', available: false },
      { name: '徳島県', emoji: '🌀', available: false },
      { name: '高知県', emoji: '🐟', available: false }
    ]
  },
  {
    name: '九州・沖縄地方',
    prefectures: [
      { name: '福岡県', emoji: '🏮', available: true },
      { name: '熊本県', emoji: '🐻', available: false },
      { name: '鹿児島県', emoji: '🌋', available: false },
      { name: '長崎県', emoji: '⛪', available: false },
      { name: '沖縄県', emoji: '🏖️', available: false },
      { name: '大分県', emoji: '♨️', available: false },
      { name: '宮崎県', emoji: '🌺', available: false },
      { name: '佐賀県', emoji: '🏺', available: false }
    ]
  }
]

const selectPrefecture = async (prefecture) => {
  console.log('Prefecture selected:', prefecture.name)
  
  if (prefecture.available) {
    if (prefecture.name === '東京都') {
      console.log('Navigating to Tokyo guide...')
      try {
        await navigateTo('/tokyo')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('東京ガイドページに移動中です...')
      }
    } else if (prefecture.name === '大阪府') {
      console.log('Navigating to Osaka guide...')
      try {
        await navigateTo('/osaka')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('大阪ガイドページに移動中です...')
      }
    } else if (prefecture.name === '京都府') {
      console.log('Navigating to Kyoto guide...')
      try {
        await navigateTo('/kyoto')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('京都ガイドページに移動中です...')
      }
    } else if (prefecture.name === '北海道') {
      console.log('Navigating to Hokkaido guide...')
      try {
        await navigateTo('/hokkaido')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('北海道ガイドページに移動中です...')
      }
    } else if (prefecture.name === '愛知県') {
      console.log('Navigating to Aichi guide...')
      try {
        await navigateTo('/aichi')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('愛知県ガイドページに移動中です...')
      }
    } else if (prefecture.name === '福岡県') {
      console.log('Navigating to Fukuoka guide...')
      try {
        await navigateTo('/fukuoka')
      } catch (error) {
        console.error('Navigation error:', error)
        alert('福岡県ガイドページに移動中です...')
      }
    }
  } else {
    alert(`${prefecture.name}のガイドは準備中です。しばらくお待ちください。`)
  }
}

// モーダル関連の関数
const closePrefectureModal = (event) => {
  if (event.target === event.currentTarget) {
    showPrefectureModal.value = false
  }
}

const selectPrefectureFromModal = async (prefecture) => {
  showPrefectureModal.value = false
  await selectPrefecture(prefecture)
}

const selectCategory = (category) => {
  console.log('Category selected:', category.name)
  navigateTo(`/category?name=${encodeURIComponent(category.name)}`)
}

// Search functionality
const onSearchInput = () => {
  if (searchQuery.value.trim().length > 0) {
    searchSuggestions.value = allSpots.filter(spot =>
      spot.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      spot.prefecture.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      spot.category.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      spot.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    ).slice(0, 5) // 最大5件まで表示
  } else {
    searchSuggestions.value = []
  }
}

const selectSuggestion = (suggestion) => {
  console.log('Suggestion selected:', suggestion.name)
  navigateTo(`/spots/${suggestion.id}`)
}

const onInputFocus = () => {
  isInputFocused.value = true
  showSuggestions.value = true
}

const onInputBlur = () => {
  // 少し遅延させてクリックイベントが発火するようにする
  setTimeout(() => {
    isInputFocused.value = false
    showSuggestions.value = false
  }, 200)
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    console.log('Performing search for:', searchQuery.value)
    navigateTo(`/search?q=${encodeURIComponent(searchQuery.value.trim())}`)
  }
}

// プレースホルダーの位置を計算
const getPlaceholderStyle = (index) => {
  const currentIndex = currentPlaceholderIndex.value
  const diff = index - currentIndex
  
  if (diff === 0) {
    // 現在表示中
    return {
      transform: 'translateY(0)',
      opacity: '1'
    }
  } else if (diff === 1 || (currentIndex === placeholderSpots.length - 1 && index === 0)) {
    // 次に表示される（下から来る）
    return {
      transform: 'translateY(24px)',
      opacity: '0'
    }
  } else if (diff === -1 || (currentIndex === 0 && index === placeholderSpots.length - 1)) {
    // 前に表示されていた（上に去る）
    return {
      transform: 'translateY(-24px)',
      opacity: '0'
    }
  } else {
    // その他（非表示）
    return {
      transform: 'translateY(24px)',
      opacity: '0'
    }
  }
}

</script>


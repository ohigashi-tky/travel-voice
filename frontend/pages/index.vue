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
              Travel Voice
            </span>
          </h1>
          <p class="text-xl text-gray-600 dark:text-gray-300 font-medium max-w-2xl mx-auto mb-8 tracking-wide transition-colors duration-300">
            音声ガイドで観光を楽しもう
          </p>
        </div>
        
        <div class="max-w-6xl mx-auto">
          <!-- Functional Map Area -->
          <div class="bg-gray-50 dark:bg-gray-800 rounded-3xl p-8 mb-8 border border-gray-200 dark:border-gray-700 transition-colors duration-300">            
            <!-- Prefecture Selection -->
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
          </div>
          
          <!-- Pickup Spots Section -->
          <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 transition-colors duration-300">
            <div class="text-center mb-6">
              <h3 class="text-gray-800 dark:text-white text-xl font-light tracking-wide transition-colors duration-300">ピックアップ</h3>
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
                            <span class="text-gray-500 dark:text-gray-400 text-xs">
                              詳細を見る →
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
        </div>
      </div>
    </div>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />
    
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '~/stores/auth'
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

// Reactive variables
const activeTab = ref('top')
const currentIndex = ref(0)
const recommendedSpots = ref([])
let carouselInterval = null

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
  }
]

// Initialize auth state on mount (middleware already handles authentication)
onMounted(() => {
  // Auth is already initialized by plugin and checked by middleware
  selectRandomSpots()
  startCarousel()
})

onUnmounted(() => {
  stopCarousel()
})

// Select 3 random spots for recommendations
const selectRandomSpots = () => {
  const shuffled = [...allSpots].sort(() => 0.5 - Math.random())
  recommendedSpots.value = shuffled.slice(0, 3)
}

// Auto carousel functions
const startCarousel = () => {
  carouselInterval = setInterval(() => {
    nextSpot()
  }, 5000) // 5秒ごとに切り替え
}

const stopCarousel = () => {
  if (carouselInterval) {
    clearInterval(carouselInterval)
    carouselInterval = null
  }
}

// Carousel navigation (infinite loop)
const nextSpot = () => {
  currentIndex.value = (currentIndex.value + 1) % recommendedSpots.value.length
}

// Navigate to spot detail page
const goToSpotDetail = (spotId) => {
  // Only navigate if not dragging
  if (!isDragging.value) {
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
    }
  } else {
    alert(`${prefecture.name}のガイドは準備中です。しばらくお待ちください。`)
  }
}

</script>
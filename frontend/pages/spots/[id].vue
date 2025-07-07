<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />
    
    <!-- Back Button (always visible) -->
    <!-- <BackButton /> -->

    <!-- Title Section -->
    <div v-if="currentSpot && !isLoading" class="py-6 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-2 mb-2">
          <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-md text-xs">
            {{ currentSpot?.category }}
          </span>
          <span class="text-gray-500 dark:text-gray-400 text-xs">{{ currentSpot?.prefecture }}</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-2">
          {{ currentSpot?.name }}
        </h1>
        <p class="text-gray-600 dark:text-gray-300 text-sm">
          {{ currentSpot?.description }}
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 pb-24">
      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">観光スポット情報を読み込み中...</h3>
          <p class="text-gray-600 dark:text-gray-300">少々お待ちください</p>
        </div>
      </div>

      <!-- Content when loaded -->
      <div class="max-w-4xl mx-auto" v-else-if="currentSpot && !error">
        
        <!-- Images Gallery -->
        <section class="mb-2 px-4">
          
          <!-- Horizontal Scrollable Gallery -->
          <div class="relative">
            <div 
              ref="galleryContainer"
              class="flex gap-4 overflow-x-auto scroll-smooth gallery-scroll"
              @scroll="updateScrollButtons"
            >
              <!-- Google Place Photo -->
              <div class="flex-shrink-0 w-full aspect-video rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                <PlacePhotoImage 
                  :spot-name="currentSpot.name"
                  :place-id="currentSpot.place_id"
                  :alt="`${currentSpot.name} - メイン画像`"
                  image-class="hover:scale-105 transition-transform duration-300"
                />
              </div>
              <!-- Additional images from Google Place Photos -->
              <div 
                v-for="(photo, index) in getGalleryImages()" 
                :key="index"
                class="flex-shrink-0 w-full aspect-video rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300"
              >
                <img 
                  :src="photo.url" 
                  :alt="`${currentSpot.name} - 画像 ${index + 2}`"
                  class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                />
              </div>
            </div>
            
            <!-- Scroll Buttons -->
            <button 
              v-if="showLeftButton"
              @click="scrollLeft"
              class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-full p-2 shadow-lg hover:bg-white/70 dark:hover:bg-gray-800/70 transition-all duration-300"
            >
              <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>
            <button 
              v-if="showRightButton"
              @click="scrollRight"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-full p-2 shadow-lg hover:bg-white/70 dark:hover:bg-gray-800/70 transition-all duration-300"
            >
              <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
          
          <!-- Image Indicators -->
          <div class="flex justify-center mt-4 space-x-2">
            <button
              v-for="(_, index) in getTotalImageCount()"
              :key="index"
              @click="() => { scrollToImage(index); restartAutoScrollAfterDelay(); }"
              :class="[
                'w-2 h-2 rounded-full transition-all duration-300',
                getCurrentImageIndex() === index
                  ? 'bg-blue-600 dark:bg-blue-400'
                  : 'bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500'
              ]"
            />
          </div>
          
        </section>

        <!-- Audio Guide Section -->
        <div class="px-4 sm:px-6 lg:px-8">
          <AudioGuideSimple 
            :spot-id="spotId"
            :spot-name="currentSpot?.name || ''"
          />

          <!-- Combined Overview and History Section -->
          <section class="mb-6">
            <div class="space-y-4">
              <p v-if="currentSpot.overview" class="text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ currentSpot.overview }}
              </p>
              <p v-if="currentSpot.history" class="text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ currentSpot.history }}
              </p>
            </div>
          </section>
        </div>

        <!-- Google Map Section -->
        <section class="mb-8">
          <div class="px-4 sm:px-6 lg:px-8 mb-6">
            <GoogleMapEmbed 
              :spot-name="currentSpot.name" :place-id="currentSpot.place_id"
              :zoom="16"
            />
          </div>
          
          <!-- Unified Access Information Card -->
          <div class="mx-4 sm:mx-6 lg:mx-8 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 dark:border-gray-700">
              <button
                v-if="currentSpot.public_transport && currentSpot.public_transport.length > 0"
                @click="setAccessTab('transport')"
                :class="[
                  'flex-1 px-6 py-4 text-sm font-medium transition-colors duration-200 flex items-center justify-center',
                  accessTab === 'transport' 
                    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                ]"
              >
                電車
              </button>
              <button
                v-if="currentSpot.car_access && currentSpot.car_access.length > 0"
                @click="setAccessTab('car')"
                :class="[
                  'flex-1 px-6 py-4 text-sm font-medium transition-colors duration-200 flex items-center justify-center',
                  accessTab === 'car' 
                    ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-b-2 border-orange-600 dark:border-orange-400' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                ]"
              >
                車
              </button>
              <button
                v-if="currentSpot.parking_info"
                @click="setAccessTab('parking')"
                :class="[
                  'flex-1 px-6 py-4 text-sm font-medium transition-colors duration-200 flex items-center justify-center',
                  accessTab === 'parking' 
                    ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 border-b-2 border-purple-600 dark:border-purple-400' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                ]"
              >
                駐車場
              </button>
              <button
                v-if="currentSpot.walking_info"
                @click="setAccessTab('walking')"
                :class="[
                  'flex-1 px-6 py-4 text-sm font-medium transition-colors duration-200 flex items-center justify-center',
                  accessTab === 'walking' 
                    ? 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 border-b-2 border-green-600 dark:border-green-400' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                ]"
              >
                徒歩
              </button>
            </div>

            <!-- Tab Content with Swipe Support -->
            <div 
              class="relative overflow-hidden"
              @touchstart="handleTouchStart"
              @touchmove="handleTouchMove"
              @touchend="handleTouchEnd"
            >
              <div 
                class="flex transition-transform duration-300 ease-out"
                :style="{ transform: `translateX(-${currentTabIndex * 100}%)` }"
              >
                <!-- Public Transportation Tab -->
                <div 
                  v-if="currentSpot.public_transport && currentSpot.public_transport.length > 0"
                  class="w-full flex-shrink-0 p-4"
                >
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                      <span 
                        v-for="(transport, index) in currentSpot.public_transport" 
                        :key="index"
                        class="block mb-2 last:mb-0"
                      >
                        <span class="font-medium">{{ transport.route }}</span>「{{ transport.station }}」<span class="text-red-600 dark:text-red-400 font-medium">{{ transport.time }}</span>
                      </span>
                    </p>
                  </div>
                </div>

                <!-- Car Access Tab -->
                <div 
                  v-if="currentSpot.car_access && currentSpot.car_access.length > 0"
                  class="w-full flex-shrink-0 p-4"
                >
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                      <span 
                        v-for="(car, index) in currentSpot.car_access" 
                        :key="index"
                        class="block mb-2 last:mb-0"
                      >
                        {{ car.from }}「{{ car.exit }}」<span class="text-red-600 dark:text-red-400 font-medium">{{ car.time }}</span>
                      </span>
                    </p>
                  </div>
                </div>

                <!-- Parking Tab -->
                <div 
                  v-if="currentSpot.parking_info"
                  class="w-full flex-shrink-0 p-4"
                >
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300" v-html="highlightTimes(currentSpot.parking_info)"></p>
                  </div>
                </div>

                <!-- Walking Tab -->
                <div 
                  v-if="currentSpot.walking_info"
                  class="w-full flex-shrink-0 p-4"
                >
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300" v-html="highlightTimes(currentSpot.walking_info)"></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>



      </div>

      <!-- Error State -->
      <div v-else-if="error || (!currentSpot && !isLoading)" class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <MapPin class="w-16 h-16 text-gray-300 mx-auto mb-4" />
          <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">
            {{ error || '観光スポットが見つかりませんでした' }}
          </h3>
          <p class="text-gray-600 dark:text-gray-300 mb-6">
            {{ error ? 'データの読み込みに失敗しました。' : '指定されたスポットは存在しないか、削除された可能性があります。' }}
          </p>
          <button 
            @click="goBack"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          >
            ホームに戻る
          </button>
        </div>
      </div>

    </main>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" :default-open="true" />

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, watch, nextTick } from 'vue'
import { Headphones, Play, Info, Star, Clock, Camera, MapPin } from 'lucide-vue-next'
import { useTouristSpots } from '~/composables/useTouristSpots'
import { useGooglePlacePhotos } from '~/composables/useGooglePlacePhotos'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import AudioGuideSimple from '~/components/AudioGuideSimple.vue'
import PlacePhotoImage from '~/components/PlacePhotoImage.vue'
import GoogleMapEmbed from '~/components/GoogleMapEmbed.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

const route = useRoute()
const config = useRuntimeConfig()
const spotId = computed(() => {
  const id = route.params.id as string
  return parseInt(id)
})

// Set page title after currentSpot is loaded
useHead({
  title: '観光地詳細 - Travel Voice'
})

// Reactive variables
const activeTab = ref('top')
const currentSpot = ref<any>(null)
const isLoading = ref(true)
const error = ref<string | null>(null)

// Access tab management
const accessTab = ref('transport')
const availableTabs = ref<string[]>([])
const currentTabIndex = ref(0)

// Touch/swipe handling
const touchStartX = ref(0)
const touchEndX = ref(0)

// Photo gallery scroll handling
const galleryContainer = ref<HTMLElement | null>(null)
const showLeftButton = ref(false)
const showRightButton = ref(false)
const currentImageIndex = ref(0)
let galleryInterval: NodeJS.Timeout | null = null

// Tourist spots data
const { spots, fetchSpots, getSpotById } = useTouristSpots()

// Google Place Photos for gallery
const { getGalleryPhotos } = useGooglePlacePhotos()
const galleryPhotos = ref<any[]>([])

// Fetch spot details from API
const fetchSpotFromAPI = async (id: number) => {
  try {
    const response = await $fetch(`${config.public.apiBase}/api/tourist-spots/${id}`)
    return response.data || response
  } catch (error) {
    return null
  }
}

// Mock data for development (to be replaced with API)
const allSpots = [
  {
    id: 1,
    name: '東京スカイツリー',
    description: '高さ634mの世界最高クラスの電波塔。展望デッキからは東京の絶景を一望できます。',
    category: '展望台',
    prefecture: '東京都',
    place_id: 'ChIJ35ov0dCOGGARKvdDH7NPHX0',
    imageUrl: '',
    overview: '東京スカイツリーは、東京都墨田区押上にある電波塔で、2012年に開業しました。高さ634mは世界第2位の高さを誇り、東京の新たなランドマークとして親しまれています。展望デッキからは関東平野を一望でき、晴天時には富士山まで見渡すことができます。',
    highlights: ['展望デッキ（350m・450m）', 'スカイツリータウン', 'ライトアップ', 'プラネタリウム'],
    history: '2008年に着工し、2012年に完成。武蔵国の旧国名に因んで634m（ムサシ）の高さに設計されました。建設には最新の制振技術が使われ、日本の伝統的な建築技法も取り入れられています。東京オリンピック・パラリンピックに向けて建設され、現在では年間約600万人の観光客が訪れる東京の新名所となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 2,
    name: '浅草寺',
    description: '東京最古の寺院。雷門と仲見世通りで有名な東京を代表する観光地です。',
    category: '寺院',
    prefecture: '東京都',
    imageUrl: '',
    overview: '浅草寺は628年に創建された東京最古の寺院です。雷門から仲見世通りを通って本堂に至る参道は、常に多くの参拝者と観光客で賑わっています。江戸の下町情緒を今に伝える貴重な場所として、国内外から愛され続けています。',
    highlights: ['雷門', '仲見世通り', '本堂', '五重塔'],
    history: '推古天皇36年（628年）、隅田川で漁をしていた檜前浜成・竹成兄弟の網にかかった観音像を本尊として祀ったのが始まりとされています。江戸時代には徳川家の祈願所として栄え、庶民の信仰を集めました。戦災で多くの建物が焼失しましたが、戦後復興により現在の姿となりました。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 3,
    name: '明治神宮',
    description: '明治天皇と昭憲皇太后を祀る神社。都心にありながら豊かな森に囲まれた神聖な空間です。',
    category: '神社',
    prefecture: '東京都',
    imageUrl: '',
    overview: '明治神宮は1920年に創建された明治天皇と昭憲皇太后を祀る神社です。都心にありながら70万㎡の広大な森に囲まれており、約10万本の木々が植えられています。初詣の参拝者数は全国一を誇り、多くの人々に愛され続けています。',
    highlights: ['本殿', '明治神宮の森', '清正井', '宝物殿'],
    history: '大正9年（1920年）11月1日に創建されました。明治天皇の崩御後、国民の総意により創建が決定され、全国から献木された約10万本の木を植樹して人工の森を造成しました。戦災により多くの建物が焼失しましたが、昭和33年（1958年）に復興されました。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 4,
    name: '銀座',
    description: '高級ブランド店が立ち並ぶ東京の代表的なショッピングエリア。洗練された大人の街として世界的に有名です。',
    category: '観光エリア',
    prefecture: '東京都',
    place_id: 'ChIJu2-DAeeLGGARUZipC7OFRmA',
    imageUrl: '',
    overview: '銀座は東京の中央区にある日本を代表する繁華街です。明治時代から続く老舗店舗と最新のブランドショップが共存し、洗練された大人の街として世界中から注目されています。銀座四丁目交差点を中心とした8つの丁目からなる街並みには、高級デパート、ブティック、ギャラリー、レストランなどが軒を連ね、ショッピングやグルメを楽しむことができます。',
    highlights: ['銀座四丁目交差点', '歌舞伎座', '築地市場（近隣）', '高級ブランド店街'],
    history: '江戸時代に徳川幕府が銀貨鋳造所（銀座）を置いたことが地名の由来です。明治5年（1872年）の銀座大火後、煉瓦街として復興され、文明開化の象徴となりました。大正時代にはカフェ文化が花開き、昭和期には百貨店が次々と開業。現在では世界有数の高級商業地区として発展しています。',
    images: []
  },
  {
    id: 5,
    name: '上野公園',
    description: '桜の名所として有名な都市公園。上野動物園や博物館、美術館が集まる文化の拠点です。',
    category: '公園',
    prefecture: '東京都',
    place_id: 'ChIJw2qQRZuOGGARWmROEiM2y7E',
    imageUrl: '',
    overview: '上野恩賜公園は1873年に開園した日本初の公園の一つです。園内には上野動物園、東京国立博物館、国立西洋美術館、東京都美術館、国立科学博物館など多くの文化施設が集まり、「文化の森」と呼ばれています。春には約1200本の桜が咲き誇る東京屈指の花見スポットとしても親しまれています。',
    highlights: ['上野動物園', '東京国立博物館', '桜の名所', '不忍池'],
    history: '江戸時代は寛永寺の境内でしたが、明治維新後に公園として整備されました。明治6年（1873年）に太政官布達により芝、浅草、深川、飛鳥山とともに日本初の公園に指定されました。その後、帝室博物館（現東京国立博物館）や動物園が開設され、文化と自然が調和した都市公園として発展してきました。',
    images: []
  },
  {
    id: 6,
    name: '渋谷スクランブル交差点',
    description: '世界で最も有名な交差点の一つ。一度に3000人もの人が行き交う東京のシンボル的な光景です。',
    category: '観光エリア',
    prefecture: '東京都',
    place_id: 'ChIJK9EM68qLGGARacmu4KJj5SA',
    imageUrl: '',
    overview: '渋谷スクランブル交差点は渋谷駅ハチ公口前にある世界最大級の歩行者交差点です。信号が変わると一度に約3000人が様々な方向に歩き始める光景は、東京の象徴として世界中に知られています。周辺には109、センター街、スペイン坂など若者文化の発信地が集まり、常に活気に満ちています。',
    highlights: ['スクランブル交差点', 'ハチ公像', '渋谷109', 'センター街'],
    history: '渋谷の発展は明治18年（1885年）の山手線開通から始まりました。戦後復興期の昭和30年代に若者の街として注目され、1990年代にはギャル文化の発信地となりました。スクランブル交差点は1973年に現在の形になり、近年は外国人観光客にも人気の観光スポットとして世界的に有名になっています。',
    images: []
  },
  {
    id: 101,
    name: '大阪城',
    description: '豊臣秀吉が築城した名城。美しい天守閣と桜の名所として親しまれています。',
    category: '歴史建造物',
    prefecture: '大阪府',
    imageUrl: '',
    overview: '大阪城は豊臣秀吉によって1583年に築城された日本の名城の一つです。現在の天守閣は1931年に再建されたもので、大阪のシンボルとして親しまれています。城内には歴史博物館があり、豊臣時代から現代までの大阪の歴史を学ぶことができます。',
    highlights: ['天守閣', '大阪城公園', '桜の名所', '歴史博物館'],
    history: '1583年、豊臣秀吉が石山本願寺の跡地に築城を開始。当時は「三国無双」と称される壮大な城でした。江戸時代には徳川幕府の直轄地となり、明治維新後は陸軍の施設として使用されました。現在の天守閣は昭和6年（1931年）に市民の寄付により復興されたものです。',
    images: [
      '',
      '',
      '',
      '',
      ''
    ]
  },
  {
    id: 201,
    name: '清水寺',
    description: '778年開創の京都最古の寺院。有名な清水の舞台と美しい景色で知られています。',
    category: '寺院',
    prefecture: '京都府',
    imageUrl: '',
    overview: '清水寺は京都東山にある法相宗の寺院で、「清水の舞台」で有名です。1994年にユネスコ世界文化遺産に登録されており、年間を通じて多くの観光客が訪れます。四季折々の美しい景色を楽しむことができ、特に桜と紅葉の季節は格別の美しさです。',
    highlights: ['清水の舞台', '音羽の滝', '地主神社', '三重塔'],
    history: '延暦17年（798年）、坂上田村麻呂によって建立されました。平安時代から「清水詣」として庶民に親しまれ、江戸時代には西国三十三箇所観音霊場の第16番札所として栄えました。現在の本堂は1633年に再建されたもので、釘を一本も使わない日本古来の建築技法で建てられています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 301,
    name: '札幌時計台',
    description: '旧札幌農学校演武場として1878年に建設された北海道のシンボル的建造物です。',
    category: '歴史建造物',
    prefecture: '北海道',
    imageUrl: '',
    overview: '札幌時計台は正式名称を「旧札幌農学校演武場」といい、1878年に建設された北海道の代表的な観光スポットです。現在は重要文化財に指定されており、札幌の発展の歴史を物語る貴重な建造物として大切に保存されています。',
    highlights: ['時計台の鐘', '歴史展示', 'クラーク博士の資料', 'コンサートホール'],
    history: '明治11年（1878年）、札幌農学校（現在の北海道大学）の演武場として建設されました。時計は明治14年に設置され、以来140年以上にわたって札幌の街に時を告げ続けています。クラーク博士の「Boys, be ambitious」の言葉でも有名な札幌農学校の貴重な遺構です。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 202,
    name: '金閣寺',
    description: '足利義満の別荘として建てられた金箔で覆われた美しい楼閣。世界文化遺産です。',
    category: '寺院',
    prefecture: '京都府',
    imageUrl: '',
    overview: '金閣寺（鹿苑寺）は室町幕府三代将軍足利義満の別荘として1397年に建てられました。金箔で覆われた三層の楼閣が池に映る美しい姿は、京都を代表する風景として世界中の人々に愛されています。1994年にユネスコ世界文化遺産に登録されています。',
    highlights: ['金閣（舎利殿）', '鏡湖池', '庭園', '夕佳亭'],
    history: '応永4年（1397年）、足利義満が西園寺家の山荘を譲り受けて「北山殿」を造営。義満の死後、禅寺となり鹿苑寺と名付けられました。昭和25年に放火により焼失しましたが、昭和30年に再建されました。現在の金閣は当時の技術を駆使して忠実に復元されています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 102,
    name: '通天閣',
    description: '新世界のシンボルタワー。ビリケンさんで有名な大阪を代表する観光地です。',
    category: '展望台',
    prefecture: '大阪府',
    imageUrl: '',
    overview: '通天閣は1912年に建設された大阪の象徴的なタワーです。高さ103mの展望台からは大阪市内を一望でき、5階にはビリケンさんが鎮座しています。新世界のランドマークとして多くの観光客に愛され続けており、夜にはネオンでライトアップされて美しい夜景を楽しめます。',
    highlights: ['ビリケンさん', '展望台', '新世界', 'ネオンサイン'],
    history: '明治45年（1912年）に初代通天閣が建設されましたが、戦時中に解体されました。現在の2代目通天閣は昭和31年（1956年）に再建されたもので、大阪復興のシンボルとして親しまれています。足の裏をなでると願いが叶うとされるビリケンさんは、多くの人々に愛され続けています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 103,
    name: '海遊館',
    description: '世界最大級の水族館。ジンベエザメやマンタが泳ぐ太平洋水槽は圧巻です。',
    category: '水族館',
    prefecture: '大阪府',
    imageUrl: '',
    overview: '海遊館は1990年に開館した世界最大級の水族館です。中央にある太平洋水槽では体長4～5mのジンベエザメが悠々と泳ぐ姿を見ることができます。環太平洋の海を再現した14の大水槽では、様々な海洋生物を自然に近い環境で観察することができます。',
    highlights: ['ジンベエザメ', '太平洋水槽', 'ペンギン', 'イルカ'],
    history: '平成2年（1990年）7月20日に開館。天保山ハーバービレッジの中核施設として建設され、開館当時は世界最大級の水族館として注目を集めました。太平洋を中心とした環太平洋火山帯・生命帯の海を再現し、自然環境を忠実に再現した展示で多くの来館者を魅了しています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 104,
    name: '道頓堀',
    description: '大阪の代表的な繁華街。グリコの看板や川沿いのネオンサインで有名な観光エリアです。',
    category: '観光エリア',
    prefecture: '大阪府',
    place_id: 'ChIJzWVthgDgAGARYOk-pwyZ5UU',
    imageUrl: '',
    overview: '道頓堀は大阪ミナミの繁華街の中心で、戎橋から見るグリコの看板は大阪の象徴として世界中に知られています。川沿いには巨大な看板やネオンサインが立ち並び、たこ焼き、お好み焼き、串カツなどの大阪グルメを楽しめる店舗が軒を連ねています。昼夜を問わず多くの観光客で賑わう大阪観光の定番スポットです。',
    highlights: ['グリコの看板', '戎橋', 'かに道楽本店', 'たこ焼き'],
    history: '慶長17年（1612年）、安井道頓が私財を投じて川を開削したことが名前の由来です。江戸時代には芝居小屋が建ち並び、「天下の台所」大阪の娯楽の中心地として発展しました。現在のグリコの看板は1935年に設置され、6代目となる現在の看板は2014年にLED化されました。',
    images: []
  },
  {
    id: 105,
    name: '新世界',
    description: '通天閣を中心とした下町レトロエリア。串カツやお好み焼きなど大阪グルメの聖地です。',
    category: '観光エリア',
    prefecture: '大阪府',
    place_id: 'ChIJX8PVvGLnAGARIh1kJH-aVKM',
    imageUrl: '',
    overview: '新世界は通天閣を中心とした大阪の下町エリアです。明治36年（1903年）に開発された新しい街として「新世界」と名付けられました。現在は昭和レトロな雰囲気が残る観光地として人気で、串カツの名店が多数軒を連ねています。独特の大阪文化を体験できる貴重なエリアとして多くの観光客に愛されています。',
    highlights: ['串カツ店街', '通天閣', 'ビリケンさん', 'レトロな看板'],
    history: '明治36年（1903年）の第5回内国勧業博覧会の会場跡地に開発されました。当時はパリをモデルにした北側とニューヨークをモデルにした南側に分かれており、「東洋一の遊園地」として栄えました。戦後は庶民的な娯楽街として発展し、現在の串カツ文化が根付きました。',
    images: []
  },
  {
    id: 106,
    name: '大阪駅・梅田',
    description: '関西最大の交通ハブ。ショッピング、グルメ、エンターテイメントが集まる西日本の玄関口です。',
    category: '観光エリア',
    prefecture: '大阪府',
    place_id: 'ChIJC6fjlY3mAGARSshZ6CLIrhs',
    imageUrl: '',
    overview: '大阪駅・梅田エリアは関西最大の交通ターミナルであり、西日本最大の繁華街です。JR大阪駅を中心に、阪急、阪神、地下鉄が乗り入れ、1日約250万人が利用します。高層ビルが立ち並び、デパート、ショッピングモール、レストラン、ホテルが集積する関西経済の中心地として機能しています。',
    highlights: ['梅田スカイビル', 'グランフロント大阪', 'ルクア大阪', 'ヨドバシカメラ'],
    history: '明治7年（1874年）に大阪駅が開業。当時は田畑が広がる郊外でしたが、鉄道の発達とともに発展しました。戦後復興期には阪急、阪神の私鉄ターミナルが整備され、現在の巨大ターミナルの基礎が築かれました。21世紀に入ってからの再開発により、さらに現代的な都市空間に生まれ変わっています。',
    images: []
  },
  {
    id: 203,
    name: '伏見稲荷大社',
    description: '全国の稲荷神社の総本宮。千本鳥居の美しい朱色のトンネルで有名です。',
    category: '神社',
    prefecture: '京都府',
    imageUrl: '',
    overview: '伏見稲荷大社は全国に約40,000社ある稲荷神社の総本宮です。稲荷山全体が神域となっており、山頂まで続く数千本の朱色の鳥居は「千本鳥居」と呼ばれ、幻想的な景観を作り出しています。商売繁盛、五穀豊穣の神様として多くの参拝者が訪れます。',
    highlights: ['千本鳥居', '本殿', '稲荷山', '狐の石像'],
    history: '和銅4年（711年）に創建されたと伝えられる古社です。平安時代には朝廷の崇敬を受け、稲荷信仰の中心地として発展しました。江戸時代以降、商売繁盛の神様として庶民の信仰を集め、現在でも初詣の参拝者数は関西地区で最多を誇ります。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 302,
    name: '函館山',
    description: '世界三大夜景の一つに数えられる美しい夜景スポット。津軽海峡を一望できます。',
    category: '展望台',
    prefecture: '北海道',
    imageUrl: '',
    overview: '函館山は標高334mの山で、山頂からの夜景は世界三大夜景の一つとして知られています。函館の街並みと津軽海峡、駒ヶ岳を一望でき、特に日没後の夜景は息をのむ美しさです。ロープウェイや車でアクセスでき、四季を通じて多くの観光客が訪れます。',
    highlights: ['世界三大夜景', 'ロープウェイ', '津軽海峡', '函館の街並み'],
    history: '函館山は古くから函館の象徴として親しまれてきました。明治時代には要塞として利用され、昭和21年（1946年）に一般開放されました。昭和33年（1958年）にロープウェイが開通し、観光地として発展。現在では年間約200万人が訪れる北海道屈指の観光スポットとなっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 303,
    name: '小樽運河',
    description: '1923年完成の歴史ある運河。石造倉庫群とガス灯が織りなすロマンチックな景観です。',
    category: '歴史的景観',
    prefecture: '北海道',
    imageUrl: '',
    overview: '小樽運河は大正12年（1923年）に完成した全長1,140mの運河です。かつては北海道開拓の物資を運ぶ重要な水路でしたが、現在は観光地として整備されています。運河沿いの石造倉庫群とガス灯が作り出すノスタルジックな景観は、小樽を代表する風景として多くの人に愛されています。',
    highlights: ['石造倉庫群', 'ガス灯', '運河クルーズ', 'レンガ倉庫'],
    history: '大正12年（1923年）に小樽港の発展とともに建設されました。昭和61年（1986年）に観光地として整備され、石造倉庫はレストランやショップに生まれ変わりました。平成11年（1999年）には小樽雪あかりの路が始まり、冬の風物詩として定着しています。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 広島県
  {
    id: 601,
    name: '原爆ドーム',
    description: '平和の象徴として世界中に知られる広島の代表的なランドマーク。ユネスコ世界文化遺産です。',
    category: '歴史建造物',
    prefecture: '広島県',
    imageUrl: '',
    overview: '原爆ドームは1945年8月6日の原子爆弾投下により被爆した建物で、現在は平和の象徴として保存されています。1996年にユネスコ世界文化遺産に登録され、核兵器の恐ろしさと平和の大切さを後世に伝える重要な建造物です。平和記念公園内にあり、多くの人々が平和への祈りを込めて訪れます。',
    highlights: ['世界文化遺産', '平和記念公園', '慰霊碑', '平和の灯'],
    history: '大正4年（1915年）に広島県物産陳列館として建設され、爆心地から約160m離れた場所で被爆しました。戦後、原爆の惨禍を伝える象徴として保存が決定され、昭和41年（1966年）に「原爆ドーム」と呼ばれるようになりました。現在は平和への願いを込めた重要な記念建造物として大切に保存されています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 602,
    name: '厳島神社',
    description: '海に浮かぶ朱色の大鳥居で有名な日本三景の一つ。満潮時の美しさは格別です。',
    category: '神社',
    prefecture: '広島県',
    imageUrl: '',
    overview: '厳島神社は宮島にある神社で、海に浮かぶ朱色の大鳥居で有名です。日本三景の一つに数えられ、満潮時には神社全体が海に浮かんでいるように見える幻想的な景観を楽しめます。1996年にユネスコ世界文化遺産に登録され、年間約400万人の参拝者が訪れます。',
    highlights: ['海上大鳥居', '本殿', '能舞台', '宝物館'],
    history: '推古天皇元年（593年）に創建されたと伝えられる古社です。平安時代に平清盛が現在の規模に整備し、平家の氏神として崇敬されました。鎌倉時代以降も武家や皇室の信仰を集め、江戸時代には庶民の参詣も盛んになりました。現在の社殿は明治8年（1875年）に再建されたものです。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 603,
    name: '広島城',
    description: '毛利輝元が築いた名城。現在は歴史博物館として広島の歴史を学ぶことができます。',
    category: '歴史建造物',
    prefecture: '広島県',
    imageUrl: '',
    overview: '広島城は毛利輝元によって築城された平城で、別名「鯉城」とも呼ばれます。天守閣は戦災で焼失しましたが、昭和33年（1958年）に復元され、現在は広島市郷土資料館として利用されています。城内では広島の歴史や文化を学ぶことができ、桜の名所としても親しまれています。',
    highlights: ['復元天守閣', '二の丸', '護国神社', '桜の名所'],
    history: '天正17年（1589年）に毛利輝元が築城を開始し、慶長4年（1599年）に完成しました。関ヶ原の戦い後は福島正則、浅野氏が城主となり、明治維新まで浅野氏の居城でした。原爆により天守閣等が焼失しましたが、昭和33年に天守閣が復元され、現在は広島の歴史を伝える博物館として活用されています。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 愛知県
  {
    id: 401,
    name: '名古屋城',
    description: '徳川家康が築城した名古屋のシンボル。金の鯱鉾で有名な日本三大名城の一つです。',
    category: '歴史建造物',
    prefecture: '愛知県',
    imageUrl: '',
    overview: '名古屋城は1610年に徳川家康の命により築城された平城です。天守閣の金の鯱鉾は名古屋の象徴として親しまれ、日本三大名城の一つに数えられています。現在は復元された本丸御殿も見どころの一つとなっており、豪華絢爛な障壁画や彫刻を間近で見ることができます。',
    highlights: ['金の鯱鉾', '本丸御殿', '西南隅櫓', '名古屋城桜まつり'],
    history: '慶長15年（1610年）、徳川家康の九男義直の居城として築城開始。当時は東海道の要衝として重要な役割を果たしました。明治維新後は名古屋離宮となり、戦災で焼失後、1959年に鉄筋コンクリート造で再建されました。2018年には本丸御殿が完全復元され、江戸時代の匠の技を現代に蘇らせています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 402,
    name: '熱田神宮',
    description: '三種の神器の一つ草薙剣を祀る由緒ある神社。1900年の歴史を誇る格式高い神宮です。',
    category: '神社',
    prefecture: '愛知県',
    imageUrl: '',
    overview: '熱田神宮は三種の神器の一つである草薙剣を御神体とする神社で、伊勢神宮に次ぐ格式を誇ります。約1900年の歴史を持つ由緒ある神宮で、年間約700万人の参拝者が訪れます。境内には約6万坪の広大な森があり、都市部にありながら神聖な雰囲気に包まれています。',
    highlights: ['草薙剣', '宝物館', '熱田まつり', '信長塀'],
    history: '景行天皇43年、日本武尊が草薙剣を留め置かれたのが起源とされています。織田信長が桶狭間の戦いの前に戦勝祈願をしたことでも有名で、勝利後に信長塀を奉納しました。江戸時代には東海道の宮宿として栄え、現在でも「熱田さん」として地元の人々に親しまれています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 403,
    name: 'トヨタ産業技術記念館',
    description: 'トヨタグループ発祥の地に建つ産業技術博物館。自動車産業の歴史と技術を学べます。',
    category: '博物館',
    prefecture: '愛知県',
    imageUrl: '',
    overview: 'トヨタ産業技術記念館は、トヨタグループ発祥の地に建つ産業博物館です。繊維機械から自動車への発展過程と、ものづくりの技術を体験できます。館内では実際に動く機械の実演を見ることができ、日本の産業技術の発展を間近で感じることができる貴重な施設です。',
    highlights: ['繊維機械館', '自動車館', 'テクノランド', '実演展示'],
    history: '1994年に開館。豊田佐吉が発明した自動織機から始まり、トヨタ自動車の発展まで、日本の産業技術の歩みを展示しています。豊田佐吉の「研究と創造の精神」、豊田喜一郎の「お客様第一、現地現物」の理念を受け継ぎ、ものづくりの大切さを伝え続けています。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 福岡県
  {
    id: 501,
    name: '太宰府天満宮',
    description: '学問の神様菅原道真公を祀る由緒ある神社。受験合格や学業成就を願う多くの参拝者が訪れます。',
    category: '神社',
    prefecture: '福岡県',
    imageUrl: '',
    overview: '太宰府天満宮は学問の神様として親しまれる菅原道真公を祀る神社です。全国約12,000社の天満宮の総本宮として多くの参拝者が訪れます。境内には約6,000本の梅の木があり、2月上旬から3月上旬にかけて美しい梅の花を楽しむことができます。',
    highlights: ['本殿', '飛梅', '宝物殿', '天満宮博物館'],
    history: '延喜3年（903年）に菅原道真公が薨去された後、919年に社殿が建立されました。学問・至誠・厄除けの神様として全国から信仰を集めており、特に受験シーズンには多くの学生や保護者が合格祈願に訪れます。境内の飛梅は道真公を慕って京都から一夜で飛んできたという伝説があります。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 502,
    name: '福岡城跡',
    description: '黒田長政が築城した福岡藩の居城跡。現在は舞鶴公園として桜の名所にもなっています。',
    category: '歴史建造物',
    prefecture: '福岡県',
    imageUrl: '',
    overview: '福岡城は黒田長政によって築城された福岡藩52万石の居城跡です。現在は舞鶴公園として整備され、桜の名所としても親しまれています。春には約1,000本の桜が咲き誇り、多くの花見客で賑わいます。天守台からは福岡市街地を一望することができます。',
    highlights: ['天守台', '多聞櫓', '舞鶴公園', '桜の名所'],
    history: '慶長6年（1601年）から7年をかけて黒田長政が築城。関ヶ原の戦いでの功績により筑前国を与えられた黒田氏の居城となりました。明治時代に廃城となりましたが、石垣や櫓などの遺構が残り、福岡市のシンボルとなっています。現在は国の史跡に指定されています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 503,
    name: '博多駅',
    description: '九州の玄関口として知られる福岡の中心駅。周辺にはグルメや買い物スポットが充実しています。',
    category: '観光エリア',
    prefecture: '福岡県',
    imageUrl: '',
    overview: '博多駅は九州最大のターミナル駅で、新幹線から在来線まで多くの路線が乗り入れています。駅周辺は商業施設やグルメスポットが充実した福岡の中心地です。博多ラーメンをはじめとする福岡グルメを楽しめる店舗が数多くあり、観光の拠点としても最適です。',
    highlights: ['JR博多シティ', 'キャナルシティ博多', '博多グルメ', '屋台街'],
    history: '1889年に開業。九州の玄関口として発展し、2011年に九州新幹線が全線開通したことで、さらに重要な交通拠点となりました。2011年には新しい駅ビル「JR博多シティ」がオープンし、ショッピングやグルメを楽しめる複合施設として多くの人々に利用されています。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 福島県
  {
    id: 701,
    name: '会津若松城',
    description: '戊辰戦争で有名な名城。白虎隊の悲話で知られる会津の象徴的な城郭です。',
    category: '歴史建造物',
    prefecture: '福島県',
    imageUrl: '',
    overview: '会津若松城は1384年に築城された会津地方の象徴的な城郭です。戊辰戦争では新政府軍に対して約1ヶ月間籠城し、白虎隊の悲話でも知られています。現在の天守閣は1965年に復元されたもので、会津の歴史と文化を学ぶ博物館として利用されています。',
    highlights: ['天守閣', '白虎隊', '戊辰戦争', '桜の名所'],
    history: '至徳元年（1384年）に蘆名直盛によって黒川城として築城されました。天正18年（1590年）に蒲生氏郷が入城し、七層の天守を築いて若松城と改称。慶長3年（1598年）には上杉景勝が城主となり、その後加藤嘉明、保科正之を経て松平氏（会津松平家）の居城となりました。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 702,
    name: '五色沼',
    description: '磐梯山の噴火によって生まれた美しい湖沼群。エメラルドグリーンの湖面が神秘的です。',
    category: '自然',
    prefecture: '福島県',
    imageUrl: '',
    overview: '五色沼は磐梯山の1888年の噴火によって生まれた湖沼群の総称です。約3.6kmの自然探勝路では、毘沙門沼、赤沼、みどろ沼、竜沼、弁天沼などを巡ることができます。季節や時間、角度によって湖面の色が変化する神秘的な景観で知られています。',
    highlights: ['五色の湖', '磐梯山', 'トレッキング', '紅葉'],
    history: '明治21年（1888年）7月15日の磐梯山噴火により形成された湖沼群です。噴火による山体崩壊で川がせき止められ、大小300余りの湖沼が生まれました。昭和25年（1950年）に磐梯朝日国立公園に指定され、現在では年間約200万人の観光客が訪れる福島県の代表的な観光地となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 703,
    name: 'いわき湯本温泉',
    description: '1200年の歴史を誇る古湯。常磐地方の代表的な温泉地として親しまれています。',
    category: '温泉',
    prefecture: '福島県',
    imageUrl: '',
    overview: 'いわき湯本温泉は約1200年の歴史を持つ古湯で、常磐地方最大の温泉郷です。毎分5トンの豊富な湧出量を誇り、含硫黄・ナトリウム・塩化物・硫酸塩温泉の泉質で美肌効果があるとされています。スパリゾートハワイアンズの源泉としても知られています。',
    highlights: ['古湯', '湯本温泉', 'スパリゾート', '温泉街'],
    history: '平安時代初期の806年（大同元年）に発見されたと伝えられています。江戸時代には湯治場として栄え、明治時代には常磐炭田の発展とともに温泉地として整備されました。昭和41年（1966年）にスパリゾートハワイアンズ（当時は常磐ハワイアンセンター）が開業し、全国的に有名な温泉地となりました。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 704,
    name: '大内宿',
    description: '江戸時代の宿場町が保存された重要伝統的建造物群保存地区。茅葺き屋根の町並みが美しい。',
    category: '歴史的景観',
    prefecture: '福島県',
    imageUrl: '',
    overview: '大内宿は江戸時代に会津西街道の宿場町として栄えた集落です。現在も茅葺き屋根の民家が約50軒立ち並び、昭和56年（1981年）に重要伝統的建造物群保存地区に選定されました。江戸時代の宿場町の面影を色濃く残す貴重な文化遺産として多くの観光客が訪れます。',
    highlights: ['茅葺き屋根', '宿場町', '伝統建造物', 'ねぎそば'],
    history: '寛永20年（1643年）に会津藩が参勤交代の道として会津西街道を整備した際に宿場町として発展しました。明治時代に鉄道が開通すると交通の要衝としての役割は失いましたが、住民の努力により江戸時代の町並みが保存され、現在では年間約100万人が訪れる観光地となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 705,
    name: 'あぶくま洞',
    description: '東洋一の鍾乳洞として知られる神秘的な地底世界。美しい石筍や石柱が幻想的です。',
    category: '自然',
    prefecture: '福島県',
    imageUrl: '',
    overview: 'あぶくま洞は約8000万年という歳月をかけて形成された東洋一とも言われる鍾乳洞です。全長約3300mの洞内には、様々な形の鍾乳石や石筍、石柱が発達しており、幻想的な地底世界を体験できます。年間を通して洞内温度は約15度に保たれているため、四季を問わず楽しめます。',
    highlights: ['鍾乳洞', '石筍', '地底世界', 'ライトアップ'],
    history: '昭和44年（1969年）に発見され、昭和48年（1973年）に一般公開が開始されました。約8000万年前に形成が始まったとされ、石灰岩が地下水によって浸食されて形成された天然の芸術作品です。洞内の最大高は約30m、最大幅は約15mで、現在約600mが観光コースとして整備されています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 706,
    name: 'スパリゾートハワイアンズ',
    description: '温泉をテーマにした大型レジャー施設。フラダンスショーと温泉が楽しめます。',
    category: '観光エリア',
    prefecture: '福島県',
    imageUrl: '',
    overview: 'スパリゾートハワイアンズは常磐炭田の閉山に伴い、地域復興の一環として開設された日本初の本格的な温泉テーマパークです。映画「フラガール」の舞台としても有名で、豊富な温泉とフラダンスショー、ウォーターパークなどが楽しめる総合レジャー施設です。',
    highlights: ['フラダンス', '温泉', 'ウォータースライダー', 'ショー'],
    history: '昭和41年（1966年）に常磐ハワイアンセンターとして開業しました。常磐炭田の閉山により地域経済が低迷する中、豊富な温泉資源を活用した観光業への転換を図る画期的な取り組みでした。平成18年（2006年）の映画「フラガール」で全国的に注目され、現在では年間約130万人が訪れる東北有数の観光施設となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 愛媛県
  {
    id: 801,
    name: '道後温泉',
    description: '日本最古の温泉の一つ。千と千尋の神隠しのモデルとしても有名な歴史ある温泉地です。',
    category: '温泉',
    prefecture: '愛媛県',
    imageUrl: '',
    overview: '道後温泉は約3000年の歴史を持つ日本最古の温泉の一つです。道後温泉本館は明治27年に建設された歴史ある建物で、ジブリ映画「千と千尋の神隠し」の油屋のモデルになったことでも有名です。アルカリ性単純温泉の湯質で、美肌効果があるとされています。',
    highlights: ['道後温泉本館', '湯神社', '道後公園', '坊っちゃん列車'],
    history: '約3000年前から湧き出ていたとされ、聖徳太子や舒明天皇も入浴したという記録が残っています。夏目漱石の小説「坊っちゃん」の舞台としても有名で、現在の本館は明治27年（1894年）に改築されました。国の重要文化財に指定されており、現在も多くの人々に愛され続けています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 802,
    name: '松山城',
    description: '松山市の中心部、勝山山頂に建つ現存12天守の一つ。美しい桜の名所としても知られています。',
    category: '歴史建造物',
    prefecture: '愛媛県',
    imageUrl: '',
    overview: '松山城は加藤嘉明によって1602年から築城が開始され、現存12天守の一つとして国の重要文化財に指定されています。標高132mの勝山山頂に位置し、松山平野を一望できる美しい眺望が楽しめます。春には約200本の桜が咲き誇る桜の名所としても有名です。',
    highlights: ['現存天守', '本丸', 'ロープウェイ', '桜の名所'],
    history: '慶長7年（1602年）に加藤嘉明によって築城が開始され、約25年の歳月をかけて完成しました。江戸時代には松平氏が城主となり、明治維新まで続きました。現在の天守は1854年に再建されたもので、日本に現存する12の木造天守の一つとして貴重な文化財となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 803,
    name: '今治城',
    description: '瀬戸内海に面した水城として築かれた美しい城。藤堂高虎が築城した海岸平城です。',
    category: '歴史建造物',
    prefecture: '愛媛県',
    imageUrl: '',
    overview: '今治城は慶長9年（1604年）に築城の名手として知られる藤堂高虎によって築かれた海岸平城です。日本三大水城の一つとして数えられ、海水を堀に引き込んだ珍しい構造を持っています。現在の天守は昭和55年（1980年）に復元されたもので、瀬戸内海の美しい景色を一望できます。',
    highlights: ['水城', '天守閣', '石垣', '瀬戸内海'],
    history: '慶長9年（1604年）、藤堂高虎によって築城されました。高虎は築城の名手として知られ、今治城では海水を堀に引き込む革新的な設計を採用しました。江戸時代には久松松平家の居城となり、明治維新後は廃城となりましたが、現在は復元され今治市のシンボルとなっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 804,
    name: '内子町',
    description: '江戸時代から明治時代の町並みが美しく保存された重要伝統的建造物群保存地区です。',
    category: '歴史的景観',
    prefecture: '愛媛県',
    imageUrl: '',
    overview: '内子町は江戸時代後期から明治時代にかけて木蝋の生産で栄えた町です。重要伝統的建造物群保存地区に指定されており、白壁と格子戸の美しい町並みが約600mにわたって続いています。内子座や大村家住宅など、歴史ある建造物が数多く保存されています。',
    highlights: ['伝統的建造物群', '内子座', '大村家住宅', '和ろうそく'],
    history: '江戸時代後期から大正時代にかけて、木蝋（ハゼの実から作る蝋）の生産で大いに栄えました。昭和57年（1982年）に重要伝統的建造物群保存地区に選定され、美しい町並みが保存されています。現在も伝統工芸の和ろうそく作りが受け継がれており、歴史と文化が息づく町として多くの観光客が訪れます。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 805,
    name: '石鎚山',
    description: '西日本最高峰の霊峰。四季折々の美しい自然と登山が楽しめる愛媛県のシンボルです。',
    category: '自然',
    prefecture: '愛媛県',
    imageUrl: '',
    overview: '石鎚山は標高1982mの西日本最高峰で、日本七霊山の一つとして古くから信仰の対象となっています。愛媛県と高知県の境に位置し、四季折々の美しい自然景観が楽しめます。春の新緑、夏の高山植物、秋の紅葉、冬の樹氷と、年間を通して登山者や観光客に愛されています。',
    highlights: ['西日本最高峰', '霊峰', '紅葉', 'ロープウェイ'],
    history: '古くから山岳信仰の霊山として崇拝され、修験道の修行場としても利用されてきました。石鎚神社の本社が山頂付近にあり、毎年7月1日から10日まで夏季大祭が行われます。昭和30年（1955年）に石鎚国定公園に指定され、現在は多くの登山愛好家や自然愛好家が訪れる四国を代表する山となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 806,
    name: 'しまなみ海道',
    description: '本州と四国を結ぶ美しい橋の連続。サイクリングの聖地として世界的に有名です。',
    category: '観光エリア',
    prefecture: '愛媛県',
    imageUrl: '',
    overview: 'しまなみ海道は尾道市と今治市を結ぶ全長約60kmの自動車専用道路で、瀬戸内海の美しい島々を橋で結んでいます。6つの島を経由し、特に来島海峡大橋からの眺望は絶景です。世界的なサイクリングコースとしても有名で、「サイクリストの聖地」と呼ばれています。',
    highlights: ['サイクリング', '来島海峡大橋', '瀬戸内海', '島めぐり'],
    history: '平成11年（1999年）に全線開通した西瀬戸自動車道の愛称です。本州四国連絡橋の一つとして建設され、尾道と今治を結ぶ重要な交通路となっています。開通後はサイクリングコースとしても整備され、国内外から多くのサイクリストが訪れる観光地となりました。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 埼玉県
  {
    id: 901,
    name: '川越',
    description: '小江戸と呼ばれる蔵造りの町並み。江戸の情緒を残す歴史ある観光地です。',
    category: '歴史的景観',
    prefecture: '埼玉県',
    imageUrl: '',
    overview: '川越は江戸時代から商業都市として栄えた城下町で、現在でも蔵造りの建物が約30棟残っています。菓子屋横丁や時の鐘など、江戸情緒あふれる町並みが「小江戸」と呼ばれる由縁です。重要伝統的建造物群保存地区に指定されており、歴史と文化が息づく観光地として多くの人に愛されています。',
    highlights: ['蔵造り', '菓子屋横丁', '時の鐘', '小江戸'],
    history: '平安時代に河越館が築かれ、江戸時代には川越藩の城下町として発展しました。新河岸川舟運により江戸との交易が盛んになり、商業都市として繁栄。明治時代の川越大火後に建設された蔵造りの建物群が現在の町並みの基礎となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 902,
    name: '秩父神社',
    description: '2000年以上の歴史を持つ古社。秩父夜祭で有名な関東屈指のパワースポットです。',
    category: '神社',
    prefecture: '埼玉県',
    imageUrl: '',
    overview: '秩父神社は2000年以上の歴史を持つ古社で、秩父地方の総鎮守として崇敬されています。毎年12月2日・3日に行われる秩父夜祭は日本三大曳山祭の一つで、ユネスコ無形文化遺産に登録されています。左甚五郎作と伝わる見事な彫刻でも知られています。',
    highlights: ['秩父夜祭', 'パワースポット', '左甚五郎', '彫刻'],
    history: '崇神天皇の代に知知夫彦命が祖神を祀ったのが始まりとされています。平安時代初期に現在地に遷座し、鎌倉時代には関東総鎮守として崇敬されました。現在の社殿は天正20年（1592年）に徳川家康が再建したもので、江戸時代の建築技術の粋を集めた美しい建物です。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 903,
    name: '長瀞渓谷',
    description: '荒川が作り出した美しい渓谷美。ライン下りと岩畳で有名な景勝地です。',
    category: '自然',
    prefecture: '埼玉県',
    imageUrl: '',
    overview: '長瀞渓谷は荒川上流部の渓谷で、結晶片岩の岩石が作り出す美しい景観で知られています。「岩畳」と呼ばれる平たい岩盤と、川下りのライン下りが有名です。国指定の名勝・天然記念物に指定されており、秋の紅葉時期は特に美しい景色を楽しめます。',
    highlights: ['ライン下り', '岩畳', '紅葉', '渓谷美'],
    history: '長瀞の地名は「長い淵」を意味し、古くから景勝地として知られていました。明治時代に東京地学協会会長の地質学者によって「地球の窓」と称され、地質学的にも貴重な場所として注目されました。昭和3年（1928年）に国の名勝・天然記念物に指定されています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 904,
    name: '武蔵一宮氷川神社',
    description: '関東の氷川神社の総本社。2400年の歴史を誇る格式高い神社です。',
    category: '神社',
    prefecture: '埼玉県',
    imageUrl: '',
    overview: '武蔵一宮氷川神社は2400年以上の歴史を持つ古社で、関東の氷川神社約280社の総本社です。約2kmの参道は日本一長い参道として知られています。素戔嗚尊を主祭神とし、厄除け・縁結び・家内安全のご利益があるとされています。',
    highlights: ['総本社', '参道', '楼門', '氷川神社'],
    history: '第5代孝昭天皇の代（紀元前473年）に出雲族の兄多毛比命が氷川神社を創建したのが始まりとされています。朝廷や武家の信仰を集め、特に源頼朝や足利氏、徳川将軍家から篤い崇敬を受けました。現在の社殿は明治6年（1873年）に造営されたものです。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 905,
    name: '鉄道博物館',
    description: '日本最大級の鉄道博物館。実物の車両展示と体験型展示が充実しています。',
    category: '博物館',
    prefecture: '埼玉県',
    imageUrl: '',
    overview: '鉄道博物館は2007年に開館したJR東日本の企業博物館です。蒸気機関車から新幹線まで実物車両36両を展示し、運転シミュレータや巨大なジオラマなど体験型展示が充実しています。鉄道の歴史と技術を楽しく学べる施設として人気を集めています。',
    highlights: ['車両展示', 'ジオラマ', 'シミュレータ', '体験型'],
    history: '2007年10月14日の鉄道の日に開館。JR東日本の創立20周年記念事業として建設されました。前身である交通博物館の収蔵品を引き継ぎ、日本の鉄道技術の発展を包括的に展示する施設として設立されました。年間約80万人が訪れる人気博物館となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 906,
    name: '芝桜の丘',
    description: '羊山公園の芝桜が織りなす美しいカーペット。春の絶景スポットとして人気です。',
    category: '自然',
    prefecture: '埼玉県',
    imageUrl: '',
    overview: '羊山公園の芝桜の丘は約17,600㎡の敷地に9種類、約40万株の芝桜が植栽された関東でも有数の芝桜の名所です。4月中旬から5月上旬にかけて、ピンクや白、紫の芝桜が花のカーペットを織りなし、武甲山を背景とした美しい景観を楽しめます。',
    highlights: ['芝桜', '羊山公園', '春の絶景', 'カーペット'],
    history: '2000年から芝桜の植栽が始まり、秩父市の新しい観光名所として整備されました。羊山公園は昭和初期まで県営牧場として羊を放牧していたことからその名がつきました。現在では芝桜まつり期間中に約50万人の観光客が訪れる埼玉県有数の観光スポットとなっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 新潟県
  {
    id: 1001,
    name: '佐渡島',
    description: '日本海に浮かぶ神秘の島。金山の歴史と豊かな自然、伝統芸能が息づく島です。',
    category: '自然',
    prefecture: '新潟県',
    imageUrl: '',
    overview: '佐渡島は日本海最大の島で、江戸時代の佐渡金山で栄えた歴史と豊かな自然が魅力です。トキの生息地としても有名で、能楽や太鼓などの伝統芸能も根付いています。四季を通じて美しい自然景観と独特の文化を楽しめる島です。',
    highlights: ['佐渡金山', 'トキの森公園', '能楽', '棚田'],
    history: '平安時代から流刑地として利用され、江戸時代には佐渡金山の発見により天領として栄えました。明治時代以降は農業と漁業が中心となり、昭和27年（1952年）に国定公園に指定されました。現在はトキの野生復帰事業でも注目を集めています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1002,
    name: '越後湯沢',
    description: '川端康成「雪国」の舞台。温泉と雪景色、スキーリゾートとして親しまれています。',
    category: '温泉',
    prefecture: '新潟県',
    imageUrl: '',
    overview: '越後湯沢は川端康成の小説「雪国」の舞台として有名な温泉地です。豊富な雪に恵まれた日本有数のスキーリゾートでもあり、新幹線でのアクセスも良好です。温泉と雪景色、そして日本酒の文化が楽しめる魅力的な観光地です。',
    highlights: ['雪国', '温泉', 'スキー場', '雪景色'],
    history: '江戸時代から湯治場として栄え、明治時代に鉄道が開通すると東京からの避暑地として発展しました。昭和6年（1931年）に川端康成が「雪国」を執筆し、全国的に有名になりました。現在は年間約400万人が訪れる一大リゾート地となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1003,
    name: '弥彦神社',
    description: '越後一宮として古くから信仰を集める神社。弥彦山とともに新潟県を代表する霊地です。',
    category: '神社',
    prefecture: '新潟県',
    imageUrl: '',
    overview: '弥彦神社は越後国一宮として2400年以上の歴史を誇る古社です。天香山命を祭神とし、越後開拓の祖神として崇敬されています。弥彦山の麓に鎮座し、四季の美しい自然とともに多くの参拝者が訪れる新潟県屈指のパワースポットです。',
    highlights: ['越後一宮', '弥彦山', '紅葉', 'パワースポット'],
    history: '神武天皇の時代に天香山命が越後国開拓のために派遣され、その功績を称えて創建されたと伝えられています。万葉集にも詠まれた古い歴史を持ち、現在の拝殿は大正5年（1916年）、本殿は昭和33年（1958年）に再建されました。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1004,
    name: '清津峡',
    description: '日本三大峡谷の一つ。トンネル内のアート展示と美しい渓谷美で話題の絶景スポットです。',
    category: '自然',
    prefecture: '新潟県',
    imageUrl: '',
    overview: '清津峡は信濃川の支流である清津川が形成した峡谷で、黒部峡谷・大杉谷とともに日本三大峡谷の一つに数えられています。2018年にリニューアルされた清津峡渓谷トンネルでは、現代アートと自然が融合した美しい空間を体験できます。',
    highlights: ['三大峡谷', 'アート', 'トンネル', '絶景'],
    history: '昭和24年（1949年）に上信越高原国立公園に指定され、昭和63年（1988年）に清津峡渓谷トンネルが開通しました。平成30年（2018年）に大地の芸術祭の一環としてアート作品「Tunnel of Light」が設置され、新たな観光スポットとして注目を集めています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1005,
    name: '高田城跡',
    description: '徳川家康の六男松平忠輝の居城跡。桜の名所として日本三大夜桜の一つに数えられます。',
    category: '歴史建造物',
    prefecture: '新潟県',
    imageUrl: '',
    overview: '高田城は慶長19年（1614年）に松平忠輝の居城として築城された平城です。現在は高田公園として整備され、約4000本の桜が植えられた新潟県屈指の桜の名所となっています。夜桜は弘前、上野と並んで日本三大夜桜の一つとされています。',
    highlights: ['三大夜桜', '桜の名所', '城跡', 'ライトアップ'],
    history: '慶長19年（1614年）に徳川家康の六男松平忠輝の居城として築城されました。明治時代に廃城となりましたが、昭和46年（1971年）に三重櫓が復元されました。現在は高田公園として市民に親しまれ、春の観桜会には約100万人の観光客が訪れます。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1006,
    name: '新潟市水族館マリンピア日本海',
    description: '日本海側最大級の水族館。イルカショーと日本海の豊かな海洋生物を楽しめます。',
    category: '水族館',
    prefecture: '新潟県',
    imageUrl: '',
    overview: '新潟市水族館マリンピア日本海は1990年に開館した日本海側最大級の水族館です。約500種2万点の海洋生物を展示し、特にイルカショーとラッコの展示で人気を集めています。日本海の豊かな海洋生物を中心とした展示が特色です。',
    highlights: ['イルカショー', '日本海', '水族館', '海洋生物'],
    history: '平成2年（1990年）7月に開館し、平成25年（2013年）にリニューアルオープンしました。日本海に面した立地を活かし、地元の海洋生物を中心とした展示を行っています。年間約70万人が訪れる新潟県の代表的な観光施設となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 山口県
  {
    id: 1101,
    name: '錦帯橋',
    description: '日本三名橋の一つ。五連の木造アーチ橋が美しい岩国市の象徴的な橋です。',
    category: '歴史建造物',
    prefecture: '山口県',
    imageUrl: '',
    overview: '錦帯橋は1673年に岩国藩主吉川広嘉によって建造された日本三名橋の一つです。錦川に架かる五連のアーチが美しい木造の橋で、釘を一本も使わない伝統的な組木の技法により建設されています。',
    highlights: ['三名橋', '木造アーチ', '桜の名所', '岩国城'],
    history: '延宝元年（1673年）、岩国藩3代藩主吉川広嘉によって架橋されました。台風や洪水で何度も流失しましたが、その都度再建され、現在の橋は1953年に再建されたものです。独特の5連アーチ構造は世界的にも珍しく、日本の木造技術の粋を集めた傑作です。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1102,
    name: '秋芳洞',
    description: '日本最大級の鍾乳洞。神秘的な地下世界と美しい石灰岩の造形美が楽しめます。',
    category: '自然',
    prefecture: '山口県',
    imageUrl: '',
    overview: '秋芳洞は山口県美祢市にある日本最大級の鍾乳洞です。総延長は約10kmで、うち約1kmが一般公開されています。洞内の気温は年間を通じて17度と一定で、百枚皿や黄金柱などの見事な石灰岩の造形美を鑑賞できます。',
    highlights: ['鍾乳洞', '秋吉台', '石灰岩', '地下世界'],
    history: '約3億年前のサンゴ礁が石灰岩となり、長い年月をかけて地下水によって溶食されて形成された鍾乳洞です。1922年に天然記念物に指定され、1955年に特別天然記念物となりました。地上の秋吉台と一体となって、地球の歴史を物語る貴重な自然遺産です。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1103,
    name: '萩城下町',
    description: '明治維新の舞台となった武家屋敷群。幕末の志士たちが歩いた歴史ある町並みです。',
    category: '歴史的景観',
    prefecture: '山口県',
    imageUrl: '',
    overview: '萩城下町は江戸時代の町割りがそのまま残る貴重な歴史的町並みです。毛利氏の居城萩城の城下町として栄え、幕末には吉田松陰、高杉晋作、木戸孝允など明治維新の志士を多数輩出しました。',
    highlights: ['明治維新', '武家屋敷', '幕末', '歴史的町並み'],
    history: '慶長9年（1604年）、毛利輝元が萩城を築城し、城下町が形成されました。江戸時代を通じて長州藩の政治・文化の中心地として発展し、幕末には討幕運動の拠点となりました。現在も当時の武家屋敷や商家が数多く残り、ユネスコ世界文化遺産に登録されています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1104,
    name: '角島大橋',
    description: 'エメラルドグリーンの海に架かる美しい橋。CMや映画のロケ地としても有名です。',
    category: '観光エリア',
    prefecture: '山口県',
    imageUrl: '',
    overview: '角島大橋は山口県下関市と角島を結ぶ全長1,780mの離島架橋です。エメラルドグリーンの美しい海に架かる白い橋は絶景スポットとして人気を集め、多くのCMや映画のロケ地として使用されています。',
    highlights: ['エメラルドグリーン', '絶景', 'ロケ地', '角島'],
    history: '2000年11月3日に開通した比較的新しい橋ですが、その美しい景観から瞬く間に山口県を代表する観光地となりました。橋の建設にあたっては景観への配慮がなされ、海の美しさを損なわない設計となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1105,
    name: '瑠璃光寺',
    description: '国宝の五重塔で有名な臨済宗の寺院。山口市を代表する美しい古刹です。',
    category: '寺院',
    prefecture: '山口県',
    imageUrl: '',
    overview: '瑠璃光寺は山口市にある曹洞宗の寺院で、室町時代建立の五重塔が国宝に指定されています。大内氏の菩提寺として建立され、現在は山口市のシンボル的存在として親しまれています。',
    highlights: ['国宝', '五重塔', '古刹', '山口市'],
    history: '応永6年（1399年）、大内氏25代義弘の菩提を弔うために、夫人が建立した香積寺が起源です。五重塔は応永の乱で戦死した大内義弘を弔うため、弟の盛見によって建立されました。日本三名塔の一つに数えられる美しい塔です。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1106,
    name: '元乃隅神社',
    description: '海に向かって123基の鳥居が連なる絶景神社。CNNに選ばれた美しい場所です。',
    category: '神社',
    prefecture: '山口県',
    imageUrl: '',
    overview: '元乃隅神社は山口県長門市にある神社で、断崖絶壁に建つ123基の朱色の鳥居で有名です。2015年にCNNの「日本の最も美しい場所31選」に選ばれ、一躍有名になりました。',
    highlights: ['123基の鳥居', 'CNN選出', '絶景', '海の神社'],
    history: '昭和30年（1955年）に地元の網元によって建立された比較的新しい神社です。白狐のお告げにより建立されたとされ、その後弐々に鳥居が増設されて現在の姿となりました。近年のSNSブームにより多くの観光客が訪れる人気スポットとなっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 徳島県
  {
    id: 1201,
    name: '鳴門の渦潮',
    description: '世界最大級の渦潮。大鳴門橋からの眺めと遊覧船での体験が人気の自然現象です。',
    category: '自然',
    prefecture: '徳島県',
    imageUrl: '',
    overview: '鳴門の渦潮は鳴門海峡で発生する世界最大級の渦潮です。潮の干満により海峡を流れる潮流が時速20kmにも達し、直径20mを超える大きな渦が発生します。大鳴門橋の渦の道や観潮船から間近で観察できます。',
    highlights: ['世界最大級', '渦潮', '大鳴門橋', '遊覧船'],
    history: '鳴門海峡は瀮戸内海と太平洋を結ぶ狭い海峡で、古くから船乗りに恐れられていました。明治時代以降、この自然現象が観光資源として注目され、1985年の大鳴門橋開通により多くの観光客が訪れるようになりました。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1202,
    name: '祖谷のかずら橋',
    description: '野生のシラクチカズラで作られた吊り橋。日本三奇橋の一つとして知られています。',
    category: '歴史的景観',
    prefecture: '徳島県',
    imageUrl: '',
    overview: '祖谷のかずら橋は祖谷川に架かる全長45m、幅2m、水面上14mの吊り橋です。シラクチカズラという野生の蔓で編まれており、3年毎に架け替えられています。平家の落人が追手から逃れるために架けたという伝説があります。',
    highlights: ['三奇橋', 'かずら橋', '吊り橋', '祖谷渓'],
    history: '平安時代末期、源平の戦いに敗れた平家の落人が祖谷の地に逃れ、追手が来ても切り落とせるようにと蔓で橋を架けたのが始まりとされています。現在も昔ながらの製法で3年毎に架け替えられ、日本三奇橋の一つとして多くの観光客を魅了しています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1203,
    name: '阿波踊り会館',
    description: '徳島の伝統芸能阿波踊りを一年中楽しめる施設。踊りの歴史と魅力を学べます。',
    category: '観光エリア',
    prefecture: '徳島県',
    imageUrl: '',
    overview: '阿波踊り会館は徳島市にある阿波踊りの常設展示・公演施設です。約400年の歴史を持つ阿波踊りの魅力を一年中体験でき、実際の踊りの実演や踊り方の体験もできます。',
    highlights: ['阿波踊り', '伝統芸能', 'よしこの', '踊り子'],
    history: '阿波踊りは約400年前の安土桃山時代に始まったとされる徳島県の伝統芸能です。「踊る阿呢に見る阿呢、同じ阿呢なら踊らにゃそんそん」の掛け声で知られ、毎年8月のお盆期間中には徳島市内で盛大な祭りが開催されます。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1204,
    name: '大塚国際美術館',
    description: '世界の名画を陶板で原寸大再現した美術館。ルーヴル美術館級の展示が楽しめます。',
    category: '博物館',
    prefecture: '徳島県',
    imageUrl: '',
    overview: '大塚国際美術館は鳴門市にある世界初の陶板名画美術館です。古代から現代まで世界26ヶ国、190余の美術館が所蔵する西洋名画1000余点を、陶板で原寸大に忠実再現しています。',
    highlights: ['陶板名画', '原寸大', '世界の名画', '大塚製薬'],
    history: '1998年に大塚製薬グループによって設立されました。大塚オーミ陶業株式会社の特殊技術により、退色や劣化しない陶板で世界の名画を半永久的に保存・展示することを可能にした画期的な美術館です。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1205,
    name: '剣山',
    description: '四国第二の高峰。霊峰として信仰を集め、高山植物と360度の絶景が楽しめます。',
    category: '自然',
    prefecture: '徳島県',
    imageUrl: '',
    overview: '剣山は標高1,955mの四国第二の高峰で、古くから霊峰として信仰を集めています。山頂周辺には貴重な高山植物が自生し、360度の大パノラマが楽しめます。リフトが整備されており、比較的登りやすい山としても人気です。',
    highlights: ['四国第二峰', '霊峰', '高山植物', '絶景'],
    history: '古くから山岳信仰の対象とされ、修験道の霊場として栄えました。安徳天皇の剣が埋められているという伝説もあります。現在は四国百名山の一つとして多くの登山者に愛され、春のアケボノツツジ、夏の高山植物、秋の紅葉と四季折々の美しさを見せています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1206,
    name: 'うだつの町並み',
    description: '脇町の江戸時代の商家群。うだつが上がった美しい伝統的建造物群保存地区です。',
    category: '歴史的景観',
    prefecture: '徳島県',
    imageUrl: '',
    overview: '脇町のうだつの町並みは、江戸時代の商家群が約400mにわたって続く美しい町並みです。「うだつ」とは隣家との境界に設けられた防火壁のことで、経済力の象徴でもありました。現在は重要伝統的建造物群保存地区に指定されています。',
    highlights: ['うだつ', '商家群', '江戸時代', '伝統建造物'],
    history: '江戸時代中期以降、吉野川の水運を利用した藍の集散地として栄えました。「うだつが上がらない」という言葉の語源ともなった「うだつ」は、商家の繁栄を示すシンボルでした。現在も江戸時代の風情を残す貴重な歴史的町並みとして保存されています。',
    images: [
      '',
      '',
      ''
    ]
  },
  // 鹿児島県
  {
    id: 1301,
    name: '桜島',
    description: '世界有数の活火山。噴煙を上げる雄大な姿と火山の恵みを体感できる象徴的な山です。',
    category: '自然',
    prefecture: '鹿児島県',
    imageUrl: '',
    overview: '桜島は鹿児島県にある世界有数の活火山で、現在も活発な火山活動を続けています。標高1,117mの御岳を主峰とし、鹿児島のシンボルとして親しまれています。火山活動により形成された特殊な地形と生態系が魅力です。',
    highlights: ['活火山', '噴煙', '火山灰', '溶岩'],
    history: '約2万6千年前から火山活動を始め、度重なる噴火により現在の姿となりました。1914年の大正大噴火では大隅半島と陸続きになり、現在の地形が形成されました。現在も年間数百回の噴火を繰り返している世界でも珍しい活火山です。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1302,
    name: '屋久島',
    description: '世界自然遺産の原生林。樹齢数千年の屋久杉と豊かな自然生態系で有名です。',
    category: '自然',
    prefecture: '鹿児島県',
    imageUrl: '',
    overview: '屋久島は鹿児島県の大隅半島南方約60kmにある島で、1993年に世界自然遺産に登録されました。樹齢1000年を超える屋久杉の原生林と、亜熱帯から亜寒帯までの植生の垂直分布が見られる貴重な自然の宝庫です。',
    highlights: ['世界遺産', '屋久杉', '原生林', 'もののけ姫'],
    history: '約1500万年前に海底火山の隆起により形成された花崗岩の島です。縄文杉をはじめとする樹齢数千年の屋久杉の存在が確認され、1993年に白神山地とともに日本初の世界自然遺産に登録されました。ジブリ映画「もののけ姫」の舞台のモデルとしても有名です。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1303,
    name: '知覧特攻平和会館',
    description: '太平洋戦争の特攻隊の歴史を伝える平和の拠点。戦争の教訓と平和の尊さを学べます。',
    category: '博物館',
    prefecture: '鹿児島県',
    imageUrl: '',
    overview: '知覧特攻平和会館は、太平洋戦争末期に沖縄戦で戦死した陸軍特攻隊員の遺品や資料を展示する施設です。1,036名の特攻隊員の遺影と遺書、遺品などが展示され、平和の尊さを伝える重要な施設として多くの人が訪れます。',
    highlights: ['特攻隊', '平和', '戦争遺跡', '教育施設'],
    history: '知覧飛行場は1941年に陸軍の飛行場として開設され、太平洋戦争末期には特攻基地となりました。1975年に知覧特攻平和会館が開館し、戦争の悲惨さと平和の尊さを後世に伝える施設として、国内外から多くの見学者が訪れています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1304,
    name: '霧島神宮',
    description: '瓊瓊杵尊を祀る霧島連山の麓にある古社。パワースポットとして信仰を集めています。',
    category: '神社',
    prefecture: '鹿児島県',
    imageUrl: '',
    overview: '霧島神宮は霧島連山の麓にある神社で、天孫降臨の主神である瓊瓊杵尊（ニニギノミコト）を主祭神として祀っています。創建から1500年以上の歴史を持つ古社で、鮮やかな朱色の社殿が美しく、パワースポットとしても人気です。',
    highlights: ['瓊瓊杵尊', 'パワースポット', '霧島連山', '古社'],
    history: '欽明天皇の時代（6世紀）に創建されたと伝えられる古社で、当初は霧島山頂付近にありましたが、噴火により何度も移転し、現在地には文明16年（1484年）に鎮座しました。坂本龍馬とお龍が新婚旅行で訪れたことでも有名です。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1305,
    name: '指宿温泉',
    description: '天然砂むし温泉で有名な南国の温泉地。開聞岳を望む美しい景観も魅力です。',
    category: '温泉',
    prefecture: '鹿児島県',
    imageUrl: '',
    overview: '指宿温泉は薩摩半島南端に位置する温泉地で、世界的にも珍しい天然砂むし温泉で有名です。摂津の湯、弥次ヶ湯温泉、二月田温泉などの温泉群からなり、年間を通じて温暖な気候と美しい海岸線が魅力です。',
    highlights: ['砂むし温泉', '南国', '開聞岳', '温泉地'],
    history: '古くから温泉地として知られ、江戸時代には薩摩藩主島津家の湯治場として利用されていました。明治時代以降、砂むし温泉の効能が広く知られるようになり、現在では年間約300万人が訪れる九州屈指の温泉観光地となっています。',
    images: [
      '',
      '',
      ''
    ]
  },
  {
    id: 1306,
    name: '城山展望台',
    description: '桜島と錦江湾を一望する絶景スポット。鹿児島市街地の美しいパノラマが楽しめます。',
    category: '展望台',
    prefecture: '鹿児島県',
    imageUrl: '',
    overview: '城山展望台は鹿児島市の中心部にある標高107mの城山の山頂にある展望台です。雄大な桜島と錦江湾、鹿児島市街地を一望できる絶景スポットで、特に夕日が美しく、多くの観光客や地元の人々に愛されています。',
    highlights: ['絶景', '桜島', '錦江湾', 'パノラマ'],
    history: '城山は西南戦争最後の激戦地として知られ、西郷隆盛が最期を遂げた場所でもあります。戦後、展望台として整備され、現在では鹿児島を代表する観光スポットとなっています。夜景スポットとしても人気が高く、恋人の聖地にも認定されています。',
    images: [
      '',
      '',
      ''
    ]
  }
]



// Photo gallery functions - all from Google Place Photos
const loadGalleryPhotos = async () => {
  if (currentSpot.value?.name) {
    try {
      const photos = await getGalleryPhotos(currentSpot.value.name, currentSpot.value.place_id)
      galleryPhotos.value = photos
    } catch (error) {
      galleryPhotos.value = []
    }
  }
}

const getGalleryImages = () => {
  // Return Google Place Photos (excluding the first one which is used as main image)
  return galleryPhotos.value.slice(1) || []
}

const getTotalImageCount = () => {
  // Total count of all Google Place Photos
  return galleryPhotos.value.length || 1
}

const scrollLeft = () => {
  if (galleryContainer.value) {
    const containerWidth = galleryContainer.value.clientWidth
    galleryContainer.value.scrollBy({
      left: -containerWidth, // Full width of container
      behavior: 'smooth'
    })
    restartAutoScrollAfterDelay()
  }
}

const scrollRight = () => {
  if (galleryContainer.value) {
    const containerWidth = galleryContainer.value.clientWidth
    galleryContainer.value.scrollBy({
      left: containerWidth, // Full width of container
      behavior: 'smooth'
    })
    restartAutoScrollAfterDelay()
  }
}

const updateScrollButtons = () => {
  if (!galleryContainer.value) return
  
  const container = galleryContainer.value
  const scrollLeft = container.scrollLeft
  const scrollWidth = container.scrollWidth
  const clientWidth = container.clientWidth
  
  showLeftButton.value = scrollLeft > 0
  showRightButton.value = scrollLeft < (scrollWidth - clientWidth - 10) // 10px buffer
  
  // Update current image index based on scroll position
  const imageIndex = Math.round(scrollLeft / clientWidth)
  currentImageIndex.value = imageIndex
}

const getCurrentImageIndex = () => {
  return currentImageIndex.value
}

const scrollToImage = (index: number) => {
  if (!galleryContainer.value) return
  
  const containerWidth = galleryContainer.value.clientWidth
  const gap = 16 // gap-4 = 16px
  galleryContainer.value.scrollTo({
    left: index * (containerWidth + gap),
    behavior: 'smooth'
  })
  currentImageIndex.value = index
}

// Auto-scroll functions
const startGalleryAutoScroll = () => {
  if (galleryInterval) return
  
  galleryInterval = setInterval(() => {
    const totalImages = getTotalImageCount()
    if (totalImages <= 1) return
    
    const nextIndex = (currentImageIndex.value + 1) % totalImages
    scrollToImage(nextIndex)
  }, 4000) // 4秒ごとに切り替え
}

const stopGalleryAutoScroll = () => {
  if (galleryInterval) {
    clearInterval(galleryInterval)
    galleryInterval = null
  }
}

// Restart auto-scroll after user interaction
const restartAutoScrollAfterDelay = () => {
  stopGalleryAutoScroll()
  setTimeout(() => {
    startGalleryAutoScroll()
  }, 5000) // 5秒後に再開
}


// Helper function to highlight time patterns in text
const highlightTimes = (text: string): string => {
  if (!text) return ''
  
  // Pattern to match time expressions like "徒歩3分", "約5分", "30分", etc.
  const timePattern = /(徒歩\d+分|約\d+分|\d+分)/g
  
  return text.replace(timePattern, '<span class="text-red-600 dark:text-red-400 font-medium">$1</span>')
}

// Initialize available tabs based on spot data
const initializeTabs = () => {
  if (!currentSpot.value) return
  
  const tabs = []
  if (currentSpot.value.public_transport && currentSpot.value.public_transport.length > 0) {
    tabs.push('transport')
  }
  if (currentSpot.value.car_access && currentSpot.value.car_access.length > 0) {
    tabs.push('car')
  }
  if (currentSpot.value.parking_info) {
    tabs.push('parking')
  }
  if (currentSpot.value.walking_info) {
    tabs.push('walking')
  }
  
  availableTabs.value = tabs
  if (tabs.length > 0) {
    accessTab.value = tabs[0]
    currentTabIndex.value = 0
  }
}

// Set access tab
const setAccessTab = (tab: string) => {
  accessTab.value = tab
  currentTabIndex.value = availableTabs.value.indexOf(tab)
}

// Touch handlers for swipe functionality
const handleTouchStart = (e: TouchEvent) => {
  touchStartX.value = e.touches[0].clientX
}

const handleTouchMove = (e: TouchEvent) => {
  // Prevent default to avoid scrolling while swiping
  e.preventDefault()
}

const handleTouchEnd = (e: TouchEvent) => {
  touchEndX.value = e.changedTouches[0].clientX
  handleSwipe()
}

const handleSwipe = () => {
  const threshold = 50 // Minimum distance for swipe
  const swipeDistance = touchStartX.value - touchEndX.value
  
  if (Math.abs(swipeDistance) > threshold) {
    if (swipeDistance > 0) {
      // Swipe left - next tab
      const nextIndex = Math.min(currentTabIndex.value + 1, availableTabs.value.length - 1)
      if (nextIndex !== currentTabIndex.value) {
        setAccessTab(availableTabs.value[nextIndex])
      }
    } else {
      // Swipe right - previous tab
      const prevIndex = Math.max(currentTabIndex.value - 1, 0)
      if (prevIndex !== currentTabIndex.value) {
        setAccessTab(availableTabs.value[prevIndex])
      }
    }
  }
}

// Generate audio guide text for text-to-speech
const getAudioGuideText = () => {
  if (!currentSpot.value) {
    return ''
  }

  const spot = currentSpot.value
  let audioText = ''

  // Introduction
  audioText += `ようこそ、${spot.name}へお越しいただき、ありがとうございます。`
  
  // Overview/Description
  if (spot.overview || spot.description) {
    audioText += `${spot.overview || spot.description}`
  }

  // History section
  if (spot.history) {
    audioText += `\n\n${spot.name}の歴史についてご紹介します。${spot.history}`
  }

  // Highlights section
  if (spot.highlights && spot.highlights.length > 0) {
    audioText += `\n\nこちらの見どころは、${spot.highlights.join('、')}となっております。`
  }

  // Closing
  audioText += `\n\n${spot.name}での素晴らしいひとときをお楽しみください。ありがとうございました。`

  return audioText
}

// Load spot data on mount
onMounted(async () => {
  try {
    isLoading.value = true
    error.value = null
    
    // First fetch the spots data
    await fetchSpots()
    
    // Get the ID from route params
    const id = route.params.id as string
    
    if (!id) {
      error.value = 'No spot ID provided'
      return
    }
    
    const numericId = parseInt(id)
    
    // Use local data first, then fallback to API
    let spot = null
    
    // Try API first for real data
    spot = await fetchSpotFromAPI(numericId)
    
    // Fallback to local data if API fails
    if (!spot) {
      spot = allSpots.find(s => s.id === numericId)
    }
    
    // Last fallback to stored API data
    if (!spot) {
      spot = getSpotById(numericId)
    }
    
    if (spot) {
      currentSpot.value = spot
      
      // Load gallery photos from Google Places API
      await loadGalleryPhotos()
      
      // Initialize access tabs
      initializeTabs()
      
      // Start auto-scroll for gallery
      nextTick(() => {
        startGalleryAutoScroll()
      })
      
      // Initialize photo gallery scroll buttons
      nextTick(() => {
        updateScrollButtons()
      })
      
      // Update page title
      useHead({
        title: `${spot.name} - Travel Voice`
      })
    } else {
      error.value = 'Spot not found'
    }
  } catch (err) {
    console.error('Error loading spot:', err)
    error.value = 'Failed to load spot data'
  } finally {
    isLoading.value = false
  }
})

// Cleanup on unmount
onUnmounted(() => {
  stopGalleryAutoScroll()
})

// Also watch for route changes
watch(() => route.params.id, async (newId) => {
  if (newId) {
    const numericId = parseInt(newId as string)
    const spot = allSpots.find(s => s.id === numericId)
    if (spot) {
      currentSpot.value = spot
      // Load gallery photos for the new spot
      await loadGalleryPhotos()
    }
  }
})
</script>

<style scoped>
.gallery-scroll {
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* Internet Explorer 10+ */
}

.gallery-scroll::-webkit-scrollbar {
  display: none; /* WebKit */
}
</style>

<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Hero Section -->
    <div class="relative pt-16">
      <!-- Main Image -->
      <div class="h-64 md:h-80 relative overflow-hidden">
        <img 
          :src="currentSpot?.images?.[0] || currentSpot?.imageUrl" 
          :alt="currentSpot?.name"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>
        
        <!-- Back Button -->
        <button 
          @click="goBack"
          class="absolute top-6 left-6 flex items-center gap-2 text-white hover:text-cyan-400 transition-colors duration-300 group bg-black/30 backdrop-blur-sm rounded-full px-4 py-2"
        >
          <ArrowLeft class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" />
          <span class="text-sm font-medium">戻る</span>
        </button>
        
        <!-- Spot Title Overlay -->
        <div class="absolute bottom-6 left-6 right-6">
          <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-lg p-4">
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
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 pb-24">
      <div class="max-w-4xl mx-auto py-8" v-if="currentSpot">
        
        <!-- Audio Guide Section -->
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-xl p-6 mb-8">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                <Headphones class="w-6 h-6 text-white" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">音声ガイド</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm">{{ currentSpot.name }}の詳しい解説を聞く</p>
              </div>
            </div>
            <button 
              @click="playAudioGuide"
              class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:from-purple-700 hover:to-blue-700 transition-all duration-300 flex items-center gap-2"
            >
              <Play class="w-4 h-4" />
              再生
            </button>
          </div>
        </div>

        <!-- Overview Section -->
        <section class="mb-8">
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <Info class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            概要
          </h2>
          <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
              {{ currentSpot.overview }}
            </p>
          </div>
        </section>

        <!-- Highlights Section -->
        <section class="mb-8">
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
              <Star class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
            </div>
            見どころ
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div 
              v-for="highlight in currentSpot.highlights" 
              :key="highlight"
              class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 flex items-center gap-3"
            >
              <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
              <span class="text-gray-700 dark:text-gray-300">{{ highlight }}</span>
            </div>
          </div>
        </section>

        <!-- History Section -->
        <section class="mb-8">
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <Clock class="w-5 h-5 text-green-600 dark:text-green-400" />
            </div>
            歴史
          </h2>
          <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
              {{ currentSpot.history }}
            </p>
          </div>
        </section>

        <!-- Images Gallery -->
        <section class="mb-8" v-if="currentSpot.images && currentSpot.images.length > 1">
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
              <Camera class="w-5 h-5 text-purple-600 dark:text-purple-400" />
            </div>
            フォトギャラリー
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
              v-for="(image, index) in currentSpot.images" 
              :key="index"
              class="aspect-video rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300"
            >
              <img 
                :src="image" 
                :alt="`${currentSpot.name} - 画像 ${index + 1}`"
                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
          </div>
        </section>

      </div>

      <!-- Error State -->
      <div v-else class="flex items-center justify-center min-h-screen">
        <div class="text-center">
          <MapPin class="w-16 h-16 text-gray-300 mx-auto mb-4" />
          <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">観光スポットが見つかりませんでした</h3>
          <p class="text-gray-600 dark:text-gray-300 mb-6">指定されたスポットは存在しないか、削除された可能性があります</p>
          <button 
            @click="goBack"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          >
            ホームに戻る
          </button>
        </div>
      </div>

      <!-- Audio Player -->
      <AudioGuidePlayer 
        :audio-guide="currentAudioGuide"
        :spot-name="currentSpot?.name || null"
        :is-visible="!!currentAudioGuide"
        @close="closePlayer"
      />
    </main>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { ArrowLeft, Headphones, Play, Info, Star, Clock, Camera, MapPin } from 'lucide-vue-next'
import type { AudioGuide } from '~/types'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import AudioGuidePlayer from '~/components/AudioGuidePlayer.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

const route = useRoute()
const spotId = computed(() => parseInt(route.params.id as string))

useHead({
  title: computed(() => `${currentSpot.value?.name || '観光地詳細'} - Travel Voice`)
})

// Reactive variables
const activeTab = ref('top')
const currentSpot = ref(null)
const currentAudioGuide = ref<AudioGuide | null>(null)

// All spots data (same as in index.vue)
const allSpots = [
  {
    id: 1,
    name: '東京スカイツリー',
    description: '高さ634mの世界最高クラスの電波塔。展望デッキからは東京の絶景を一望できます。',
    category: '展望台',
    prefecture: '東京都',
    imageUrl: 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=400&h=300&fit=crop&auto=format',
    overview: '東京スカイツリーは、東京都墨田区押上にある電波塔で、2012年に開業しました。高さ634mは世界第2位の高さを誇り、東京の新たなランドマークとして親しまれています。展望デッキからは関東平野を一望でき、晴天時には富士山まで見渡すことができます。',
    highlights: ['展望デッキ（350m・450m）', 'スカイツリータウン', 'ライトアップ', 'プラネタリウム'],
    history: '2008年に着工し、2012年に完成。武蔵国の旧国名に因んで634m（ムサシ）の高さに設計されました。建設には最新の制振技術が使われ、日本の伝統的な建築技法も取り入れられています。東京オリンピック・パラリンピックに向けて建設され、現在では年間約600万人の観光客が訪れる東京の新名所となっています。',
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
    overview: '浅草寺は628年に創建された東京最古の寺院です。雷門から仲見世通りを通って本堂に至る参道は、常に多くの参拝者と観光客で賑わっています。江戸の下町情緒を今に伝える貴重な場所として、国内外から愛され続けています。',
    highlights: ['雷門', '仲見世通り', '本堂', '五重塔'],
    history: '推古天皇36年（628年）、隅田川で漁をしていた檜前浜成・竹成兄弟の網にかかった観音像を本尊として祀ったのが始まりとされています。江戸時代には徳川家の祈願所として栄え、庶民の信仰を集めました。戦災で多くの建物が焼失しましたが、戦後復興により現在の姿となりました。',
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
    overview: '大阪城は豊臣秀吉によって1583年に築城された日本の名城の一つです。現在の天守閣は1931年に再建されたもので、大阪のシンボルとして親しまれています。城内には歴史博物館があり、豊臣時代から現代までの大阪の歴史を学ぶことができます。',
    highlights: ['天守閣', '大阪城公園', '桜の名所', '歴史博物館'],
    history: '1583年、豊臣秀吉が石山本願寺の跡地に築城を開始。当時は「三国無双」と称される壮大な城でした。江戸時代には徳川幕府の直轄地となり、明治維新後は陸軍の施設として使用されました。現在の天守閣は昭和6年（1931年）に市民の寄付により復興されたものです。',
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
    overview: '清水寺は京都東山にある法相宗の寺院で、「清水の舞台」で有名です。1994年にユネスコ世界文化遺産に登録されており、年間を通じて多くの観光客が訪れます。四季折々の美しい景色を楽しむことができ、特に桜と紅葉の季節は格別の美しさです。',
    highlights: ['清水の舞台', '音羽の滝', '地主神社', '三重塔'],
    history: '延暦17年（798年）、坂上田村麻呂によって建立されました。平安時代から「清水詣」として庶民に親しまれ、江戸時代には西国三十三箇所観音霊場の第16番札所として栄えました。現在の本堂は1633年に再建されたもので、釘を一本も使わない日本古来の建築技法で建てられています。',
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
    overview: '札幌時計台は正式名称を「旧札幌農学校演武場」といい、1878年に建設された北海道の代表的な観光スポットです。現在は重要文化財に指定されており、札幌の発展の歴史を物語る貴重な建造物として大切に保存されています。',
    highlights: ['時計台の鐘', '歴史展示', 'クラーク博士の資料', 'コンサートホール'],
    history: '明治11年（1878年）、札幌農学校（現在の北海道大学）の演武場として建設されました。時計は明治14年に設置され、以来140年以上にわたって札幌の街に時を告げ続けています。クラーク博士の「Boys, be ambitious」の言葉でも有名な札幌農学校の貴重な遺構です。',
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
    overview: '金閣寺（鹿苑寺）は室町幕府三代将軍足利義満の別荘として1397年に建てられました。金箔で覆われた三層の楼閣が池に映る美しい姿は、京都を代表する風景として世界中の人々に愛されています。1994年にユネスコ世界文化遺産に登録されています。',
    highlights: ['金閣（舎利殿）', '鏡湖池', '庭園', '夕佳亭'],
    history: '応永4年（1397年）、足利義満が西園寺家の山荘を譲り受けて「北山殿」を造営。義満の死後、禅寺となり鹿苑寺と名付けられました。昭和25年に放火により焼失しましたが、昭和30年に再建されました。現在の金閣は当時の技術を駆使して忠実に復元されています。',
    images: [
      'https://images.unsplash.com/photo-1478436127897-769e1b3f0f36?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=600&h=400&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?w=600&h=400&fit=crop&auto=format'
    ]
  }
]

const goBack = () => {
  navigateTo('/')
}

const playAudioGuide = () => {
  if (currentSpot.value) {
    currentAudioGuide.value = {
      id: Date.now(),
      title: `${currentSpot.value.name}の音声ガイド`,
      description: `${currentSpot.value.name}の歴史と魅力について詳しく解説します。`,
      audioUrl: '/audio/sample.mp3',
      duration: 180,
      transcript: `${currentSpot.value.name}の詳しい音声ガイドです...`,
      touristSpotId: currentSpot.value.id,
      language: 'ja',
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }
  }
}

const closePlayer = () => {
  currentAudioGuide.value = null
}

// Load spot data on mount
onMounted(() => {
  const spot = allSpots.find(s => s.id === spotId.value)
  if (spot) {
    currentSpot.value = spot
  } else {
    // Redirect to home if spot not found
    navigateTo('/')
  }
})
</script>

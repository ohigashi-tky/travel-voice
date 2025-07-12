<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Page Title -->
    <div class="relative py-6 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 pt-6 overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img 
          src="/prefectures_image/46.jpeg" 
          alt="鹿児島県"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-center relative">
          <button 
            @click="goHome"
            class="absolute left-0 flex items-center gap-2 text-gray-200 hover:text-white transition-colors duration-300 group"
          >
            <ArrowLeft class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" />
            <span class="text-sm font-medium">戻る</span>
          </button>
          <h1 class="text-3xl font-bold text-white tracking-wide">
            鹿児島県
          </h1>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 pb-24">
      <div class="max-w-7xl mx-auto py-6">
        <!-- Tourist Spots Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <TouristSpotCard
            v-for="spot in touristSpots"
            :key="spot.id"
            :spot="spot"
            :show-tags="true"
          />
        </div>
      </div>
    </main>
    
    <!-- Footer -->
    <AppFooter v-model="activeTab" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ArrowLeft, ArrowRight } from 'lucide-vue-next'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import TouristSpotCard from '~/components/TouristSpotCard.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '鹿児島県 - Travel Voice',
  meta: [
    { name: 'description', content: '鹿児島県の観光スポットを音声ガイドで楽しもう。桜島、屋久島、指宿温泉など火山と自然の魅力をご紹介。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// 鹿児島県の観光スポット
const allSpots = [
  {
    id: 1301,
    name: '桜島',
    description: '世界有数の活火山。噴煙を上げる雄大な姿と火山の恵みを体感できる象徴的な山です。',
    category: '自然',
    prefecture: '鹿児島県',
    highlights: ['活火山', '噴煙', '火山灰', '溶岩']
  },
  {
    id: 1302,
    name: '屋久島',
    description: '世界自然遺産の原生林。樹齢数千年の屋久杉と豊かな自然生態系で有名です。',
    category: '自然',
    prefecture: '鹿児島県',
    highlights: ['世界遺産', '屋久杉', '原生林', 'もののけ姫']
  },
  {
    id: 1303,
    name: '知覧特攻平和会館',
    description: '太平洋戦争の特攻隊の歴史を伝える平和の拠点。戦争の教訓と平和の尊さを学べます。',
    category: '博物館',
    prefecture: '鹿児島県',
    highlights: ['特攻隊', '平和', '戦争遺跡', '教育施設']
  },
  {
    id: 1304,
    name: '霧島神宮',
    description: '瓊瓊杵尊を祀る霧島連山の麓にある古社。パワースポットとして信仰を集めています。',
    category: '神社',
    prefecture: '鹿児島県',
    highlights: ['瓊瓊杵尊', 'パワースポット', '霧島連山', '古社']
  },
  {
    id: 1305,
    name: '指宿温泉',
    description: '天然砂むし温泉で有名な南国の温泉地。開聞岳を望む美しい景観も魅力です。',
    category: '温泉',
    prefecture: '鹿児島県',
    highlights: ['砂むし温泉', '南国', '開聞岳', '温泉地']
  },
  {
    id: 1306,
    name: '城山展望台',
    description: '桜島と錦江湾を一望する絶景スポット。鹿児島市街地の美しいパノラマが楽しめます。',
    category: '展望台',
    prefecture: '鹿児島県',
    highlights: ['絶景', '桜島', '錦江湾', 'パノラマ']
  }
]

// 鹿児島県の観光スポットのみをフィルタリング
const touristSpots = computed(() => {
  return allSpots.filter(spot => spot.prefecture === '鹿児島県')
})

// Navigation methods
const goHome = () => {
  navigateTo('/')
}

</script>
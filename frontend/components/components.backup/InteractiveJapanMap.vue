<template>
  <div class="relative w-full max-w-6xl mx-auto">
    <!-- Map Container -->
    <div class="relative">
      <svg
        viewBox="0 0 800 600"
        class="w-full h-auto"
        @mouseleave="hoveredPrefecture = null"
      >
        <!-- Background -->
        <rect width="800" height="600" fill="url(#oceanGradient)" />
        
        <!-- Gradient Definitions -->
        <defs>
          <linearGradient id="oceanGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#1e3a8a;stop-opacity:0.3" />
            <stop offset="100%" style="stop-color:#3730a3;stop-opacity:0.2" />
          </linearGradient>
          
          <linearGradient id="prefectureGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#10b981;stop-opacity:0.8" />
            <stop offset="100%" style="stop-color:#059669;stop-opacity:0.9" />
          </linearGradient>
          
          <linearGradient id="prefectureHover" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#06b6d4;stop-opacity:0.9" />
            <stop offset="100%" style="stop-color:#0891b2;stop-opacity:1" />
          </linearGradient>
          
          <linearGradient id="tokyoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#f59e0b;stop-opacity:0.9" />
            <stop offset="100%" style="stop-color:#d97706;stop-opacity:1" />
          </linearGradient>
          
          <filter id="glow">
            <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
            <feMerge> 
              <feMergeNode in="coloredBlur"/>
              <feMergeNode in="SourceGraphic"/>
            </feMerge>
          </filter>
        </defs>
        
        <!-- Prefecture Paths -->
        <g id="prefectures">
          <!-- Tokyo (Special highlighting) -->
          <path
            d="M360 280 L380 275 L385 285 L390 290 L385 295 L375 300 L365 295 L360 285 Z"
            :fill="getPrefectureFill('東京都')"
            :stroke="getPrefectureStroke('東京都')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '東京都' }"
            @click="selectPrefecture('東京都')"
            @mouseenter="hoveredPrefecture = '東京都'"
          />
          
          <!-- Hokkaido -->
          <path
            d="M300 50 L350 45 L380 60 L400 80 L390 110 L360 120 L320 115 L280 100 L270 80 L285 65 Z"
            :fill="getPrefectureFill('北海道')"
            :stroke="getPrefectureStroke('北海道')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '北海道' }"
            @click="selectPrefecture('北海道')"
            @mouseenter="hoveredPrefecture = '北海道'"
          />
          
          <!-- Aomori -->
          <path
            d="M320 140 L340 135 L355 145 L350 160 L335 165 L320 160 Z"
            :fill="getPrefectureFill('青森県')"
            :stroke="getPrefectureStroke('青森県')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '青森県' }"
            @click="selectPrefecture('青森県')"
            @mouseenter="hoveredPrefecture = '青森県'"
          />
          
          <!-- Iwate -->
          <path
            d="M340 170 L355 165 L365 180 L360 200 L345 205 L335 190 Z"
            :fill="getPrefectureFill('岩手県')"
            :stroke="getPrefectureStroke('岩手県')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '岩手県' }"
            @click="selectPrefecture('岩手県')"
            @mouseenter="hoveredPrefecture = '岩手県'"
          />
          
          <!-- Miyagi -->
          <path
            d="M345 210 L360 205 L370 220 L365 235 L350 240 L340 225 Z"
            :fill="getPrefectureFill('宮城県')"
            :stroke="getPrefectureStroke('宮城県')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '宮城県' }"
            @click="selectPrefecture('宮城県')"
            @mouseenter="hoveredPrefecture = '宮城県'"
          />
          
          <!-- Fukushima -->
          <path
            d="M350 245 L365 240 L375 255 L370 270 L355 275 L345 260 Z"
            :fill="getPrefectureFill('福島県')"
            :stroke="getPrefectureStroke('福島県')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '福島県' }"
            @click="selectPrefecture('福島県')"
            @mouseenter="hoveredPrefecture = '福島県'"
          />
          
          <!-- Osaka -->
          <path
            d="M280 320 L295 315 L305 330 L300 340 L285 345 L275 335 Z"
            :fill="getPrefectureFill('大阪府')"
            :stroke="getPrefectureStroke('大阪府')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '大阪府' }"
            @click="selectPrefecture('大阪府')"
            @mouseenter="hoveredPrefecture = '大阪府'"
          />
          
          <!-- Kyoto -->
          <path
            d="M285 305 L300 300 L310 315 L305 325 L290 330 L280 320 Z"
            :fill="getPrefectureFill('京都府')"
            :stroke="getPrefectureStroke('京都府')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '京都府' }"
            @click="selectPrefecture('京都府')"
            @mouseenter="hoveredPrefecture = '京都府'"
          />
          
          <!-- Nara -->
          <path
            d="M295 335 L310 330 L320 345 L315 355 L300 360 L290 350 Z"
            :fill="getPrefectureFill('奈良県')"
            :stroke="getPrefectureStroke('奈良県')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '奈良県' }"
            @click="selectPrefecture('奈良県')"
            @mouseenter="hoveredPrefecture = '奈良県'"
          />
          
          <!-- Hiroshima -->
          <path
            d="M220 350 L235 345 L245 360 L240 370 L225 375 L215 365 Z"
            :fill="getPrefectureFill('広島県')"
            :stroke="getPrefectureStroke('広島県')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '広島県' }"
            @click="selectPrefecture('広島県')"
            @mouseenter="hoveredPrefecture = '広島県'"
          />
          
          <!-- Fukuoka -->
          <path
            d="M150 380 L165 375 L175 390 L170 400 L155 405 L145 395 Z"
            :fill="getPrefectureFill('福岡県')"
            :stroke="getPrefectureStroke('福岡県')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '福岡県' }"
            @click="selectPrefecture('福岡県')"
            @mouseenter="hoveredPrefecture = '福岡県'"
          />
          
          <!-- Okinawa -->
          <path
            d="M200 480 L210 478 L215 485 L212 492 L205 495 L198 490 Z"
            :fill="getPrefectureFill('沖縄県')"
            :stroke="getPrefectureStroke('沖縄県')"
            stroke-width="2"
            class="prefecture-path cursor-pointer transition-all duration-300"
            :class="{ 'filter-glow': hoveredPrefecture === '沖縄県' }"
            @click="selectPrefecture('沖縄県')"
            @mouseenter="hoveredPrefecture = '沖縄県'"
          />
        </g>
        
        <!-- Prefecture Labels -->
        <g id="labels" class="pointer-events-none">
          <text x="375" y="290" text-anchor="middle" class="text-xs font-bold fill-white">東京</text>
          <text x="340" y="85" text-anchor="middle" class="text-xs font-bold fill-white">北海道</text>
          <text x="340" y="155" text-anchor="middle" class="text-xs font-bold fill-white">青森</text>
          <text x="350" y="185" text-anchor="middle" class="text-xs font-bold fill-white">岩手</text>
          <text x="355" y="225" text-anchor="middle" class="text-xs font-bold fill-white">宮城</text>
          <text x="360" y="260" text-anchor="middle" class="text-xs font-bold fill-white">福島</text>
          <text x="290" y="335" text-anchor="middle" class="text-xs font-bold fill-white">大阪</text>
          <text x="295" y="315" text-anchor="middle" class="text-xs font-bold fill-white">京都</text>
          <text x="305" y="350" text-anchor="middle" class="text-xs font-bold fill-white">奈良</text>
          <text x="230" y="365" text-anchor="middle" class="text-xs font-bold fill-white">広島</text>
          <text x="160" y="395" text-anchor="middle" class="text-xs font-bold fill-white">福岡</text>
          <text x="206" y="488" text-anchor="middle" class="text-xs font-bold fill-white">沖縄</text>
        </g>
      </svg>
    </div>
    
    <!-- Prefecture Info Panel -->
    <Transition
      enter-active-class="transition-all duration-300"
      enter-from-class="opacity-0 transform translate-y-4"
      enter-to-class="opacity-100 transform translate-y-0"
      leave-active-class="transition-all duration-200"
      leave-from-class="opacity-100 transform translate-y-0"
      leave-to-class="opacity-0 transform translate-y-4"
    >
      <div
        v-if="hoveredPrefecture"
        class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-10"
      >
        <div class="glass-card rounded-2xl p-4 backdrop-blur-xl bg-white/10 border border-white/20 text-center min-w-48">
          <h3 class="text-white font-bold text-lg mb-2">{{ hoveredPrefecture }}</h3>
          <p class="text-blue-100 text-sm mb-3">{{ getPrefectureDescription(hoveredPrefecture) }}</p>
          <div class="flex items-center justify-center gap-2 text-cyan-400 text-sm">
            <Icon name="lucide:map-pin" class="w-4 h-4" />
            <span>クリックして探索</span>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits<{
  prefectureSelected: [prefecture: string]
}>()

const hoveredPrefecture = ref<string | null>(null)

const availablePrefectures = [
  '東京都', '北海道', '青森県', '岩手県', '宮城県', '福島県',
  '大阪府', '京都府', '奈良県', '広島県', '福岡県', '沖縄県'
]

const getPrefectureFill = (prefecture: string) => {
  if (prefecture === '東京都') {
    return hoveredPrefecture.value === prefecture ? 'url(#prefectureHover)' : 'url(#tokyoGradient)'
  }
  return hoveredPrefecture.value === prefecture ? 'url(#prefectureHover)' : 'url(#prefectureGradient)'
}

const getPrefectureStroke = (prefecture: string) => {
  if (prefecture === '東京都') {
    return hoveredPrefecture.value === prefecture ? '#06b6d4' : '#f59e0b'
  }
  return hoveredPrefecture.value === prefecture ? '#06b6d4' : '#10b981'
}

const getPrefectureDescription = (prefecture: string) => {
  const descriptions: Record<string, string> = {
    '東京都': '現代と伝統が融合する日本の首都',
    '北海道': '豊かな自然と美味しい食べ物の宝庫',
    '青森県': 'りんごとねぶた祭りで有名',
    '岩手県': '広大な自然と温泉が魅力',
    '宮城県': '伊達政宗と牛タンの故郷',
    '福島県': '桃と温泉、歴史ある会津',
    '大阪府': '食い倒れの街、関西の中心',
    '京都府': '千年の都、伝統文化の宝庫',
    '奈良県': '古都奈良、鹿と大仏で有名',
    '広島県': '平和の象徴、牡蠣も有名',
    '福岡県': '九州の玄関口、ラーメンの街',
    '沖縄県': '美しい海と独特の文化'
  }
  return descriptions[prefecture] || '魅力的な観光地がたくさん'
}

const selectPrefecture = (prefecture: string) => {
  emit('prefectureSelected', prefecture)
}
</script>

<style scoped>
.prefecture-path {
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.prefecture-path:hover {
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
}

.filter-glow {
  filter: url(#glow) drop-shadow(0 4px 8px rgba(6, 182, 212, 0.4));
}
</style>
<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />
    

    <!-- Title and Filters Section with Background -->
    <div class="relative">
      <!-- Background Image -->
      <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('/event_image.webp')"></div>
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black bg-opacity-40"></div>
      
      <!-- Title Section -->
      <div class="relative z-10 pt-0 pb-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
          <div class="text-center">
            <div class="flex items-center justify-center gap-3 my-4">
              <h1 class="text-3xl md:text-4xl font-bold text-white drop-shadow-lg">
                イベント情報
              </h1>
            </div>
            <p class="text-white text-md max-w-2xl mx-auto drop-shadow-md">
              全国各地で開催されるイベントを探してみよう
            </p>
          </div>
        </div>
      </div>
      
      <!-- Filters Section -->
      <div class="relative px-4 sm:px-6 lg:px-8 pb-6">
        <div class="max-w-6xl mx-auto">
          <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-xl p-4 border border-white/20 dark:border-gray-700/30 relative z-20">
          <!-- Always visible: Prefecture filter and toggle button -->
          <div class="flex items-center gap-4">
            <!-- Prefecture Filter -->
            <div class="flex-1 relative">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                都道府県
              </label>
              <CustomSelect
                v-model="selectedPrefecture"
                :options="prefectureOptions"
                placeholder="すべて"
                @update:modelValue="filterEvents"
              />
            </div>

            <!-- Toggle Button -->
            <div class="flex flex-col justify-end">
              <div class="h-6 mb-2"></div>
              <button
                @click="isFiltersExpanded = !isFiltersExpanded"
                class="px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-gray-600 dark:hover:to-gray-500 border border-blue-200 dark:border-gray-500 rounded-lg shadow-sm hover:shadow transition-all duration-200 flex items-center gap-1.5"
              >
                <span>詳細検索</span>
                <svg 
                  class="w-3 h-3 transition-transform duration-200 text-gray-400"
                  :class="{ 'rotate-180': isFiltersExpanded }"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Expandable filters -->
          <div 
            v-show="isFiltersExpanded"
            class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 transition-all duration-300"
          >
            <div class="flex flex-col gap-4">
              <!-- Date and Category Filters -->
              <div class="flex gap-4 relative">
                <!-- Date Filter -->
                <div class="flex-1 relative">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    日付
                  </label>
                  <input
                    v-model="selectedDate"
                    @input="filterEvents"
                    type="date"
                    class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400"
                    style="min-height: 38px;"
                  />
                </div>

                <!-- Category Filter -->
                <div class="flex-1 relative">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    カテゴリ
                  </label>
                  <CustomSelect
                    v-model="selectedCategory"
                    :options="categoryOptions"
                    placeholder="すべて"
                    @update:modelValue="filterEvents"
                  />
                </div>
              </div>

              <!-- Search -->
              <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  キーワード検索
                </label>
                <input 
                  v-model="searchKeyword"
                  @input="filterEvents"
                  type="text"
                  placeholder="イベント名で検索"
                  class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-gray-300 dark:focus:border-gray-600"
                />
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 pb-32">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Loading State -->
        <div v-if="isLoading" class="flex items-center justify-center py-12">
          <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600 dark:text-gray-300">イベント情報を読み込み中...</p>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <div class="text-red-600 dark:text-red-400 mb-4">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-medium mb-2">データの読み込みに失敗しました</h3>
            <p class="text-sm">{{ error }}</p>
          </div>
          <button 
            @click="fetchEvents"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors"
          >
            再試行
          </button>
        </div>

        <!-- Pagination Controls -->
        <div v-if="filteredEvents.length > 0 && totalPages > 1" class="mb-2">
          <!-- Results Summary -->
          <div class="text-sm text-gray-600 dark:text-gray-300 mb-1 text-center">
            全{{ filteredEvents.length }}件 {{ (currentPage - 1) * itemsPerPage + 1 }}-{{ Math.min(currentPage * itemsPerPage, filteredEvents.length) }}件を表示
          </div>
          
          <!-- Pagination Buttons (5 buttons always visible) -->
          <div class="flex items-center justify-center space-x-2">
            <!-- First Page Button -->
            <button
              @click="goToPage(1)"
              :disabled="currentPage === 1"
              class="px-2 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              title="最初のページ"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
              </svg>
            </button>

            <!-- Previous Button -->
            <button
              @click="goToPreviousPage"
              :disabled="currentPage === 1"
              class="px-2 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              title="前のページ"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>

            <!-- Current Page Display -->
            <div class="px-3 py-0.5 text-sm font-medium bg-blue-600 border border-blue-600 text-white rounded-md">
              {{ currentPage }}
            </div>

            <!-- Next Button -->
            <button
              @click="goToNextPage"
              :disabled="currentPage === totalPages"
              class="px-2 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              title="次のページ"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>

            <!-- Last Page Button -->
            <button
              @click="goToPage(totalPages)"
              :disabled="currentPage === totalPages"
              class="px-2 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              title="最後のページ"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Events List -->
        <div v-if="filteredEvents.length > 0" class="space-y-3">
          <div 
            v-for="event in paginatedEvents" 
            :key="event.id"
            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow duration-300"
          >
            <!-- Event Header -->
            <div class="mb-2">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                {{ event.title }}
              </h3>
              
              <!-- Prefecture, Location, Date -->
              <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                <!-- Prefecture -->
                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded text-xs font-medium">
                  {{ event.prefecture }}
                </span>
                
                <!-- Location -->
                <div class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span>{{ event.location }}</span>
                </div>

                <!-- Date -->
                <div class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span>{{ formatDateRange(event.start_date, event.end_date) }}</span>
                </div>
              </div>

              <!-- Tags -->
              <div v-if="getEventTags(event).length > 0" class="flex flex-wrap gap-1 mt-2">
                <span 
                  v-for="tag in getEventTags(event).slice(0, 3)" 
                  :key="tag"
                  class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs"
                >
                  {{ tag }}
                </span>
              </div>
            </div>

            <!-- Overview and Access Information -->
            <div v-if="event.overview || event.access" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 space-y-2">
              <!-- Overview -->
              <div v-if="event.overview" class="text-sm text-gray-700 dark:text-gray-300">
                <div class="flex items-start gap-2">
                  <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span>{{ event.overview }}</span>
                </div>
              </div>
              
              <!-- Access -->
              <div v-if="event.access" class="text-sm text-gray-700 dark:text-gray-300">
                <div class="flex items-start gap-2">
                  <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                  </svg>
                  <span>{{ event.access }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Events State -->
        <div v-else-if="!isLoading && !error && filteredEvents.length === 0" class="text-center py-12">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-600 dark:text-gray-300 mb-2">イベントが見つかりません</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">検索条件を変更してみてください</p>
          </div>
        </div>


      </div>
    </main>
  </div>
</template>

<style scoped>
/* Date input styling for mobile browsers */
.date-input {
  /* Remove default browser styling */
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

/* Webkit browsers (Safari, Chrome on iOS) */
.date-input::-webkit-datetime-edit {
  padding: 0;
  color: inherit;
}

.date-input::-webkit-datetime-edit-fields-wrapper {
  padding: 0;
}

.date-input::-webkit-datetime-edit-text {
  color: inherit;
}

.date-input::-webkit-datetime-edit-month-field,
.date-input::-webkit-datetime-edit-day-field,
.date-input::-webkit-datetime-edit-year-field {
  color: inherit;
}

.date-input::-webkit-inner-spin-button,
.date-input::-webkit-calendar-picker-indicator {
  display: none;
  -webkit-appearance: none;
}

/* Dark mode support for date input */
.dark .date-input::-webkit-datetime-edit,
.dark .date-input::-webkit-datetime-edit-text,
.dark .date-input::-webkit-datetime-edit-month-field,
.dark .date-input::-webkit-datetime-edit-day-field,
.dark .date-input::-webkit-datetime-edit-year-field {
  color: white;
}

/* Firefox */
.date-input::-moz-focus-inner {
  border: 0;
  padding: 0;
}

/* Ensure consistent height across browsers */
@supports (-webkit-touch-callout: none) {
  /* iOS Safari specific */
  .date-input {
    font-size: 14px;
    line-height: 1.4;
  }
}
</style>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useLanguage } from '~/composables/useLanguage'
import { usePrefectures } from '~/composables/usePrefectures'
import AppHeader from '~/components/AppHeader.vue'
import CustomSelect from '~/components/CustomSelect.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'イベント情報 - おうち旅行',
  meta: [
    { name: 'description', content: '全国各地で開催されるイベント情報を都道府県や開催期間で絞り込んで検索できます。' }
  ]
})

const { t } = useLanguage()
const config = useRuntimeConfig()
const { prefectures: masterPrefectures, fetchPrefectures } = usePrefectures()

// Reactive variables
const isLoading = ref(false)
const error = ref(null)
const events = ref([])
const selectedPrefecture = ref('')
const selectedDate = ref('')
const selectedCategory = ref('')
const searchKeyword = ref('')
const currentPage = ref(1)
const itemsPerPage = 20
const isFiltersExpanded = ref(false)

// Options for CustomSelect components
const prefectureOptions = computed(() => {
  const options = [{ value: '', label: 'すべて' }]
  
  // マスターテーブルから都道府県を取得（display_order順）
  if (masterPrefectures.value && masterPrefectures.value.length > 0) {
    const sortedPrefectures = [...masterPrefectures.value].sort((a, b) => a.display_order - b.display_order)
    options.push(...sortedPrefectures.map(prefecture => ({
      value: prefecture.name,
      label: prefecture.name
    })))
  }
  
  return options
})

// Available categories based on actual event tags in database
const availableCategories = [
  '祭り', '花火大会', '桜', '紅葉', 'グルメ', '音楽', 'スポーツ', 
  '文化', '伝統', 'アート', 'フェスティバル', 'ライトアップ', 
  'イルミネーション', '体験', '季節イベント', '展示・展覧会'
]

const categoryOptions = computed(() => [
  { value: '', label: 'すべて' },
  ...availableCategories.map(category => ({
    value: category,
    label: category
  }))
])


// Computed filtered events
const filteredEvents = computed(() => {
  let filtered = events.value

  // Prefecture filter
  if (selectedPrefecture.value) {
    filtered = filtered.filter(event => event.prefecture === selectedPrefecture.value)
  }

  // Date filter - check if selected date falls within event period
  if (selectedDate.value) {
    const selectedDateObj = new Date(selectedDate.value)
    filtered = filtered.filter(event => {
      const startDate = new Date(event.start_date)
      const endDate = new Date(event.end_date)
      return selectedDateObj >= startDate && selectedDateObj <= endDate
    })
  }

  // Category filter
  if (selectedCategory.value) {
    filtered = filtered.filter(event => {
      const eventTags = getEventTags(event)
      const category = selectedCategory.value
      
      // Check if any tag matches the selected category
      return eventTags.some(tag => {
        
        // Handle specific category mappings
        switch (category) {
          case '花火大会':
            return tag.includes('花火')
          case '桜':
            return tag.includes('桜') || tag.includes('花見')
          case '紅葉':
            return tag.includes('紅葉')
          case 'グルメ':
            return tag.includes('グルメ') || tag.includes('食') || tag.includes('酒') || tag.includes('フルーツ') || tag.includes('うどん') || tag.includes('そば') || tag.includes('ラーメン')
          case '音楽':
            return tag.includes('音楽') || tag.includes('ライブ') || tag.includes('コンサート') || tag.includes('ジャズ')
          case 'スポーツ':
            return tag.includes('スポーツ') || tag.includes('マラソン') || tag.includes('野球') || tag.includes('サッカー') || tag.includes('競馬')
          case '文化':
            return tag.includes('文化') || tag.includes('芸術') || tag.includes('美術') || tag.includes('歴史')
          case '伝統':
            return tag.includes('伝統') || tag.includes('神事') || tag.includes('神輿') || tag.includes('盆踊り')
          case 'アート':
            return tag.includes('アート') || tag.includes('美術') || tag.includes('現代アート')
          case 'フェスティバル':
            return tag.includes('フェスティバル') || tag.includes('フェス')
          case 'ライトアップ':
            return tag.includes('ライトアップ') || tag.includes('夜景')
          case 'イルミネーション':
            return tag.includes('イルミネーション') || tag.includes('クリスマス') || tag.includes('光')
          case '体験':
            return tag.includes('体験') || tag.includes('アクティビティ') || tag.includes('ワークショップ')
          case '季節イベント':
            return tag.includes('季節') || tag.includes('春') || tag.includes('夏') || tag.includes('秋') || tag.includes('冬')
          case '展示・展覧会':
            return tag.includes('展示') || tag.includes('展覧会') || tag.includes('博物館')
          default:
            return tag.includes(category)
        }
      })
    })
  }

  // Keyword search
  if (searchKeyword.value.trim()) {
    const keyword = searchKeyword.value.trim().toLowerCase()
    filtered = filtered.filter(event => 
      event.title.toLowerCase().includes(keyword) ||
      event.description.toLowerCase().includes(keyword)
    )
  }

  return filtered
})

// Computed pagination
const totalPages = computed(() => Math.ceil(filteredEvents.value.length / itemsPerPage))

const paginatedEvents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredEvents.value.slice(start, end)
})


// Fetch events from API (データベース駆動)
const fetchEvents = async () => {
  isLoading.value = true
  error.value = null

  try {
    // 全てのイベントを取得（フィルタリングはクライアント側で実行）
    const params = new URLSearchParams()
    params.append('per_page', '2000') // 全イベントを取得

    const queryString = params.toString()
    const url = `${config.public.apiBase}/api/events${queryString ? '?' + queryString : ''}`

    const response = await $fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (response.success) {
      events.value = response.data || []
    } else {
      throw new Error(response.message || 'イベント情報の取得に失敗しました')
    }
  } catch (err) {
    console.error('API接続に失敗しました:', err)
    error.value = 'イベント情報を取得できませんでした。しばらく時間をおいてから再度お試しください。'
    events.value = []
  } finally {
    isLoading.value = false
  }
}


// Format date range for compact display
const formatDateRange = (startDate, endDate) => {
  const start = new Date(startDate)
  const end = new Date(endDate)
  
  // Same date
  if (startDate === endDate) {
    return start.toLocaleDateString('ja-JP', {
      month: 'short',
      day: 'numeric'
    })
  }
  
  // Same month
  if (start.getMonth() === end.getMonth() && start.getFullYear() === end.getFullYear()) {
    return `${start.getMonth() + 1}/${start.getDate()}-${end.getDate()}`
  }
  
  // Different months
  return `${start.toLocaleDateString('ja-JP', { month: 'short', day: 'numeric' })} - ${end.toLocaleDateString('ja-JP', { month: 'short', day: 'numeric' })}`
}

// Get event tags as array (handle both string and array formats)
const getEventTags = (event) => {
  if (!event.tags) return []
  
  // If already an array, return it
  if (Array.isArray(event.tags)) {
    return event.tags.filter(tag => tag && typeof tag === 'string')
  }
  
  // If string, try to parse as JSON
  if (typeof event.tags === 'string') {
    try {
      const parsed = JSON.parse(event.tags)
      if (Array.isArray(parsed)) {
        return parsed.filter(tag => tag && typeof tag === 'string')
      }
    } catch (e) {
      console.warn('Failed to parse event tags:', event.tags)
    }
  }
  
  return []
}

// Filter events when filters change
const filterEvents = () => {
  // Reset to first page when filters change
  currentPage.value = 1
  // フィルタリングはcomputed propertyで自動的に行われる
}

// Pagination functions
const goToPage = (page) => {
  currentPage.value = page
}

const goToPreviousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const goToNextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}


// Initialize
onMounted(async () => {
  // 都道府県マスターデータを取得
  await fetchPrefectures()
  // イベントデータを取得
  await fetchEvents()
})
</script>

<style scoped>
.gallery-scroll {
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.gallery-scroll::-webkit-scrollbar {
  display: none;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
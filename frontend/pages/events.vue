<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative overflow-hidden flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />
    
    <!-- Back Button -->
    <BackButton />

    <!-- Title Section -->
    <div class="pt-20 pb-6 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto">
        <div class="text-center">
          <div class="flex items-center justify-center gap-3 mb-4">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white">
              イベント情報
            </h1>
          </div>
          <p class="text-gray-600 dark:text-gray-300 text-md max-w-2xl mx-auto">
            全国各地で開催されるイベントを探してみよう
          </p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 relative z-10 pb-24">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Filters Section -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-4 border border-gray-200 dark:border-gray-700 relative">
          <!-- Always visible: Prefecture filter and toggle button -->
          <div class="flex items-center gap-4">
            <!-- Prefecture Filter -->
            <div class="flex-1 relative z-20">
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
                <div class="flex-1 relative z-10">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    日付
                  </label>
                  <input
                    v-model="selectedDate"
                    @input="filterEvents"
                    type="date"
                    class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-gray-300 dark:focus:border-gray-600"
                  />
                </div>

                <!-- Category Filter -->
                <div class="flex-1 relative z-10">
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
        <div v-if="filteredEvents.length > 0 && totalPages > 1" class="mb-4 flex items-center justify-between">
          <!-- Results Summary -->
          <div class="text-sm text-gray-600 dark:text-gray-300">
            {{ filteredEvents.length }}件中 {{ (currentPage - 1) * itemsPerPage + 1 }}-{{ Math.min(currentPage * itemsPerPage, filteredEvents.length) }}件を表示
          </div>
          
          <!-- Pagination Buttons -->
          <div class="flex items-center space-x-2">
            <!-- Previous Button -->
            <button
              @click="goToPreviousPage"
              :disabled="currentPage === 1"
              class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              前へ
            </button>

            <!-- Page Numbers -->
            <div class="flex space-x-1">
              <button
                v-for="page in Math.min(totalPages, 7)"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-2 py-1 text-sm font-medium rounded-lg border transition-colors',
                  page === currentPage
                    ? 'bg-blue-600 border-blue-600 text-white'
                    : 'text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'
                ]"
              >
                {{ page }}
              </button>
              
              <!-- Show dots if there are more pages -->
              <span v-if="totalPages > 7" class="px-2 py-1 text-sm text-gray-500 dark:text-gray-400">
                ...
              </span>
              
              <!-- Last page if not already shown -->
              <button
                v-if="totalPages > 7"
                @click="goToPage(totalPages)"
                :class="[
                  'px-2 py-1 text-sm font-medium rounded-lg border transition-colors',
                  totalPages === currentPage
                    ? 'bg-blue-600 border-blue-600 text-white'
                    : 'text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'
                ]"
              >
                {{ totalPages }}
              </button>
            </div>

            <!-- Next Button -->
            <button
              @click="goToNextPage"
              :disabled="currentPage === totalPages"
              class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              次へ
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
              <div class="flex flex-wrap gap-1 mt-2">
                <span 
                  v-for="tag in event.tags.slice(0, 3)" 
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
        <div v-else-if="!isLoading && filteredEvents.length === 0" class="text-center py-12">
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

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useLanguage } from '~/composables/useLanguage'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import BackButton from '~/components/BackButton.vue'
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

// Reactive variables
const isLoading = ref(false)
const error = ref(null)
const events = ref([])
const selectedPrefecture = ref('')
const selectedDate = ref('')
const selectedCategory = ref('')
const searchKeyword = ref('')
const currentPage = ref(1)
const itemsPerPage = 10
const isFiltersExpanded = ref(false)

// Available prefectures (matches the main app)
const availablePrefectures = [
  '北海道', '東京都', '大阪府', '京都府', '愛知県', '福岡県', 
  '広島県', '愛媛県', '福島県', '埼玉県', '新潟県', '山口県', 
  '徳島県', '鹿児島県'
]

// Options for CustomSelect components
const prefectureOptions = computed(() => [
  { value: '', label: 'すべて' },
  ...availablePrefectures.map(prefecture => ({
    value: prefecture,
    label: prefecture
  }))
])

// Available categories based on common event tags
const availableCategories = [
  '祭り', '花見', '紅葉', '花火', '文化', '伝統', 'グルメ', '音楽', 
  'スポーツ', '季節', '春', '夏', '秋', '冬', 'ライトアップ', '体験'
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
      const eventTags = event.tags || []
      return eventTags.some(tag => tag.includes(selectedCategory.value))
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

// Fetch events from API
const fetchEvents = async () => {
  isLoading.value = true
  error.value = null

  try {
    const response = await $fetch(`${config.public.apiBase}/api/events`, {
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


// Format date for display
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('ja-JP', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
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

// Filter events when filters change
const filterEvents = () => {
  // Reset to first page when filters change
  currentPage.value = 1
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
onMounted(() => {
  fetchEvents()
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
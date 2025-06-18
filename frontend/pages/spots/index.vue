<template>
  <div>
    <AppHeader />
    
    <main class="min-h-screen py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">観光スポット</h1>
          <p class="text-xl text-gray-600">日本各地の魅力的な観光スポットを発見しよう</p>
        </div>

        <!-- Filters -->
        <div class="card p-6 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">都道府県</label>
              <select
                v-model="filters.prefecture"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                @change="applyFilters"
              >
                <option value="">すべて</option>
                <option value="東京都">東京都</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">カテゴリ</label>
              <select
                v-model="filters.category"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                @change="applyFilters"
              >
                <option value="">すべて</option>
                <option value="観光施設">観光施設</option>
                <option value="寺院">寺院</option>
                <option value="神社">神社</option>
                <option value="庭園">庭園</option>
                <option value="動物園">動物園</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">検索</label>
              <BaseInput
                v-model="filters.search"
                placeholder="スポット名で検索"
                icon="lucide:search"
                @input="debounceSearch"
              />
            </div>
            
            <div class="flex items-end">
              <BaseButton
                variant="ghost"
                icon="lucide:x"
                @click="clearFilters"
              >
                フィルタをクリア
              </BaseButton>
            </div>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-12">
          <Icon name="lucide:loader-2" class="w-8 h-8 animate-spin mx-auto mb-4 text-primary-600" />
          <p class="text-gray-600">観光スポットを読み込み中...</p>
        </div>

        <!-- Spots Grid -->
        <div v-else-if="spots.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="spot in spots"
            :key="spot.id"
            class="card-hover cursor-pointer group"
            @click="$router.push(`/spots/${spot.id}`)"
          >
            <div class="aspect-video bg-gradient-to-br from-primary-100 to-purple-100 rounded-t-xl flex items-center justify-center">
              <Icon
                name="lucide:image"
                class="w-12 h-12 text-gray-400"
              />
            </div>
            
            <div class="p-6">
              <div class="flex items-center justify-between mb-3">
                <span 
                  class="text-sm font-medium px-3 py-1 rounded-full"
                  :class="getCategoryStyle(spot.category)"
                >
                  {{ spot.category }}
                </span>
                <div class="flex items-center space-x-1">
                  <Icon name="lucide:map-pin" class="w-4 h-4 text-gray-400" />
                  <span class="text-sm text-gray-500">{{ spot.prefecture }}</span>
                </div>
              </div>
              
              <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
                {{ spot.name }}
              </h3>
              
              <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                {{ spot.description }}
              </p>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                  <div v-if="spot.active_guides?.length" class="flex items-center space-x-1">
                    <Icon name="lucide:headphones" class="w-4 h-4 text-primary-500" />
                    <span class="text-primary-600">{{ spot.active_guides.length }}ガイド</span>
                  </div>
                  <div v-if="spot.admission_fee" class="flex items-center space-x-1">
                    <Icon name="lucide:yen-sign" class="w-4 h-4" />
                    <span>{{ spot.admission_fee }}</span>
                  </div>
                </div>
                
                <Icon 
                  name="lucide:arrow-right" 
                  class="w-5 h-5 text-gray-400 group-hover:text-primary-600 group-hover:translate-x-1 transition-all" 
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
          <Icon name="lucide:map-pin" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
          <h3 class="text-xl font-semibold text-gray-900 mb-2">観光スポットが見つかりませんでした</h3>
          <p class="text-gray-600 mb-6">検索条件を変更して再度お試しください</p>
          <BaseButton
            variant="ghost"
            @click="clearFilters"
          >
            フィルタをクリア
          </BaseButton>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import type { TouristSpot } from '~/types'

useHead({
  title: '観光スポット'
})

const route = useRoute()
const { $api } = useNuxtApp()

const loading = ref(true)
const spots = ref<TouristSpot[]>([])

const filters = reactive({
  prefecture: (route.query.prefecture as string) || '',
  category: '',
  search: ''
})

let searchTimeout: NodeJS.Timeout

const getCategoryStyle = (category?: string) => {
  const styles = {
    '観光施設': 'bg-blue-100 text-blue-800',
    '寺院': 'bg-purple-100 text-purple-800',
    '神社': 'bg-red-100 text-red-800',
    '庭園': 'bg-green-100 text-green-800',
    '動物園': 'bg-yellow-100 text-yellow-800'
  }
  return styles[category as keyof typeof styles] || 'bg-gray-100 text-gray-800'
}

const fetchSpots = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.prefecture) params.append('prefecture', filters.prefecture)
    if (filters.category) params.append('category', filters.category)
    
    const url = `/tourist-spots${params.toString() ? '?' + params.toString() : ''}`
    const response = await $api<TouristSpot[]>(url)
    
    let filteredSpots = response
    if (filters.search) {
      filteredSpots = response.filter(spot => 
        spot.name.toLowerCase().includes(filters.search.toLowerCase()) ||
        spot.description.toLowerCase().includes(filters.search.toLowerCase())
      )
    }
    
    spots.value = filteredSpots
  } catch (error) {
    console.error('Error fetching spots:', error)
    spots.value = []
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchSpots()
}

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchSpots()
  }, 300)
}

const clearFilters = () => {
  filters.prefecture = ''
  filters.category = ''
  filters.search = ''
  fetchSpots()
}

onMounted(() => {
  fetchSpots()
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
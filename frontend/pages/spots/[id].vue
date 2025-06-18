<template>
  <div>
    <AppHeader />
    
    <main class="min-h-screen">
      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center min-h-screen">
        <div class="text-center">
          <Icon name="lucide:loader-2" class="w-12 h-12 animate-spin mx-auto mb-4 text-primary-600" />
          <p class="text-gray-600">観光スポットを読み込み中...</p>
        </div>
      </div>

      <!-- Spot Details -->
      <div v-else-if="spot" class="pb-16">
        <!-- Hero Section -->
        <section class="relative h-96 bg-gradient-to-br from-primary-100 via-purple-100 to-blue-100">
          <div class="absolute inset-0 flex items-center justify-center">
            <Icon name="lucide:image" class="w-24 h-24 text-gray-400" />
          </div>
          
          <!-- Breadcrumbs -->
          <div class="absolute top-4 left-4 right-4">
            <nav class="flex" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-2 text-sm">
                <li>
                  <NuxtLink to="/" class="text-white hover:text-gray-200 transition-colors">
                    ホーム
                  </NuxtLink>
                </li>
                <Icon name="lucide:chevron-right" class="w-4 h-4 text-white" />
                <li>
                  <NuxtLink to="/spots" class="text-white hover:text-gray-200 transition-colors">
                    観光スポット
                  </NuxtLink>
                </li>
                <Icon name="lucide:chevron-right" class="w-4 h-4 text-white" />
                <li>
                  <span class="text-gray-200">{{ spot.name }}</span>
                </li>
              </ol>
            </nav>
          </div>

          <!-- Spot Title -->
          <div class="absolute bottom-8 left-8 right-8">
            <div class="glass rounded-xl p-6">
              <div class="flex items-center justify-between mb-4">
                <span 
                  class="text-sm font-medium px-3 py-1 rounded-full"
                  :class="getCategoryStyle(spot.category)"
                >
                  {{ spot.category }}
                </span>
                <div class="flex items-center space-x-2 text-white">
                  <Icon name="lucide:map-pin" class="w-4 h-4" />
                  <span class="text-sm">{{ spot.prefecture }} {{ spot.city }}</span>
                </div>
              </div>
              
              <h1 class="text-4xl font-bold text-white mb-2">{{ spot.name }}</h1>
              <p class="text-lg text-gray-100">{{ spot.description }}</p>
            </div>
          </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
              <!-- Audio Guides Section -->
              <section v-if="spot.active_guides?.length" class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                  <Icon name="lucide:headphones" class="w-6 h-6 mr-3 text-primary-600" />
                  音声ガイド
                </h2>
                
                <div class="space-y-6">
                  <div
                    v-for="guide in spot.active_guides"
                    :key="guide.id"
                    class="card p-6"
                  >
                    <div class="flex items-start justify-between mb-4">
                      <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ guide.title }}</h3>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                          <span v-if="guide.duration_minutes" class="flex items-center space-x-1">
                            <Icon name="lucide:clock" class="w-4 h-4" />
                            <span>{{ guide.duration_minutes }}分</span>
                          </span>
                          <span class="flex items-center space-x-1">
                            <Icon name="lucide:tag" class="w-4 h-4" />
                            <span>{{ guide.type }}</span>
                          </span>
                        </div>
                      </div>
                      
                      <BaseButton
                        v-if="guide.active_audio_guides?.length"
                        size="sm"
                        icon="lucide:play"
                        @click="playAudio(guide)"
                      >
                        再生
                      </BaseButton>
                    </div>
                    
                    <p class="text-gray-600 mb-4">{{ guide.content }}</p>
                    
                    <!-- Highlights -->
                    <div v-if="guide.highlights?.length" class="mb-4">
                      <h4 class="text-sm font-medium text-gray-900 mb-2">ハイライト</h4>
                      <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                        <li v-for="highlight in guide.highlights" :key="highlight">
                          {{ highlight }}
                        </li>
                      </ul>
                    </div>

                    <!-- Audio Player -->
                    <div v-if="currentGuide?.id === guide.id && currentAudio" class="mt-4 p-4 bg-gray-50 rounded-lg">
                      <AudioPlayer
                        :audio-url="currentAudio.audio_file_url || ''"
                        :title="guide.title"
                        @ended="onAudioEnded"
                      />
                    </div>
                  </div>
                </div>
              </section>

              <!-- Description Section -->
              <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">詳細情報</h2>
                <div class="card p-6">
                  <p class="text-gray-700 leading-relaxed">{{ spot.description }}</p>
                </div>
              </section>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
              <!-- Quick Info -->
              <div class="card p-6 mb-8 sticky top-24">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">基本情報</h3>
                
                <div class="space-y-4">
                  <div v-if="spot.address" class="flex items-start space-x-3">
                    <Icon name="lucide:map-pin" class="w-5 h-5 text-gray-400 mt-0.5" />
                    <div>
                      <p class="text-sm font-medium text-gray-900">住所</p>
                      <p class="text-sm text-gray-600">{{ spot.address }}</p>
                    </div>
                  </div>
                  
                  <div v-if="spot.phone" class="flex items-start space-x-3">
                    <Icon name="lucide:phone" class="w-5 h-5 text-gray-400 mt-0.5" />
                    <div>
                      <p class="text-sm font-medium text-gray-900">電話番号</p>
                      <p class="text-sm text-gray-600">{{ spot.phone }}</p>
                    </div>
                  </div>
                  
                  <div v-if="spot.opening_hours" class="flex items-start space-x-3">
                    <Icon name="lucide:clock" class="w-5 h-5 text-gray-400 mt-0.5" />
                    <div>
                      <p class="text-sm font-medium text-gray-900">営業時間</p>
                      <p class="text-sm text-gray-600">{{ spot.opening_hours }}</p>
                    </div>
                  </div>
                  
                  <div v-if="spot.admission_fee" class="flex items-start space-x-3">
                    <Icon name="lucide:yen-sign" class="w-5 h-5 text-gray-400 mt-0.5" />
                    <div>
                      <p class="text-sm font-medium text-gray-900">入場料</p>
                      <p class="text-sm text-gray-600">{{ spot.admission_fee }}</p>
                    </div>
                  </div>
                  
                  <div v-if="spot.website" class="flex items-start space-x-3">
                    <Icon name="lucide:external-link" class="w-5 h-5 text-gray-400 mt-0.5" />
                    <div>
                      <p class="text-sm font-medium text-gray-900">公式サイト</p>
                      <a 
                        :href="spot.website" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="text-sm text-primary-600 hover:text-primary-800 transition-colors"
                      >
                        サイトを開く
                      </a>
                    </div>
                  </div>
                </div>

                <!-- Access Info -->
                <div v-if="spot.access_info" class="mt-6 pt-6 border-t border-gray-200">
                  <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                    <Icon name="lucide:train" class="w-4 h-4 mr-2" />
                    アクセス
                  </h4>
                  <p class="text-sm text-gray-600">{{ spot.access_info }}</p>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                  <BaseButton
                    full-width
                    icon="lucide:heart"
                    variant="ghost"
                  >
                    お気に入りに追加
                  </BaseButton>
                  
                  <BaseButton
                    full-width
                    icon="lucide:share-2"
                    variant="ghost"
                    @click="shareSpot"
                  >
                    シェア
                  </BaseButton>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="flex items-center justify-center min-h-screen">
        <div class="text-center">
          <Icon name="lucide:map-pin" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
          <h3 class="text-xl font-semibold text-gray-900 mb-2">観光スポットが見つかりませんでした</h3>
          <p class="text-gray-600 mb-6">指定されたスポットは存在しないか、削除された可能性があります</p>
          <BaseButton @click="$router.push('/spots')">
            観光スポット一覧に戻る
          </BaseButton>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import type { TouristSpot, Guide, AudioGuide } from '~/types'

const route = useRoute()
const { $api } = useNuxtApp()

const loading = ref(true)
const spot = ref<TouristSpot | null>(null)
const currentGuide = ref<Guide | null>(null)
const currentAudio = ref<AudioGuide | null>(null)

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

const fetchSpot = async () => {
  loading.value = true
  try {
    const response = await $api<TouristSpot>(`/tourist-spots/${route.params.id}`)
    spot.value = response
    
    // Set page title
    useHead({
      title: response.name
    })
  } catch (error) {
    console.error('Error fetching spot:', error)
    spot.value = null
  } finally {
    loading.value = false
  }
}

const playAudio = (guide: Guide) => {
  currentGuide.value = guide
  currentAudio.value = guide.active_audio_guides?.[0] || null
}

const onAudioEnded = () => {
  currentGuide.value = null
  currentAudio.value = null
}

const shareSpot = async () => {
  if (!spot.value) return
  
  if (navigator.share) {
    try {
      await navigator.share({
        title: spot.value.name,
        text: spot.value.description,
        url: window.location.href
      })
    } catch (error) {
      console.error('Error sharing:', error)
    }
  } else {
    // Fallback: copy to clipboard
    try {
      await navigator.clipboard.writeText(window.location.href)
      // Show toast notification here
      console.log('URLをコピーしました')
    } catch (error) {
      console.error('Error copying to clipboard:', error)
    }
  }
}

onMounted(() => {
  fetchSpot()
})
</script>
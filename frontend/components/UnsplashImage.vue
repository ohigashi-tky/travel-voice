<template>
  <div class="relative h-full w-full">
    <img 
      :src="imageUrl" 
      :alt="alt"
      class="w-full h-full object-cover"
      :class="imageClass"
      loading="lazy"
      @error="handleImageError"
    />
    
    <!-- Credit表記削除 -->
    
    <!-- その他のオーバーレイ -->
    <slot />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useUnsplashImages } from '~/composables/useUnsplashImages'

interface Props {
  spotName: string
  alt?: string
  imageClass?: string
  showCredit?: boolean
  fallbackUrl?: string
}

const props = withDefaults(defineProps<Props>(), {
  alt: '',
  imageClass: '',
  showCredit: false,
  fallbackUrl: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&h=600&fit=crop&auto=format&q=80'
})

const { searchImages, getImageCredit } = useUnsplashImages()

const imageUrl = ref(props.fallbackUrl)
const credit = ref('')

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.src = props.fallbackUrl
}

const loadImage = async () => {
  try {
    const images = await searchImages(props.spotName)
    if (images.length > 0) {
      const firstImage = images[0]
      imageUrl.value = firstImage.urls.regular
      credit.value = props.showCredit ? getImageCredit(firstImage) : ''
    } else {
      imageUrl.value = props.fallbackUrl
      credit.value = props.showCredit ? 'Photo via Unsplash' : ''
    }
  } catch (error) {
    console.error(`Failed to load image for ${props.spotName}:`, error)
    imageUrl.value = props.fallbackUrl
    credit.value = props.showCredit ? 'Photo via Unsplash' : ''
  }
}

// スポット名が変更されたら画像を再読み込み
watch(() => props.spotName, loadImage, { immediate: true })

onMounted(() => {
  if (props.spotName) {
    loadImage()
  }
})
</script>
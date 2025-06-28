export interface PlacePhoto {
  photo_reference: string
  width: number
  height: number
  url: string
  attribution: string
}

export interface PlacePhotosResponse {
  success: boolean
  place_id?: string
  place_name?: string
  photos: PlacePhoto[]
  total_photos?: number
  message?: string
}

export const useGooglePlacePhotos = () => {
  // Cache for photos to avoid repeated API calls
  const photoCache = new Map<string, PlacePhotosResponse>()
  const loading = ref(false)
  const error = ref<string | null>(null)

  /**
   * Get photos for a tourist spot by name or place_id
   */
  const getPlacePhotos = async (spotName: string, placeId?: string): Promise<PlacePhotosResponse> => {
    const cacheKey = placeId || spotName
    
    // Check cache first
    if (photoCache.has(cacheKey)) {
      return photoCache.get(cacheKey)!
    }

    loading.value = true
    error.value = null

    try {
      const response = await $fetch<PlacePhotosResponse>('/api/google-places/photos', {
        method: 'POST',
        body: {
          spotName,
          placeId
        }
      })

      // Cache successful responses
      if (response.success) {
        photoCache.set(cacheKey, response)
      } else {
        console.warn('⚠️ No photos found for:', spotName)
      }

      return response

    } catch (err: any) {
      console.error('❌ Failed to fetch place photos:', err)
      error.value = err.message || 'Failed to fetch place photos'
      
      // Return empty result with fallback
      return {
        success: false,
        photos: [],
        message: error.value
      }
    } finally {
      loading.value = false
    }
  }

  /**
   * No fallback images - always use Google Place Photos
   */
  const getFallbackImages = (spotName: string): PlacePhoto[] => {
    // No fallback images - force usage of Google Place Photos only
    return []
  }

  /**
   * Get photos - Google Place Photos only, no fallback
   */
  const getPhotosWithFallback = async (spotName: string, placeId?: string): Promise<PlacePhoto[]> => {
    try {
      const response = await getPlacePhotos(spotName, placeId)
      
      if (response.success && response.photos.length > 0) {
        return response.photos
      }
      
      // No fallback - show placeholder or error state
      console.warn(`No Google Photos found for ${spotName}`)
      return []
      
    } catch (err) {
      console.error('Error fetching Google Place photos:', err)
      return []
    }
  }

  /**
   * Get primary photo URL (first photo) - Google Place Photos only
   */
  const getPrimaryPhotoUrl = async (spotName: string, placeId?: string): Promise<string | null> => {
    try {
      const photos = await getPhotosWithFallback(spotName, placeId)
      return photos[0]?.url || null
    } catch (err) {
      console.error('Error getting primary photo:', err)
      return null
    }
  }

  /**
   * Clear cache
   */
  const clearCache = () => {
    photoCache.clear()
  }

  /**
   * Preload photos for multiple spots
   */
  const preloadPhotos = async (spots: Array<{ name: string; place_id?: string }>) => {
    const promises = spots.map(spot => getPlacePhotos(spot.name, spot.place_id))
    await Promise.allSettled(promises)
  }

  return {
    loading: readonly(loading),
    error: readonly(error),
    getPlacePhotos,
    getPhotosWithFallback,
    getPrimaryPhotoUrl,
    getFallbackImages,
    clearCache,
    preloadPhotos
  }
}
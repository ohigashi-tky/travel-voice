export default defineEventHandler(async (event) => {
  try {
    const body = await readBody(event)
    const { spotName, placeId } = body

    if (!spotName && !placeId) {
      throw createError({
        statusCode: 400,
        statusMessage: 'Either spotName or placeId is required'
      })
    }

    const config = useRuntimeConfig()
    const apiKey = config.googleMapsApiKey
    
    if (!apiKey) {
      console.error('❌ Google Maps API key not found in runtime config')
      throw createError({
        statusCode: 500,
        statusMessage: 'Google Maps API key not configured'
      })
    }

    let finalPlaceId = placeId

    // If no placeId provided, search for the place first
    if (!finalPlaceId && spotName) {
      console.log('🔍 Searching for place:', spotName)
      const searchUrl = `https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=${encodeURIComponent(spotName)}&inputtype=textquery&fields=place_id,name&key=${apiKey}`
      
      const searchResponse = await $fetch(searchUrl)
      
      if (searchResponse.status === 'OK' && searchResponse.candidates?.length > 0) {
        finalPlaceId = searchResponse.candidates[0].place_id
        console.log('✅ Found place_id:', finalPlaceId)
      } else {
        console.error('❌ Place not found:', searchResponse)
        throw createError({
          statusCode: 404,
          statusMessage: `Place not found for: ${spotName} (Status: ${searchResponse.status})`
        })
      }
    }

    // Get place details including photos
    const detailsUrl = `https://maps.googleapis.com/maps/api/place/details/json?place_id=${finalPlaceId}&fields=photos,name&key=${apiKey}`
    
    const detailsResponse = await $fetch(detailsUrl)
    
    if (detailsResponse.status !== 'OK') {
      console.error('❌ Place details error:', detailsResponse)
      throw createError({
        statusCode: 404,
        statusMessage: `Place details not found: ${detailsResponse.status}`
      })
    }

    const photos = detailsResponse.result?.photos || []
    console.log('📷 Found photos count:', photos.length)
    
    if (photos.length === 0) {
      console.warn('⚠️ No photos available for place:', spotName)
      return {
        success: false,
        message: 'No photos available for this place',
        photos: []
      }
    }

    // Process photos and generate URLs
    const processedPhotos = photos.slice(0, 5).map((photo: any, index: number) => {
      const maxWidth = index === 0 ? 800 : 600 // First photo larger
      const maxHeight = index === 0 ? 600 : 400
      
      const photoUrl = `https://maps.googleapis.com/maps/api/place/photo?maxwidth=${maxWidth}&maxheight=${maxHeight}&photo_reference=${photo.photo_reference}&key=${apiKey}`
      
      return {
        photo_reference: photo.photo_reference,
        width: photo.width,
        height: photo.height,
        url: photoUrl,
        attribution: ''
      }
    })

    console.log('✅ Found', processedPhotos.length, 'photos for', spotName)

    return {
      success: true,
      place_id: finalPlaceId,
      place_name: detailsResponse.result?.name || spotName,
      photos: processedPhotos,
      total_photos: photos.length
    }

  } catch (error: any) {
    console.error('Google Places Photos API error:', error)
    
    // Return error details
    if (error.statusCode) {
      throw error
    }
    
    throw createError({
      statusCode: 500,
      statusMessage: `Failed to fetch place photos: ${error.message}`
    })
  }
})
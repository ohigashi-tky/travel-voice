import { defineEventHandler, readBody } from 'h3'

export default defineEventHandler(async (event) => {
  setHeader(event, 'Content-Type', 'application/json')
  
  try {
    const body = await readBody(event)
    const { query, per_page = 10 } = body || {}

    if (!query) {
      return {
        error: 'Search query is required'
      }
    }

    const UNSPLASH_ACCESS_KEY = process.env.UNSPLASH_ACCESS_KEY

    if (!UNSPLASH_ACCESS_KEY) {
      console.error('UNSPLASH_ACCESS_KEY is not set')
      return {
        error: 'Unsplash API key not configured'
      }
    }

    console.log('Unsplash API request:', { query, per_page })

    // Unsplash Search API を呼び出し
    const response = await fetch(`https://api.unsplash.com/search/photos?query=${encodeURIComponent(query)}&per_page=${per_page}&orientation=landscape`, {
      headers: {
        'Authorization': `Client-ID ${UNSPLASH_ACCESS_KEY}`,
        'Accept-Version': 'v1'
      }
    })

    if (!response.ok) {
      const errorText = await response.text()
      console.error('Unsplash API Error:', response.status, errorText)
      return {
        error: 'Failed to fetch images from Unsplash',
        details: `Status: ${response.status}`
      }
    }

    const data = await response.json()
    console.log('Unsplash API response:', { total: data.total, results_count: data.results?.length })
    
    // 必要な情報のみを抽出
    const images = data.results?.map((photo: any) => ({
      id: photo.id,
      urls: {
        small: photo.urls.small,
        regular: photo.urls.regular,
        full: photo.urls.full
      },
      alt_description: photo.alt_description || photo.description,
      user: {
        name: photo.user.name,
        username: photo.user.username,
        profile_url: `https://unsplash.com/@${photo.user.username}`
      },
      download_url: photo.links.download_location
    })) || []

    console.log('Processed images:', images.length)

    return {
      images,
      total: data.total || 0,
      total_pages: data.total_pages || 0
    }

  } catch (error) {
    console.error('Unsplash API Error:', error)
    return {
      error: 'Internal server error',
      details: error instanceof Error ? error.message : 'Unknown error'
    }
  }
})
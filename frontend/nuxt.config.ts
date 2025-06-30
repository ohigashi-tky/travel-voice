export default defineNuxtConfig({
  devtools: { enabled: false },
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@vueuse/nuxt',
    '@nuxtjs/google-fonts'
  ],
  nitro: {
    devProxy: {
      '/storage': {
        target: 'http://localhost:8000/storage',
        changeOrigin: true
      }
    }
  },
  googleFonts: {
    families: {
      'Noto+Sans+JP': [300, 400, 500, 700],
      'Inter': [300, 400, 500, 600, 700]
    }
  },
  css: ['~/assets/css/main.css'],
  runtimeConfig: {
    // Private keys (only available on server-side)
    googleMapsApiKey: process.env.GOOGLE_MAPS_API_KEY,
    public: {
      apiBase: process.env.NUXT_API_BASE_URL || 'http://localhost:8000/api',
      googleMapsMapId: process.env.NUXT_PUBLIC_GOOGLE_MAPS_MAP_ID
    }
  },
  typescript: {
    strict: false,
    typeCheck: false
  }
})
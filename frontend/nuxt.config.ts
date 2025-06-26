export default defineNuxtConfig({
  devtools: { enabled: false },
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@vueuse/nuxt',
    '@nuxtjs/google-fonts'
  ],
  googleFonts: {
    families: {
      'Noto+Sans+JP': [300, 400, 500, 700],
      'Inter': [300, 400, 500, 600, 700]
    }
  },
  css: ['~/assets/css/main.css'],
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_API_BASE_URL || 'http://localhost:8000/api',
      googleMapsApiKey: process.env.NUXT_PUBLIC_GOOGLE_MAPS_API_KEY,
      googleMapsMapId: process.env.NUXT_PUBLIC_GOOGLE_MAPS_MAP_ID
    }
  },
  typescript: {
    strict: false,
    typeCheck: false
  }
})
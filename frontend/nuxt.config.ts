export default defineNuxtConfig({
  devtools: { enabled: false },
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@vueuse/nuxt',
    '@nuxtjs/google-fonts',
    '@vite-pwa/nuxt'
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
    googleMapsApiKey: process.env.NUXT_GOOGLE_MAPS_API_KEY || process.env.GOOGLE_MAPS_API_KEY,
    openrouterApiKey: process.env.NUXT_OPENROUTER_API_KEY || process.env.OPENROUTER_API_KEY,
    openrouterModel: process.env.NUXT_OPENROUTER_MODEL || process.env.OPENROUTER_MODEL || 'google/gemini-2.5-flash-lite-preview-06-17',
    // Server-side API base URL (for SSR)
    apiBaseServer: process.env.NUXT_API_BASE_URL || 'http://localhost:8000',
    public: {
      // Client-side API base URL - default to Railway, only use localhost for local dev
      apiBase: 'https://travel-voice-production.up.railway.app',
      googleMapsMapId: process.env.NUXT_PUBLIC_GOOGLE_MAPS_MAP_ID
    }
  },
  typescript: {
    strict: false,
    typeCheck: false
  },
  app: {
    head: {
      meta: [
        { name: 'viewport', content: 'width=device-width, initial-scale=1, viewport-fit=cover' },
        { name: 'apple-mobile-web-app-capable', content: 'yes' },
        { name: 'apple-mobile-web-app-status-bar-style', content: 'black-translucent' },
        { name: 'apple-mobile-web-app-title', content: 'おうち旅行' },
        { name: 'theme-color', content: '#2563eb' },
        { name: 'mobile-web-app-capable', content: 'yes' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'icon', type: 'image/png', sizes: '32x32', href: '/favicon-32x32.png' },
        { rel: 'icon', type: 'image/png', sizes: '16x16', href: '/favicon-16x16.png' },
        { rel: 'apple-touch-icon', href: '/apple-touch-icon.png' },
        { rel: 'mask-icon', href: '/app-icon.svg', color: '#2563eb' }
      ]
    }
  },
  pwa: {
    registerType: 'autoUpdate',
    workbox: {
      navigateFallback: '/',
      navigateFallbackAllowlist: [/^(?!\/__).*/],
      globPatterns: ['**/*.{js,css,html,png,svg,ico}'],
      navigationPreload: true,
      runtimeCaching: [
        {
          urlPattern: /^https:\/\/maps\.googleapis\.com\/.*/i,
          handler: 'StaleWhileRevalidate',
          options: {
            cacheName: 'google-maps-cache',
            expiration: {
              maxEntries: 50,
              maxAgeSeconds: 60 * 60 * 24 // 1 day
            }
          }
        },
        {
          urlPattern: /^http:\/\/localhost:8000\/api\/.*/i,
          handler: 'NetworkFirst',
          options: {
            cacheName: 'api-cache',
            expiration: {
              maxEntries: 100,
              maxAgeSeconds: 60 * 60 // 1 hour
            }
          }
        }
      ]
    },
    manifest: {
      name: 'おうち旅行 - 穏やかな音声で日本を巡る',
      short_name: 'おうち旅行',
      description: '穏やかな音声で日本全国を巡るバーチャル旅行体験アプリ。歴史やエピソードを聞きながら、家にいながら本格的な旅行気分を味わえます。',
      theme_color: '#2563eb',
      background_color: '#ffffff',
      display: 'standalone',
      orientation: 'portrait',
      scope: '/',
      start_url: '/',
      display_override: ['standalone', 'minimal-ui'],
      prefer_related_applications: false,
      icons: [
        {
          src: '/icon-192x192.png',
          sizes: '192x192',
          type: 'image/png'
        },
        {
          src: '/icon-512x512.png',
          sizes: '512x512',
          type: 'image/png'
        },
        {
          src: '/icon-maskable-192x192.png',
          sizes: '192x192',
          type: 'image/png',
          purpose: 'maskable'
        },
        {
          src: '/icon-maskable-512x512.png',
          sizes: '512x512',
          type: 'image/png',
          purpose: 'maskable'
        }
      ],
      categories: ['travel', 'entertainment', 'education'],
      screenshots: [
        {
          src: '/screenshot-wide.png',
          sizes: '1024x640',
          type: 'image/png',
          form_factor: 'wide'
        },
        {
          src: '/screenshot-narrow.png',
          sizes: '640x1136',
          type: 'image/png',
          form_factor: 'narrow'
        }
      ]
    }
  }
})
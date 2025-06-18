export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore()
  
  // Initialize auth state if not already done
  if (process.client) {
    authStore.initializeAuth()
  }
  
  // If user is already authenticated, redirect to home
  if (authStore.isAuthenticated) {
    return navigateTo('/')
  }
})
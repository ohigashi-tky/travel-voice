export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()
  
  // Skip auth check on server side
  if (process.server) {
    return
  }
  
  // On client side, wait for proper initialization
  if (process.client) {
    // Wait for auth initialization to complete
    await authStore.initializeAuth()
    
    // Add a small additional delay to ensure everything is ready
    await new Promise(resolve => setTimeout(resolve, 50))
    
    // Check if user is authenticated after initialization
    if (!authStore.isAuthenticated) {
      console.log('User not authenticated, redirecting to login')
      return navigateTo('/login')
    } else {
      console.log('User authenticated, allowing access to:', to.path)
    }
  }
})
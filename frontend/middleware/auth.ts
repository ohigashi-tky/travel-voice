export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()
  
  // Ensure auth state is initialized on client side
  if (process.client) {
    authStore.initializeAuth()
    // Small delay to ensure localStorage is read
    await new Promise(resolve => setTimeout(resolve, 10))
  }
  
  // Check if user is authenticated
  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }
})
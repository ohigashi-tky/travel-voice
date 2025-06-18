export default defineNuxtPlugin(() => {
  const authStore = useAuthStore()
  
  // Initialize auth state on client side
  authStore.initializeAuth()
})
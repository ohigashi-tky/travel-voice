export default defineNuxtPlugin(async () => {
  const authStore = useAuthStore()
  
  // Initialize auth state asynchronously on client side
  await authStore.initializeAuth()
  
  // Ensure auth state is ready before app continues
  await nextTick()
})
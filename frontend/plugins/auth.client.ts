export default defineNuxtPlugin(async () => {
  const authStore = useAuthStore()
  
  // Initialize auth state synchronously on client side
  authStore.initializeAuth()
  
  // Ensure auth state is ready before app continues
  await nextTick()
})
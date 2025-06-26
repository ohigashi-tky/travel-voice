import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export interface User {
  id: number
  name: string
  email: string
}

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const isAuthenticated = computed(() => !!user.value)

  // Actions
  const login = async (email: string, password: string) => {
    isLoading.value = true
    error.value = null

    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Mock authentication - in real app, this would be an API call
      if (email === 'demo@example.com' && password === 'TravelGuide2024!') {
        user.value = {
          id: 1,
          name: '田中太郎',
          email: email
        }
        
        // Save to localStorage
        localStorage.setItem('user', JSON.stringify(user.value))
        
        return { success: true }
      } else {
        throw new Error('メールアドレスまたはパスワードが正しくありません')
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'ログインに失敗しました'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const register = async (name: string, email: string, password: string) => {
    isLoading.value = true
    error.value = null

    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Mock registration - in real app, this would be an API call
      user.value = {
        id: Date.now(), // Mock ID
        name: name,
        email: email
      }
      
      // Save to localStorage
      localStorage.setItem('user', JSON.stringify(user.value))
      
      return { success: true }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'アカウント登録に失敗しました'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const logout = () => {
    user.value = null
    error.value = null
    localStorage.removeItem('user')
  }

  const initializeAuth = async () => {
    // Only run on client side
    if (process.client) {
      try {
        // Add a small delay to ensure localStorage is available
        await new Promise(resolve => setTimeout(resolve, 10))
        
        // Check if user is logged in from localStorage
        const savedUser = localStorage.getItem('user')
        if (savedUser && savedUser !== 'null') {
          const parsedUser = JSON.parse(savedUser)
          if (parsedUser && parsedUser.id) {
            user.value = parsedUser
            console.log('認証状態を復元しました:', parsedUser.name)
          } else {
            localStorage.removeItem('user')
          }
        }
      } catch (error) {
        console.error('認証状態の復元エラー:', error)
        localStorage.removeItem('user')
      }
    }
  }

  return {
    // State
    user,
    isLoading,
    error,
    // Getters
    isAuthenticated,
    // Actions
    login,
    register,
    logout,
    initializeAuth
  }
})
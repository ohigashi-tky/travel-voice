import type { LoginCredentials, RegisterCredentials, User, AuthResponse } from '~/types'

export const useAuth = () => {
  const user = useState<User | null>('auth.user', () => null)
  const token = useCookie('auth-token', {
    default: () => null,
    httpOnly: false,
    secure: true,
    sameSite: 'strict'
  })

  const { $api } = useNuxtApp()

  const login = async (credentials: LoginCredentials): Promise<void> => {
    try {
      const response = await $api<AuthResponse>('/login', {
        method: 'POST',
        body: credentials
      })

      user.value = response.user
      token.value = response.token
      
      await navigateTo('/')
    } catch (error) {
      throw error
    }
  }

  const register = async (credentials: RegisterCredentials): Promise<void> => {
    try {
      const response = await $api<AuthResponse>('/register', {
        method: 'POST',
        body: credentials
      })

      user.value = response.user
      token.value = response.token
      
      await navigateTo('/')
    } catch (error) {
      throw error
    }
  }

  const logout = async (): Promise<void> => {
    try {
      if (token.value) {
        await $api('/logout', {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token.value}`
          }
        })
      }
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      token.value = null
      await navigateTo('/login')
    }
  }

  const fetchUser = async (): Promise<void> => {
    if (!token.value) return

    try {
      const response = await $api<User>('/user', {
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })
      user.value = response
    } catch (error) {
      console.error('Fetch user error:', error)
      token.value = null
      user.value = null
    }
  }

  const isLoggedIn = computed(() => !!user.value && !!token.value)

  return {
    user: readonly(user),
    isLoggedIn,
    login,
    register,
    logout,
    fetchUser
  }
}
<template>
  <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
          観光ガイドアプリ
        </h1>
        <p class="text-lg text-gray-600 mb-8">
          日本の美しい観光地を発見しよう
        </p>
        <h2 class="text-2xl font-semibold text-gray-900">
          新規登録
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          新しいアカウントを作成してください
        </p>
      </div>

      <!-- Register Form -->
      <form
        class="mt-8 space-y-6"
        @submit.prevent="handleRegister"
      >
        <div class="card p-6 space-y-4">
          <BaseInput
            v-model="form.name"
            type="text"
            label="お名前"
            placeholder="山田太郎"
            icon="lucide:user"
            required
            :error="errors.name"
          />

          <BaseInput
            v-model="form.email"
            type="email"
            label="メールアドレス"
            placeholder="your@email.com"
            icon="lucide:mail"
            required
            :error="errors.email"
          />

          <BaseInput
            v-model="form.password"
            type="password"
            label="パスワード"
            placeholder="8文字以上のパスワード"
            icon="lucide:lock"
            required
            :error="errors.password"
            hint="8文字以上で入力してください"
          />

          <BaseInput
            v-model="form.password_confirmation"
            type="password"
            label="パスワード確認"
            placeholder="パスワードを再入力"
            icon="lucide:lock"
            required
            :error="errors.password_confirmation"
          />

          <BaseButton
            type="submit"
            :loading="loading"
            full-width
            class="mt-6"
          >
            アカウント作成
          </BaseButton>
        </div>

        <div class="text-center">
          <p class="text-sm text-gray-600">
            すでにアカウントをお持ちの方は
            <NuxtLink
              to="/login"
              class="font-medium text-primary-600 hover:text-primary-500 transition-colors"
            >
              ログイン
            </NuxtLink>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { RegisterCredentials } from '~/types'

definePageMeta({
  layout: false,
  auth: false
})

useHead({
  title: '新規登録'
})

const { register } = useAuth()

const loading = ref(false)
const form = reactive<RegisterCredentials>({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  general: ''
})

const clearErrors = () => {
  errors.name = ''
  errors.email = ''
  errors.password = ''
  errors.password_confirmation = ''
  errors.general = ''
}

const validateForm = (): boolean => {
  clearErrors()
  let isValid = true

  if (!form.name.trim()) {
    errors.name = 'お名前を入力してください'
    isValid = false
  }

  if (!form.email) {
    errors.email = 'メールアドレスを入力してください'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = '有効なメールアドレスを入力してください'
    isValid = false
  }

  if (!form.password) {
    errors.password = 'パスワードを入力してください'
    isValid = false
  } else if (form.password.length < 8) {
    errors.password = 'パスワードは8文字以上で入力してください'
    isValid = false
  }

  if (!form.password_confirmation) {
    errors.password_confirmation = 'パスワード確認を入力してください'
    isValid = false
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'パスワードが一致しません'
    isValid = false
  }

  return isValid
}

const handleRegister = async () => {
  if (!validateForm()) return

  loading.value = true
  clearErrors()

  try {
    await register(form)
  } catch (error: any) {
    console.error('Register error:', error)
    
    if (error?.data?.errors) {
      const apiErrors = error.data.errors
      if (apiErrors.name) errors.name = apiErrors.name[0]
      if (apiErrors.email) errors.email = apiErrors.email[0]
      if (apiErrors.password) errors.password = apiErrors.password[0]
    } else if (error?.data?.message) {
      errors.general = error.data.message
    } else {
      errors.general = '登録に失敗しました。もう一度お試しください。'
    }
  } finally {
    loading.value = false
  }
}
</script>
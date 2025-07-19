<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 py-8 pb-24">
      <div class="max-w-4xl mx-auto">
        <!-- Page Title -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">利用規約</h1>
        </div>

        <!-- Terms Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="prose dark:prose-invert max-w-none">
            <h2>アプリについて</h2>
            <p>「おうち旅行」は、日本各地の観光地を音声ガイドとともに楽しめるアプリです。家にいながら日本の魅力を発見していただければと思います。</p>

            <h2>利用登録</h2>
            <p>現在は新規登録に非対応のため、デモアカウント(ログイン画面に情報記載)をご利用ください。</p>

            <h2>禁止事項</h2>
            <ul>
              <li>他の方に迷惑をかけるような使い方</li>
              <li>アプリの情報を商用目的で使用すること</li>
              <li>不正アクセスやシステムへの攻撃</li>
              <li>法律に違反するような使い方</li>
            </ul>

            <h2>サービスの停止について</h2>
            <p>以下の場合、一時的にサービスを停止することがあります：</p>
            <ul>
              <li>システムのメンテナンスやアップデート</li>
              <li>災害や技術的な問題</li>
              <li>その他、サービス提供が困難な場合</li>
            </ul>
            <p>緊急時は予告なく停止する場合があります。</p>

            <h2>免責事項</h2>
            <p>個人開発のため、以下の点をご理解ください：</p>
            <ul>
              <li>サービスの完全性や正確性を保証するものではありません</li>
              <li>利用により生じた損害については責任を負いかねます</li>
              <li>予告なくサービス内容を変更・終了する場合があります</li>
            </ul>

            <h2>個人情報の扱い</h2>
            <p>個人情報は適切に取り扱い、第三者に提供することはありません。</p>

            <h2>規約の変更</h2>
            <p>サービスの改善のため、この規約を変更する場合があります。</p>

            <h2>お問い合わせ</h2>
            <p>お問い合わせ機能は将来的に実装予定です。</p>

            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
              <p class="text-sm text-gray-500 dark:text-gray-400">
                作成日：2025年7月7日<br>
                最終更新日：2025年7月19日
              </p>
            </div>

          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ArrowLeft } from 'lucide-vue-next'
import AppHeader from '~/components/AppHeader.vue'

// Page meta
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: '利用規約 - おうち旅行',
  meta: [
    { name: 'description', content: 'おうち旅行サービスの利用規約をご確認いただけます。' }
  ]
})

// Reactive variables
const activeTab = ref('top')

// Navigation
const goBack = () => {
  window.history.length > 1 ? window.history.back() : navigateTo('/')
}
</script>

<style scoped>
.prose h2 {
  @apply text-lg font-bold text-gray-800 dark:text-white mt-6 mb-3 first:mt-0;
}

.prose p {
  @apply text-sm text-gray-700 dark:text-gray-300 mb-3 leading-relaxed;
}

.prose ul {
  @apply list-disc list-inside mb-3 space-y-1;
}

.prose li {
  @apply text-sm text-gray-700 dark:text-gray-300;
}
</style>

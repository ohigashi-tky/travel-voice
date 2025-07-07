<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 relative flex flex-col transition-colors duration-300">
    <!-- Header -->
    <AppHeader />

    <!-- Main Content -->
    <main class="flex-1 relative z-10 px-4 sm:px-6 lg:px-8 py-8 pb-24">
      <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
          <button 
            @click="goBack"
            class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors duration-300 group"
          >
            <ArrowLeft class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" />
            <span class="text-sm font-medium">戻る</span>
          </button>
        </div>

        <!-- Page Title -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">利用規約</h1>
          <p class="text-sm text-gray-600 dark:text-gray-300">おうち旅行サービス利用規約</p>
        </div>

        <!-- Terms Content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="prose dark:prose-invert max-w-none">
            
            <h2>第1条（適用）</h2>
            <p>本規約は、当サービス「おうち旅行」（以下「本サービス」）の利用条件を定めるものです。利用者の皆様には本規約に従って本サービスをご利用いただきます。</p>

            <h2>第2条（利用登録）</h2>
            <p>本サービスにおいて、登録希望者が所定の方法によって利用登録を申請することによって、利用登録が完了するものとします。</p>

            <h2>第3条（禁止事項）</h2>
            <p>利用者は、本サービスの利用にあたり、以下の行為をしてはなりません。</p>
            <ul>
              <li>法令または公序良俗に違反する行為</li>
              <li>犯罪行為に関連する行為</li>
              <li>本サービスの内容等、本サービスに含まれる著作権、商標権その他の知的財産権を侵害する行為</li>
              <li>他の利用者、またはその他第三者のサーバーまたはネットワークの機能を破壊したり、妨害したりする行為</li>
              <li>本サービスによって得られた情報を商業的に利用する行為</li>
              <li>本サービスの運営を妨害するおそれのある行為</li>
              <li>不正アクセスをし、またはこれを試みる行為</li>
              <li>その他、不適切と判断する行為</li>
            </ul>

            <h2>第4条（本サービスの提供の停止等）</h2>
            <p>以下のいずれかの事由があると判断した場合、利用者に事前に通知することなく本サービスの全部または一部の提供を停止または中断することができるものとします。</p>
            <ul>
              <li>本サービスにかかるコンピュータシステムの保守点検または更新を行う場合</li>
              <li>地震、落雷、火災、停電または天災などの不可抗力により、本サービスの提供が困難となった場合</li>
              <li>コンピュータまたは通信回線等が事故により停止した場合</li>
              <li>その他、本サービスの提供が困難と判断した場合</li>
            </ul>

            <h2>第5条（利用制限および登録抹消）</h2>
            <p>利用者が以下のいずれかに該当する場合には、事前の通知なく、投稿データを削除し、利用者に対して本サービスの全部もしくは一部の利用を制限しまたは利用者としての登録を抹消することができるものとします。</p>
            <ul>
              <li>本規約のいずれかの条項に違反した場合</li>
              <li>登録事項に虚偽の事実があることが判明した場合</li>
              <li>連絡に対し、一定期間返答がない場合</li>
              <li>本サービスについて、最終の利用から一定期間利用がない場合</li>
              <li>その他、本サービスの利用を適当でないと判断した場合</li>
            </ul>

            <h2>第6条（退会）</h2>
            <p>利用者は、所定の退会手続により、本サービスから退会できるものとします。</p>

            <h2>第7条（保証の否認および免責事項）</h2>
            <p>本サービスに事実上または法律上の瑕疵（安全性、信頼性、正確性、完全性、有効性、特定の目的への適合性、セキュリティなどに関する欠陥、エラーやバグ、権利侵害などを含みます。）がないことを明示的にも黙示的にも保証しておりません。</p>

            <h2>第8条（サービス内容の変更等）</h2>
            <p>利用者への事前の告知をもって、本サービスの内容を変更、追加または廃止することがあり、利用者はこれを承諾するものとします。</p>

            <h2>第9条（利用規約の変更）</h2>
            <p>以下の場合には、利用者の個別の同意を要せず、本規約を変更することができるものとします。</p>
            <ul>
              <li>本規約の変更が利用者の一般の利益に適合するとき。</li>
              <li>本規約の変更が本サービス利用契約の目的に反せず、かつ、変更の必要性、変更後の内容の相当性その他の変更に係る事情に照らして合理的なものであるとき。</li>
            </ul>

            <h2>第10条（個人情報の取扱い）</h2>
            <p>本サービスの利用によって取得する個人情報については、「プライバシーポリシー」に従い適切に取り扱うものとします。</p>

            <h2>第11条（通知または連絡）</h2>
            <p>利用者への通知または連絡は、所定の方法によって行うものとします。利用者から、別途定める方式に従った変更届け出がない限り、現在登録されている連絡先が有効なものとみなして当該連絡先へ通知または連絡を行い、これらは、発信時に利用者へ到達したものとみなします。</p>

            <h2>第12条（権利義務の譲渡の禁止）</h2>
            <p>利用者は、書面による事前の承諾なく、利用契約上の地位または本規約に基づく権利もしくは義務を第三者に譲渡し、または担保に供することはできません。</p>

            <h2>第13条（準拠法・裁判管轄）</h2>
            <p>本規約の解釈にあたっては、日本法を準拠法とします。本サービスに関して紛争が生じた場合には、東京地方裁判所を専属的合意管轄とします。</p>

            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
              <p class="text-sm text-gray-500 dark:text-gray-400">
                制定日：2025年7月7日<br>
                最終更新日：2025年7月7日
              </p>
            </div>

          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <AppFooter v-model="activeTab" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ArrowLeft } from 'lucide-vue-next'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'

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
import { ref, computed } from 'vue'

type Locale = 'ja' | 'en'

// グローバルな言語状態
const currentLocale = ref<Locale>('ja')

// 翻訳データ
const translations = {
  ja: {
    'Travel Voice': 'Travel Voice',
    '音声ガイドで観光を楽しもう': '音声ガイドで観光を楽しもう',
    '人気スポット': '人気スポット',
    '都道府県から探す': '都道府県から探す',
    'カテゴリから探す': 'カテゴリから探す',
    '設定': '設定',
    'ダークモード': 'ダークモード',
    '言語': '言語',
    'ログアウト': 'ログアウト',
    '戻る': '戻る',
    '検索': '検索',
    '観光地名を入力してください': '観光地名を入力してください',
    '検索結果': '検索結果',
    '概要': '概要',
    '見どころ': '見どころ',
    '歴史': '歴史',
    'フォトギャラリー': 'フォトギャラリー',
    '音声ガイド': '音声ガイド',
    '再生': '再生',
    'ホームに戻る': 'ホームに戻る',
    'すべて表示': 'すべて表示',
    '都道府県を選択': '都道府県を選択',
    '日本語': '日本語',
    'English': 'English'
  },
  en: {
    'Travel Voice': 'Travel Voice',
    '音声ガイドで観光を楽しもう': 'Discover Japan with Audio Guides',
    '人気スポット': 'Popular Spots',
    '都道府県から探す': 'Search by Prefecture',
    'カテゴリから探す': 'Search by Category',
    '設定': 'Settings',
    'ダークモード': 'Dark Mode',
    '言語': 'Language',
    'ログアウト': 'Logout',
    '戻る': 'Back',
    '検索': 'Search',
    '観光地名を入力してください': 'Enter tourist spot name',
    '検索結果': 'Search Results',
    '概要': 'Overview',
    '見どころ': 'Highlights',
    '歴史': 'History',
    'フォトギャラリー': 'Photo Gallery',
    '音声ガイド': 'Audio Guide',
    '再生': 'Play',
    'ホームに戻る': 'Back to Home',
    'すべて表示': 'Show All',
    '都道府県を選択': 'Select Prefecture',
    '日本語': 'Japanese',
    'English': 'English'
  }
} as const

interface TranslationCache {
  [key: string]: {
    [locale: string]: string
  }
}

const translationCache: TranslationCache = {}

export const useLanguage = () => {
  // ローカルストレージから言語設定を読み込み
  if (import.meta.client) {
    const savedLocale = localStorage.getItem('travel-voice-lang') as Locale
    if (savedLocale && (savedLocale === 'ja' || savedLocale === 'en')) {
      currentLocale.value = savedLocale
    }
  }

  // 利用可能な言語
  const availableLocales = [
    { code: 'ja' as const, name: '日本語' },
    { code: 'en' as const, name: 'English' }
  ]

  // 現在の言語
  const locale = computed(() => currentLocale.value)

  // 翻訳関数
  const t = (key: string): string => {
    return translations[currentLocale.value]?.[key] || key
  }

  // 言語切り替え関数
  const setLocale = (newLocale: Locale) => {
    currentLocale.value = newLocale
    if (import.meta.client) {
      localStorage.setItem('travel-voice-lang', newLocale)
    }
  }

  // 動的翻訳関数（観光地情報など）
  const translateText = async (text: string, targetLang: string = currentLocale.value): Promise<string> => {
    // 日本語の場合はそのまま返す
    if (currentLocale.value === 'ja') {
      return text
    }

    // キャッシュをチェック
    if (translationCache[text] && translationCache[text][targetLang]) {
      return translationCache[text][targetLang]
    }

    try {
      const response = await $fetch('/api/translate', {
        method: 'POST',
        body: {
          text,
          target: targetLang,
          source: 'ja'
        }
      })

      const translatedText = response.translatedText || text

      // キャッシュに保存
      if (!translationCache[text]) {
        translationCache[text] = {}
      }
      translationCache[text][targetLang] = translatedText

      return translatedText
    } catch (error) {
      console.error('Translation error:', error)
      return text
    }
  }

  // 動的翻訳のためのコンポーザブル
  const tDynamic = async (text: string): Promise<string> => {
    return await translateText(text, currentLocale.value)
  }

  return {
    locale,
    availableLocales,
    currentLocale,
    t,
    setLocale,
    translateText,
    tDynamic
  }
}
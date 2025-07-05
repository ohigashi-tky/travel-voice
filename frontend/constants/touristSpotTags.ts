/**
 * 観光地タグのマッピング設定
 * 将来的にはデータベースから取得する予定
 */

export interface SpotTagMapping {
  spotKeywords: string[]
  tags: string[]
}

export const TOURIST_SPOT_TAG_MAPPINGS: SpotTagMapping[] = [
  // 東京都
  { spotKeywords: ['スカイツリー'], tags: ['展望台', '現代建築', 'ランドマーク'] },
  { spotKeywords: ['浅草寺'], tags: ['歴史', '寺院', '伝統文化'] },
  { spotKeywords: ['明治神宮'], tags: ['神社', '自然', 'パワースポット'] },
  { spotKeywords: ['銀座'], tags: ['ショッピング', '高級エリア', 'グルメ'] },
  { spotKeywords: ['渋谷'], tags: ['若者文化', '繁華街', 'ファッション'] },
  { spotKeywords: ['新宿'], tags: ['ビジネス街', '娯楽', '交通'] },
  { spotKeywords: ['上野'], tags: ['美術館', '公園', '文化'] },

  // 大阪府
  { spotKeywords: ['大阪城'], tags: ['歴史', '桜の名所', '城郭'] },
  { spotKeywords: ['通天閣'], tags: ['展望台', 'レトロ', 'シンボル'] },
  { spotKeywords: ['海遊館'], tags: ['水族館', 'ファミリー', '海洋生物'] },
  { spotKeywords: ['道頓堀'], tags: ['グルメ', 'ネオン', '繁華街'] },
  { spotKeywords: ['新世界'], tags: ['レトロ', 'グルメ', '下町'] },
  { spotKeywords: ['梅田', '大阪駅'], tags: ['ショッピング', '交通', 'ビジネス'] },

  // 京都府
  { spotKeywords: ['清水寺'], tags: ['世界遺産', '歴史', '寺院'] },
  { spotKeywords: ['金閣寺'], tags: ['世界遺産', '庭園', '寺院'] },
  { spotKeywords: ['伏見稲荷'], tags: ['千本鳥居', 'パワースポット', '神社'] },

  // 北海道
  { spotKeywords: ['札幌時計台'], tags: ['歴史建造物', 'シンボル', '文化財'] },
  { spotKeywords: ['函館山'], tags: ['夜景', '展望台', '自然'] },
  { spotKeywords: ['小樽運河'], tags: ['レトロ', '運河', '歴史的景観'] },

  // カテゴリベースのタグ（フォールバック）
  { spotKeywords: ['寺院'], tags: ['歴史', '文化', '精神的'] },
  { spotKeywords: ['神社'], tags: ['伝統', 'パワースポット', '文化'] },
  { spotKeywords: ['城'], tags: ['歴史', '城郭', '文化財'] },
  { spotKeywords: ['温泉'], tags: ['リラクゼーション', '健康', '自然'] },
  { spotKeywords: ['博物館'], tags: ['文化', '学習', '展示'] },
  { spotKeywords: ['水族館'], tags: ['ファミリー', '海洋生物', 'エンターテイメント'] }
]

/**
 * 観光地のタグを取得する関数
 * @param spotName 観光地名
 * @param category カテゴリ（フォールバック用）
 * @returns タグの配列（最大3つ）
 */
export function getSpotTags(spotName: string, category?: string): string[] {
  const tags = new Set<string>()

  // 観光地名でのマッチング
  for (const mapping of TOURIST_SPOT_TAG_MAPPINGS) {
    const hasMatch = mapping.spotKeywords.some(keyword => 
      spotName.includes(keyword)
    )
    
    if (hasMatch) {
      mapping.tags.forEach(tag => tags.add(tag))
    }
  }

  // カテゴリでのフォールバックマッチング
  if (category && tags.size === 0) {
    for (const mapping of TOURIST_SPOT_TAG_MAPPINGS) {
      const hasMatch = mapping.spotKeywords.some(keyword => 
        category.includes(keyword)
      )
      
      if (hasMatch) {
        mapping.tags.forEach(tag => tags.add(tag))
      }
    }
  }

  // デフォルトタグ（何もマッチしない場合）
  if (tags.size === 0) {
    tags.add('観光地')
    if (category) {
      tags.add(category)
    }
  }

  return Array.from(tags).slice(0, 3)
}

/**
 * タグの色を取得する関数
 * @param tag タグ名
 * @returns Tailwind CSSクラス
 */
export function getTagColorClasses(tag: string): string {
  const colorMap: Record<string, string> = {
    '歴史': 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300',
    '自然': 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
    '文化': 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
    'グルメ': 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
    'ショッピング': 'bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300',
    '展望台': 'bg-sky-100 dark:bg-sky-900/30 text-sky-800 dark:text-sky-300',
    'ファミリー': 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300'
  }

  return colorMap[tag] || 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
}
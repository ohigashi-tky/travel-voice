# Claude Code プロジェクト設定

## 言語設定
- **必須**: 全ての回答は日本語で行ってください
- コメントやドキュメントも日本語を優先してください

## プロジェクト概要
- Laravel + Nuxt.js による観光ガイドアプリケーション
- Amazon Polly による音声ガイド機能を実装
- 主要技術スタック：
  - バックエンド: Laravel 11, PHP 8.2
  - フロントエンド: Nuxt.js 3, Vue 3, Tailwind CSS
  - 音声合成: Amazon Polly (Takumi/Tomoko)
  - **データベース: MySQL (Docker環境) - 絶対にSQLiteは使用禁止**

## 開発時の注意事項
- **MySQL Docker必須**: このプロジェクトはMySQLのDockerコンテナを使用する（SQLite使用禁止）
- **Docker Compose必須**: 全てのLaravelコマンドは`docker compose exec backend`を使用して実行する
- **データベース設定変更禁止**: .envファイルのデータベース接続情報を勝手に変更してはいけない
- 音声ファイルは public storage に保存
- Laravel の storage:link が必要
- 24時間キャッシュによる音声ガイドテキスト管理

## コード品質ガイドライン
- **ハードコーディング絶対禁止**: 観光地データの直接埋め込みは絶対に行わない
- **travel_spotsテーブル必須使用**: 全ての観光地データはtravel_spotsテーブルから取得する
- **API専用アクセス**: travel_spotsテーブルへのアクセスは必ずAPIを通す
- **データベース最優先**: データはデータベースで管理し、コードに埋め込まない

## Laravel Artisanコマンド実行方法
**重要**: 全てのLaravelコマンドは必ずDocker Composeを使用して実行してください

```bash
# 基本形式
docker compose exec backend php artisan [コマンド]

# 例
docker compose exec backend php artisan migrate
docker compose exec backend php artisan db:seed
docker compose exec backend php artisan cache:clear

# データベースリセット＋全シーダー実行
docker compose exec backend php artisan migrate:fresh --seed
```

## 観光地データ管理コマンド
- place_id一括更新: `docker compose exec backend php artisan travel-spots:update-place-ids`
- place_id強制更新: `docker compose exec backend php artisan travel-spots:update-place-ids --force`
- 写真一括取得: `docker compose exec backend php artisan travel-spots:fetch-images`
- 写真強制再取得: `docker compose exec backend php artisan travel-spots:fetch-images --force`

## 新しい観光地の追加手順

### ローカル環境での作業
1. **観光地データ追加**: `database/seeders/TravelSpotSeeder.php`に新しい観光地データを追加
2. **シーダー実行**: `docker compose exec backend php artisan db:seed --class=TravelSpotSeeder`
3. **画像取得**: `docker compose exec backend php artisan travel-spots:fetch-images`（新しい観光地のみ）
4. **画像データをシーダー化**: 取得した画像データを`database/seeders/TravelSpotImageSeeder.php`に追加

### Railway環境での動作
- 自動実行: マイグレーション → シーダー実行（観光地データ + 画像データ）
- **Google Places API呼び出しなし**（コスト削減）
- 画像データは事前にシーダーで準備済み

## 画像管理システム
- **travel_spot_imagesテーブル**: 各観光地の画像URL（最大5枚）をデータベースで管理
- **画像取得コマンド**: `php artisan travel-spots:fetch-images`でGoogle Places APIから画像を一括取得してDB保存
- **フロントエンド**: PlacePhotoImageコンポーネントでtravel_spot_imagesを優先使用、Google Places APIをフォールバック
- **APIコスト削減**: 画像はデータベースに保存されているため、毎回Google Places APIを呼び出す必要がない

## 画像表示実装ガイドライン

### 必須実装パターン
画像表示機能を実装する際は、以下のパターンを使用：

```vue
<template>
  <div class="image-container">
    <!-- 読み込み中プレースホルダー -->
    <div 
      v-if="!imageLoaded[item.id]" 
      class="loading-placeholder animate-pulse bg-gradient-to-r from-gray-200 to-gray-300"
    >
      <span class="text-gray-500">読み込み中...</span>
    </div>
    
    <!-- 画像本体 -->
    <img 
      v-show="imageLoaded[item.id]"
      :src="item.imageUrl"
      :alt="item.name"
      @load="imageLoaded[item.id] = true"
      @error="handleImageError(item)"
      loading="eager"
      decoding="async"
      class="w-full h-full object-cover transition-opacity duration-300"
    />
  </div>
</template>
```

### JavaScript実装
```javascript
import { ref } from 'vue'

// 画像読み込み状態管理
const imageLoaded = ref({})

// エラーハンドリング
const handleImageError = (item) => {
  console.warn(`Failed to load image for: ${item.name}`)
  imageLoaded.value[item.id] = false
}
```

### 必須チェックリスト
- [ ] `<img>`タグを使用している
- [ ] `@load`と`@error`イベントハンドラーが設定されている
- [ ] 読み込み中のプレースホルダーが実装されている
- [ ] `loading`と`decoding`属性が適切に設定されている

## 重要な禁止事項
- ❌ **CSS background-imageの使用禁止**: 読み込み状態を追跡できないため
- ❌ **エラーハンドリングなしの実装禁止**: 読み込み失敗時の対応が必要
- ❌ **ハードコーディングによるフォールバック禁止**: データベース最優先
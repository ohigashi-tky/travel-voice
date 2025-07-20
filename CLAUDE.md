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
- docker-compose.ymlでMySQLが定義済み（ポート3306で稼働）
- **Docker Compose必須**: 全てのLaravelコマンドは`docker compose exec backend`を使用して実行する
- **データベース設定変更禁止**: .envファイルのデータベース接続情報（DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD）を勝手に変更してはいけない
- 音声ファイルは public storage に保存
- Laravel の storage:link が必要
- 24時間キャッシュによる音声ガイドテキスト管理
- 全てのUIコンポーネントでIcon依存を避け、絵文字使用を推奨

## 画像管理システム
- **travel_spot_imagesテーブル**: 各観光地の画像URL（最大5枚）をデータベースで管理
- **画像取得コマンド**: `php artisan travel-spots:fetch-images`でGoogle Places APIから画像を一括取得してDB保存
- **フロントエンド**: PlacePhotoImageコンポーネントでtravel_spot_imagesを優先使用、Google Places APIをフォールバックとして使用
- **APIコスト削減**: 画像はデータベースに保存されているため、毎回Google Places APIを呼び出す必要がない

## コード品質ガイドライン
- **ハードコーディング絶対禁止**: 観光地データの直接埋め込みは絶対に行わない
- **travel_spotsテーブル必須使用**: 全ての観光地データはtravel_spotsテーブルから取得する
- **API専用アクセス**: travel_spotsテーブルへのアクセスは必ずAPIを通す
- **定数管理**: 固定値は constants/ ディレクトリで管理
- **フォールバック禁止**: ハードコーディングによるフォールバックは実装しない
- **データベース最優先**: データはデータベースで管理し、コードに埋め込まない

## テストコマンド
- フロントエンド: `npm run dev` (ポート: 3000)
- バックエンド: `docker compose exec backend php artisan serve --host=0.0.0.0 --port=8000`
- ストレージリンク: `docker compose exec backend php artisan storage:link`

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

### **ローカル環境での作業**
1. **観光地データ追加**: `database/seeders/TravelSpotSeeder.php`に新しい観光地データを追加
2. **シーダー実行**: `docker compose exec backend php artisan db:seed --class=TravelSpotSeeder`
3. **画像取得**: `docker compose exec backend php artisan travel-spots:fetch-images`（新しい観光地のみ）
4. **画像データをシーダー化**: 取得した画像データを`database/seeders/TravelSpotImageSeeder.php`に追加
5. **place_id更新**: `docker compose exec backend php artisan travel-spots:update-place-ids`（必要に応じて）

### **Railway環境での動作**
- 自動実行: マイグレーション → シーダー実行（観光地データ + 画像データ）
- **Google Places API呼び出しなし**（コスト削減）
- 画像データは事前にシーダーで準備済み

## 完全リフレッシュ手順（推奨）
```bash
# 1. データベースリセット＋全シーダー実行
docker compose exec backend php artisan migrate:fresh --seed

# 2. 新しい観光地の画像を自動取得
docker compose exec backend php artisan travel-spots:fetch-images
```

## 自動実行される処理
**Railway環境**:
- データベースマイグレーション（既存データ保持）
- 全シーダー実行
- サーバー起動

**ローカル環境**:
- データベースマイグレーション
- 全シーダー実行
- サーバー起動

## 画像取得について

### **ローカル環境とRailway環境の違い**

| 項目 | ローカル環境 | Railway環境 |
|------|-------------|-------------|
| 画像取得方法 | `travel-spots:fetch-images`コマンド | シーダーで事前準備済み |
| Google Places API呼び出し | あり（新規観光地のみ） | なし |
| APIコスト | 少量（新規分のみ） | ゼロ |
| 画像データソース | `travel_spot_images`テーブル | `travel_spot_images`テーブル |

### **開発フロー**
1. **ローカル**: 新しい観光地追加 → 画像取得コマンド実行
2. **シーダー更新**: 取得した画像データをシーダーに追加
3. **Railway**: シーダーで画像データも含めて自動復元

**注意**: 
- 全てのデータはtravel_spotsテーブルから取得する（ハードコーディング禁止）
- Railway環境ではAPIコスト発生なし（シーダーで画像データ管理）
- 新しい観光地追加時はローカルで画像取得してシーダー更新が必要
- place_idが空の場合のみ自動取得される（`--force`オプションで強制更新可能）

## Railway環境での重要な教訓（2025年7月13日）

### 問題の発生と原因
**症状**: CORSエラーと502 Bad Gatewayが継続的に発生
**真の原因**: 過剰で複雑な設定変更によりLaravelアプリケーション自体が起動しなくなった

### 間違ったアプローチと教訓

1. **複雑な設定ファイルの追加は逆効果**
   - ❌ 間違い: deploy.sh、nixpacks.toml、Procfile等の複数設定ファイルを追加
   - ✅ 正解: Railwayの自動検出（Nixpacks）に任せる
   - 教訓: **Railwayは自動でLaravelを認識して適切に起動する。余計な設定は混乱を招く**

2. **CORS設定の過剰な複雑化**
   - ❌ 間違い: カスタムCorsMiddleware、.htaccess設定、複数のOPTIONSルート追加
   - ✅ 正解: config/cors.phpの`allowed_origins: ['*']`のみで十分
   - 教訓: **CORSエラーの根本原因は502エラー（サーバー起動失敗）だった**

3. **問題の本質を見誤った**
   - ❌ 間違い: CORSエラーにばかり注目してCORS設定を複雑化
   - ✅ 正解: 502エラーがCORSエラーの原因。アプリ起動失敗を解決すべきだった
   - 教訓: **エラーの表面的な症状ではなく、根本原因を特定する**

4. **シンプルな解決策を軽視した**
   - ❌ 間違い: 複雑な設定で「確実に」動かそうとした
   - ✅ 正解: 最小限の設定（標準Laravel + 基本CORS設定）で十分だった
   - 教訓: **動作していた過去の状態に戻すのが最も確実**

### Railway環境での正しいアプローチ
1. **Laravelの標準設定を信頼する**
   - Laravel 11の`health: '/up'`を活用
   - Nixpacksの自動検出を活用
   - 標準的なフォルダ構成を維持

2. **CORS設定はシンプルに**
   ```php
   // config/cors.php
   return [
       'paths' => ['api/*'],
       'allowed_methods' => ['*'],
       'allowed_origins' => ['*'],
       'allowed_headers' => ['*'],
       'supports_credentials' => false,
   ];
   ```

3. **追加設定ファイルは基本的に不要**
   - deploy.sh ❌ 
   - Procfile ❌
   - nixpacks.toml ❌
   - カスタムCorsMiddleware ❌

### 今後の対応指針
- **問題発生時は、まず最小限の設定に戻す**
- **502エラー = アプリ起動失敗、CORSエラー = 502の結果**
- **Railwayの自動設定を信頼し、過剰な設定変更を避ける**
- **動作していた過去の状態を基準点とする**

## 画像表示実装ガイドライン

### **必須実装パターン**
新しい画像表示機能を実装する際は、以下のパターンを**必ず**使用してください：

#### 1. HTML構造（imgタグ使用）
```vue
<template>
  <!-- ❌ 使用禁止: CSS background-image -->
  <!-- <div :style="{ backgroundImage: `url(${imageUrl})` }"></div> -->
  
  <!-- ✅ 必須: imgタグ + 読み込み状態管理 -->
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

#### 2. JavaScript実装（Vue 3 Composition API）
```javascript
import { ref } from 'vue'

// 画像読み込み状態管理
const imageLoaded = ref({})

// エラーハンドリング
const handleImageError = (item) => {
  console.warn(`Failed to load image for: ${item.name}`)
  imageLoaded.value[item.id] = false
  // 必要に応じてフォールバック画像を設定
}
```

#### 3. CSS実装
```css
.image-container {
  position: relative;
  overflow: hidden;
}

.loading-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  min-height: 150px; /* 適切な最小高さを設定 */
}

img {
  transition: opacity 0.3s ease;
}
```

### **loading属性の使い分け**
- **`loading="eager"`**: ホームページ、重要なコンテンツ（即座に読み込み）
- **`loading="lazy"`**: モーダル、下部コンテンツ（スクロール時に読み込み）

### **禁止事項**
- ❌ **CSS background-imageの使用禁止**: 読み込み状態を追跡できないため
- ❌ **エラーハンドリングなしの実装禁止**: 読み込み失敗時の対応が必要
- ❌ **読み込み状態の表示なし禁止**: ユーザーに読み込み状況を示す必要

### **必須チェックリスト**
新しい画像表示実装時は以下を確認：
- [ ] `<img>`タグを使用している
- [ ] `@load`と`@error`イベントハンドラーが設定されている
- [ ] 読み込み中のプレースホルダーが実装されている
- [ ] `loading`と`decoding`属性が適切に設定されている
- [ ] エラー時の処理が実装されている
- [ ] 画像読み込み状態のリアクティブ管理が実装されている

### **実装例の参照**
- **成功例**: `PrefectureSelection.vue`, `CategorySelection.vue`
- **これらのコンポーネントを参考に同じパターンで実装すること**
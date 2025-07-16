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
1. **データ追加**: `database/seeders/TravelSpotSeeder.php`に新しい観光地データを追加
2. **シーダー実行**: `docker compose exec backend php artisan db:seed --class=TravelSpotSeeder`
3. **写真取得**: `docker compose exec backend php artisan travel-spots:fetch-images`（新しい観光地のみ自動取得）
4. **place_id更新**: `docker compose exec backend php artisan travel-spots:update-place-ids`（必要に応じて）

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
**重要**: 画像取得は自動実行されません（APIコスト削減のため）

**新しい観光地追加時の手動実行**:
```bash
# Railway環境（デプロイ後に一回だけ実行）
Railway CLI: railway run php artisan travel-spots:fetch-images

# ローカル環境
docker compose exec backend php artisan travel-spots:fetch-images
```

**注意**: 
- 全てのデータはtravel_spotsテーブルから取得する（ハードコーディング禁止）
- 新しい観光地の写真は手動で取得する（APIコスト管理）
- 既存の観光地画像は再取得されない（効率的な運用）
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
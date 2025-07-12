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
- バックエンド: `php artisan serve --port=8000`
- ストレージリンク: `php artisan storage:link`

## 観光地データ管理コマンド
- place_id一括更新: `php artisan travel-spots:update-place-ids`
- place_id強制更新: `php artisan travel-spots:update-place-ids --force`
- 写真一括取得: `php artisan travel-spots:fetch-images`
- 写真強制再取得: `php artisan travel-spots:fetch-images --force`

## 新しい観光地の追加手順
1. **データ追加**: `database/seeders/TravelSpotSeeder.php`に新しい観光地データを追加
2. **シーダー実行**: `php artisan db:seed --class=TravelSpotSeeder`
3. **写真取得**: `php artisan travel-spots:fetch-images`
4. **place_id更新**: `php artisan travel-spots:update-place-ids`（必要に応じて）

**注意**: 
- 全てのデータはtravel_spotsテーブルから取得する（ハードコーディング禁止）
- 新しい観光地の写真は自動的にGoogle Places APIから取得される
- place_idが空の場合のみ自動取得される（`--force`オプションで強制更新可能）
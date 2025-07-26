# 🏠 おうち旅行

家にいながら音声で楽しめる旅行体験アプリ

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com/)
[![Nuxt.js](https://img.shields.io/badge/Nuxt.js-3.x-green.svg)](https://nuxt.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://www.docker.com/)

## 環境構築

### 1. 環境変数の設定

プロジェクトルートに `.env` ファイルを作成：

```bash
# API設定
NUXT_OPENROUTER_API_KEY=your_openrouter_api_key_here
NUXT_OPENROUTER_MODEL=google/gemini-2.5-flash-lite-preview-06-17
NUXT_PUBLIC_GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here

# AWS Polly設定
AWS_ACCESS_KEY_ID=your_aws_access_key_id
AWS_SECRET_ACCESS_KEY=your_aws_secret_access_key
AWS_DEFAULT_REGION=ap-northeast-1
```

### 2. アプリケーションの起動

```bash
# 全サービス起動
docker compose up -d

# データベース初期化
docker compose exec backend php artisan migrate --seed
```

### 3. アクセス

- **アプリ**: http://localhost:3000
- **API**: http://localhost:8000
- **DB管理**: http://localhost:8080

## 開発コマンド

```bash
# 全サービス管理
docker compose up -d          # 起動
docker compose down          # 停止

# データベース操作
docker compose exec backend php artisan migrate --seed     # DB初期化
docker compose exec backend php artisan migrate:fresh --seed  # 完全リセット

# 観光地データ管理
docker compose exec backend php artisan travel-spots:fetch-images  # 画像取得
docker compose exec backend php artisan travel-spots:fetch-images --force  # 全画像強制再取得
docker compose exec backend php artisan travel-spots:update-place-ids  # place_id更新

# イベント情報管理
docker compose exec backend php artisan events:fetch              # 全都道府県のイベント情報取得
docker compose exec backend php artisan events:fetch --prefecture=東京都  # 特定都道府県のみ
docker compose exec backend php artisan events:fetch --force      # 既存データ削除して再取得
```

## 技術スタック

- **フロントエンド**: Nuxt.js 3, Vue 3, TailwindCSS, TypeScript
- **バックエンド**: Laravel 11, PHP 8.2, MySQL
- **AI・音声**: Amazon Polly, OpenRouter API (Gemini)
- **インフラ**: Docker, Docker Compose, Railway(本番環境)
- **地図、観光地データ**: Google Maps API, Google Places API

## 機能

- **音声ガイド**: Amazon Pollyによる生成
- **画像管理**: Google Maps APIで初回のみ一括取得
- **イベント情報**: AIによる一括取得バッチ。任意の実行。
- **検索機能**: 観光地名・都道府県での高速検索
- **レスポンシブ対応**: メインはスマホ。PCにも対応しています。

## AIによる開発支援
- **メイン開発** Claude Code
- **issueによる自動開発** Amazon Q Developer

---
**本番環境**: https://travel-voice-production-61af.up.railway.app/
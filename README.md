# 🏠 おうち旅行

穏やかな音声で日本を巡る、家にいながら楽しめる旅行体験アプリ

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com/)
[![Nuxt.js](https://img.shields.io/badge/Nuxt.js-3.x-green.svg)](https://nuxt.com/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)](https://www.typescriptlang.org/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-blue.svg)](https://tailwindcss.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://www.docker.com/)

## 🌟 概要

おうち旅行は、家に居ながら日本各地の観光スポットを音声で巡るバーチャル旅行体験アプリです。穏やかで自然な音声ガイドにより、リラックスしながら歴史や文化を学べる、新しい形の観光体験を提供します。

## 🚀 クイックスタート

### 1. 環境変数の設定

**プロジェクトのルートディレクトリ**（`docker-compose.yml` と同じ場所）に `.env` ファイルを作成し、以下の内容を記述してください。

```bash
# .env (プロジェクトルート)
NUXT_OPENROUTER_API_KEY=your_openrouter_api_key_here
NUXT_OPENROUTER_MODEL=google/gemini-2.5-flash-lite-preview-06-17
NUXT_API_BASE_URL=http://localhost:8000/api
NUXT_PUBLIC_GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here
NUXT_PUBLIC_GOOGLE_MAPS_MAP_ID=your_google_maps_map_id_here

# AWS Polly設定
AWS_ACCESS_KEY_ID=your_aws_access_key_id
AWS_SECRET_ACCESS_KEY=your_aws_secret_access_key
AWS_DEFAULT_REGION=ap-northeast-1
```

**注意:** `frontend`ディレクトリではなく、プロジェクトのルートにファイルを作成してください。

### 2. アプリケーションの起動

```bash
# プロジェクトルートで実行
docker compose up -d

# データベースセットアップ
docker compose exec backend php artisan migrate --seed
```

### 3. アクセス

- **フロントエンド**: http://localhost:3000 
- **バックエンド**: http://localhost:8000  
- **phpMyAdmin**: http://localhost:8080

### 🔑 テストアカウント

- **Email**: demo@example.com
- **Password**: TravelGuide2024!

## 🏗️ 技術スタック

### Frontend
- **Nuxt.js 3** - Vue.js 3ベースのフルスタックフレームワーク
- **TypeScript** - 型安全性による開発効率向上
- **TailwindCSS** - ユーティリティファーストCSSフレームワーク
- **Pinia** - Vue.js用状態管理ライブラリ
- **VueUse** - Vue Composition Utilities（ダークモード等）
- **Lucide Vue Next** - 美しいアイコンライブラリ

### Backend
- **Laravel 11** - 高性能PHPフレームワーク
- **PHP 8.2+** - 最新PHP機能活用
- **Laravel Sanctum** - API認証システム
- **SQLite** - 軽量データベース

### AI・音声技術
- **Amazon Polly Neural Engine**
- **Google Gemini 2.5 Flash**
- **OpenRouter API**

### Infrastructure
- **Docker**
- **Docker Compose**

## 📁 プロジェクト構造

```
travel-voice/
├── 🔧 docker-compose.yml     # Docker設定
├── 🌐 frontend/              # Nuxt.js アプリケーション
│   ├── 📄 pages/            # ページコンポーネント
│   │   ├── index.vue        # メインページ（人気スポット・都道府県選択）
│   │   ├── login.vue        # ログインページ
│   │   ├── spots/[id].vue   # 観光地詳細・音声ガイドページ
│   │   └── prefecture/      # 都道府県別ページ
│   ├── 🧩 components/       # 共通コンポーネント
│   │   ├── AppHeader.vue    # ヘッダー（プロフィール・設定）
│   │   ├── AppFooter.vue    # フッター（タブナビゲーション）
│   │   └── AudioGuide*.vue  # 音声ガイド関連コンポーネント
│   ├── 🔧 composables/      # Vue Composition関数
│   │   ├── useAuth.ts       # 認証管理
│   │   ├── useAudioGuide.ts # 音声ガイド機能
│   │   └── useTouristSpots.ts # 観光地データ管理
│   ├── 🗄️ stores/          # Pinia ストア
│   │   └── auth.ts          # 認証状態管理
│   └── 📝 types/            # TypeScript型定義
├── ⚙️ backend/               # Laravel API
│   ├── 🎯 app/Http/Controllers/Api/ # APIコントローラー
│   │   ├── AudioGuideController.php # 音声ガイド生成
│   │   └── PopularSpotsController.php # AI推薦機能
│   ├── 🎵 app/Services/     # サービスクラス
│   │   └── PollyService.php # Amazon Polly連携
│   ├── 🧪 tests/           # テストスイート
│   │   ├── Feature/AudioGuideQualityTest.php # 品質テスト
│   │   └── Unit/AudioGuideDataTest.php # データテスト
│   ├── 📊 database/         # マイグレーション・シーダー
│   └── 🛣️ routes/           # API ルート定義
└── 📚 docs/                 # プロジェクトドキュメント
    ├── app-concept-v2.md    # アプリコンセプト提案
    └── AUDIO_GUIDE_TESTING.md # 音声ガイド品質テストガイド
```

## 🔧 開発コマンド

```bash
# 🐳 Docker操作
docker compose up -d          # 全サービス起動
docker compose down          # 全サービス停止
docker compose logs -f       # ログ確認

# ⚙️ Laravel操作
docker compose exec backend php artisan migrate --seed     # DB初期化
docker compose exec backend php artisan audio-guide:quality-check  # 音声品質チェック
docker compose exec backend ./vendor/bin/phpunit tests/    # テスト実行

# 🌐 Nuxt操作
docker compose exec frontend npm run dev           # 開発サーバー
docker compose exec frontend npm run build         # 本番ビルド
docker compose exec frontend npm run type-check    # 型チェック
```

## 📊 API エンドポイント

| メソッド | エンドポイント | 説明 | 認証 |
|---|---|---|---|
| `POST` | `/api/login` | ログイン | ❌ |
| `POST` | `/api/register` | 新規登録 | ❌ |
| `POST` | `/api/logout` | ログアウト | ✅ |
| `GET` | `/api/tourist-spots` | 観光スポット一覧 | ❌ |
| `GET` | `/api/tourist-spots/{id}` | スポット詳細 | ❌ |
| `GET` | `/api/popular-spots` | 人気スポット | ❌ |
| `POST` | `/api/audio-guide/tourist-spot` | 音声ガイド生成 | ❌ |
| `DELETE` | `/api/popular-spots/cache` | キャッシュクリア | ❌ |

## 🎯 音声ガイド品質管理

### 品質基準
- **最小文字数**: 200文字以上の詳細な解説
- **エピソード数**: 2個以上の具体的な歴史エピソード
- **具体性**: 年代、人名、数値などの具体的情報
- **汎用テンプレート検出**: AI分析による品質保証

### テスト実行
```bash
# 全観光地の品質チェック
./vendor/bin/phpunit tests/Feature/AudioGuideQualityTest.php

# Artisanコマンドでの品質チェック
php artisan audio-guide:quality-check --export-report
```

## 🎯 認証フロー

### ルート保護
- **認証必須**: `/` (メインページ), `/spots/*` (観光地詳細)
- **ゲスト限定**: `/login` (ログインページ)

### 認証状態管理
- **Pinia Store**: 認証状態をグローバル管理
- **localStorage**: ユーザー情報の永続化
- **Middleware**: Nuxt 3のミドルウェアによる自動リダイレクト

# 🏠 おうち旅行

穏やかな音声で日本を巡る、家にいながら楽しめる旅行体験アプリ

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com/)
[![Nuxt.js](https://img.shields.io/badge/Nuxt.js-3.x-green.svg)](https://nuxt.com/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)](https://www.typescriptlang.org/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-blue.svg)](https://tailwindcss.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://www.docker.com/)

## 🌟 概要

- **画面**: https://travel-voice-production-61af.up.railway.app/

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
- **MySQL** - データベース（Docker環境）

### AI・音声技術
- **Amazon Polly Neural Engine** - 自然な日本語音声合成
- **OpenRouter API** - AI応答生成（Google Gemini 2.5 Flash使用）
  - **重要**: 本プロジェクトではOpenRouter APIのみ使用。

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
│   │   ├── events.vue       # イベント情報一覧ページ
│   │   ├── spots/[id].vue   # 観光地詳細・音声ガイドページ
│   │   └── prefecture/      # 都道府県別ページ
│   ├── 🧩 components/       # 共通コンポーネント
│   │   ├── AppHeader.vue    # ヘッダー（プロフィール・設定）
│   │   ├── AppFooter.vue    # フッター（タブナビゲーション）
│   │   └── AudioGuide*.vue  # 音声ガイド関連コンポーネント
│   ├── 🔧 composables/      # Vue Composition関数
│   │   ├── useAuth.ts       # 認証管理
│   │   ├── useAudioGuide.ts # 音声ガイド機能
│   │   └── useTouristSpots.ts # 観光地データ管理（travel_spotsテーブル使用）
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

# 🗺️ 観光地データ管理
docker compose exec backend php artisan travel-spots:update-place-ids    # Google Places APIでplace_idを一括更新
docker compose exec backend php artisan travel-spots:update-place-ids --force  # 全てのplace_idを強制更新
docker compose exec backend php artisan travel-spots:fetch-images       # 観光地の写真を一括取得してtravel_spot_imagesテーブルに保存（新規のみ）
docker compose exec backend php artisan travel-spots:fetch-images --force     # 既存の写真も再取得（全て強制更新）

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
| `GET` | `/api/travel-spots` | 観光スポット一覧 | ❌ |
| `GET` | `/api/travel-spots/{id}` | スポット詳細 | ❌ |
| `GET` | `/api/popular-spots` | 人気スポット | ❌ |
| `POST` | `/api/audio-guide/tourist-spot` | 音声ガイド生成 | ❌ |
| `DELETE` | `/api/popular-spots/cache` | キャッシュクリア | ❌ |
| `GET` | `/api/prefectures` | 全都道府県一覧 | ❌ |
| `GET` | `/api/prefectures/available` | 利用可能都道府県 | ❌ |
| `GET` | `/api/prefectures/by-region` | 地域別都道府県 | ❌ |
| `GET` | `/api/prefectures/{id}` | 都道府県詳細 | ❌ |
| `GET` | `/api/prefectures/{id}/spots` | 都道府県別観光地 | ❌ |
| `GET` | `/api/prefectures/name/{name}/spots` | 都道府県名で観光地検索 | ❌ |
| `GET` | `/api/events` | イベント情報一覧 | ❌ |
| `GET` | `/api/events/{id}` | イベント詳細 | ❌ |
| `GET` | `/api/events/count-by-prefecture` | 都道府県別イベント数 | ❌ |
| `GET` | `/api/events/popular-tags` | 人気タグ一覧 | ❌ |
| `GET` | `/api/events/featured` | おすすめイベント | ❌ |
| `GET` | `/api/events/current` | 現在開催中イベント | ❌ |

## 📸 画像管理システム

### 仕組み
- **travel_spot_imagesテーブル**: 各観光地の画像URL（最大5枚）をデータベースで管理
- **フロントエンド表示**: データベース保存済み画像を優先使用、Google Places APIをフォールバック
- **コスト削減**: 画像をDBに保存することで、100%のAPI呼び出しを削減

### 現在の状況（最終更新: 2025-07-12）
- **総観光地数**: 90件
- **画像保存済み**: 90件（100%）
- **画像未保存**: 0件
- **総画像数**: 446枚
- **Google Places API呼び出し削減率**: 100%

### 画像取得の流れ
1. **新規観光地追加**: `TravelSpotSeeder`にデータ追加 → `db:seed`実行
2. **画像取得**: `travel-spots:fetch-images`でGoogle Places APIから画像を取得・保存
3. **表示**: フロントエンドで自動的にDB保存済み画像を表示

### 画像管理コマンド
```bash
# 新しい観光地の画像のみ取得（推奨）
docker compose exec backend php artisan travel-spots:fetch-images

# 全ての画像を強制再取得（既存も含む）
docker compose exec backend php artisan travel-spots:fetch-images --force

# 🎪 イベント情報管理（OpenRouter API使用）
docker compose exec backend php artisan events:fetch              # 全都道府県のイベント情報取得
docker compose exec backend php artisan events:fetch --prefecture=東京都  # 特定都道府県のみ
docker compose exec backend php artisan events:fetch --force      # 既存データ削除して再取得

## 🎪 イベント情報管理

### データ管理方式
- **ローカル環境**: OpenRouter APIで自動生成された1393件のイベントデータをMySQLで管理
- **Railway本番環境**: Seederで登録した164件のイベントデータで運用
- **place_id管理**: Railway環境では観光地のplace_idもSeederで事前登録済み
```

## 🗾 都道府県管理システム

### 設計思想
- **データベース最優先**: CLAUDE.mdガイドラインに従い、ハードコーディングを完全排除
- **正規化設計**: prefecturesテーブルとprefecture_imagesテーブルによる適切な関係管理
- **API駆動**: フロントエンドは100%APIからデータを取得

### データベース構造
```sql
-- 都道府県マスターテーブル
prefectures: id, name, name_kana, region, display_order, is_available

-- 都道府県画像テーブル  
prefecture_images: id, prefecture_id, image_url, image_type, display_order

-- 観光地テーブル（修正）
travel_spots: id, prefecture_id, name, description, ...
```

### 都道府県管理機能
- **47都道府県完全管理**: 一般的な順番（北海道→本州→四国→九州→沖縄）で登録
- **地域別分類**: 北海道・東北、関東、中部、近畿、中国、四国、九州・沖縄
- **利用可能フラグ**: 観光地データの有無を`is_available`で管理
- **将来対応**: 都道府県別アイコン画像の実装準備完了

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

# 🎧 Travel Voice

モダンなデザインと音声ガイドで日本の観光を楽しむアプリケーション

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com/)
[![Nuxt.js](https://img.shields.io/badge/Nuxt.js-3.x-green.svg)](https://nuxt.com/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)](https://www.typescriptlang.org/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-blue.svg)](https://tailwindcss.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://www.docker.com/)

## 🌟 概要

Travel Voice は、音声ガイド付きで日本各地の観光スポットを紹介するモダンな観光アプリです。白背景をベースとしたクリーンなデザインとダークモード対応で、快適な観光体験を提供します。

### ✨ 主な機能

- 🎧 **音声ガイド** - 各観光スポットの詳細な音声解説
- 🌓 **ダークモード** - ライト/ダークテーマの切り替え
- 🔐 **認証システム** - ログイン/登録/ログアウト機能
- 📱 **レスポンシブ対応** - PC・タブレット・スマートフォン対応
- ⚡ **高速パフォーマンス** - Nuxt 3による最適化
- 🎨 **モダンUI** - グラデーションとアニメーション

## 🚀 クイックスタート

### 1. アプリケーションの起動

```bash
# プロジェクトルートで実行
docker compose up -d

# データベースセットアップ
docker compose exec backend php artisan migrate --seed
```

### 2. アクセス

- **フロントエンド**: http://localhost:3000 
- **バックエンド**: http://localhost:8000  
- **phpMyAdmin**: http://localhost:8080

### 🔑 テストアカウント

- **Email**: demo@example.com
- **Password**: password

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

### Infrastructure
- **Docker** - コンテナ化によるポータブルな開発環境
- **Docker Compose** - 複数サービス統合管理

## 📁 プロジェクト構造

```
travel-voice/
├── 🔧 docker-compose.yml     # Docker設定
├── 🌐 frontend/              # Nuxt.js アプリケーション
│   ├── 📄 pages/            # ページコンポーネント
│   │   ├── index.vue        # メインページ（都道府県選択）
│   │   ├── login.vue        # ログインページ
│   │   ├── tokyo.vue        # 東京ガイドページ
│   │   └── prefecture/      # 都道府県別ページ
│   ├── 🧩 components/       # 共通コンポーネント
│   │   ├── AppHeader.vue    # ヘッダー（プロフィール・設定）
│   │   └── AppFooter.vue    # フッター（タブナビゲーション）
│   ├── 🔧 composables/      # Vue Composition関数
│   ├── 🛡️ middleware/       # ルートミドルウェア
│   │   ├── auth.ts          # 認証必須ページ用
│   │   └── guest.ts         # ゲスト用（ログイン済みは除外）
│   ├── 🗄️ stores/          # Pinia ストア
│   │   └── auth.ts          # 認証状態管理
│   └── 📝 types/            # TypeScript型定義
├── ⚙️ backend/               # Laravel API
│   ├── 🎯 app/Http/         # コントローラー・ミドルウェア
│   ├── 📊 database/         # マイグレーション・シーダー
│   └── 🛣️ routes/           # API ルート定義
└── 📚 docs/                 # プロジェクトドキュメント
```

## 🎯 現在対応の観光スポット

### 🗼 東京都 (3箇所)

| スポット | カテゴリ | 特徴 |
|---|---|---|
| **東京スカイツリー** | 展望台 | 高さ634mの世界最高クラスの電波塔 |
| **浅草寺** | 寺院 | 東京最古の寺院、雷門と仲見世通りで有名 |
| **明治神宮** | 神社 | 都心にありながら豊かな森に囲まれた神聖な空間 |

## 🎨 デザインシステム

### カラーパレット
- **Primary Gradient**: Cyan → Blue → Purple (`from-cyan-600 via-blue-600 to-purple-600`)
- **Background**: White / Dark Gray (`bg-white dark:bg-gray-900`)
- **Surface**: Light Gray / Dark Gray (`bg-gray-50 dark:bg-gray-800`)
- **Text**: Gray 800 / White (`text-gray-800 dark:text-white`)

### UI特徴
- **ダークモード**: VueUseのuseDarkによる完全対応
- **固定フッター**: スクロール時も常に表示
- **モーダル設計**: 設定・プロフィール用
- **レスポンシブ**: Tailwind CSSによる完全対応

## 🔧 開発コマンド

```bash
# 🐳 Docker操作
docker compose up -d          # 全サービス起動
docker compose down          # 全サービス停止
docker compose logs -f       # ログ確認

# ⚙️ Laravel操作
docker compose exec backend php artisan migrate     # マイグレーション
docker compose exec backend php artisan db:seed     # シーダー実行
docker compose exec backend php artisan make:model Example  # モデル作成

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
| `GET` | `/api/tourist-spots/prefecture/{prefecture}` | 都道府県別 | ❌ |

## 🎯 認証フロー

### ルート保護
- **認証必須**: `/` (メインページ), `/tokyo` (東京ガイド)
- **ゲスト限定**: `/login` (ログインページ)

### 認証状態管理
- **Pinia Store**: 認証状態をグローバル管理
- **localStorage**: ユーザー情報の永続化
- **Middleware**: Nuxt 3のミドルウェアによる自動リダイレクト

## 🔮 ロードマップ

### ✅ Phase 1 (完了)
- ✅ モダンUI/UXデザイン（白背景+ダークモード）
- ✅ 認証システム（ログイン/登録/ログアウト）
- ✅ 東京都観光スポット
- ✅ 音声ガイド機能
- ✅ 共通コンポーネント化
- ✅ レスポンシブデザイン

### 🚀 Phase 2 (計画中)
- 🔄 他都道府県対応（大阪、京都、北海道等）
- 🔄 音声プレーヤー機能強化
- 🔄 ユーザーレビュー機能
- 🔄 お気に入り機能

### 🌟 Phase 3 (構想中)
- 💡 AI音声ガイド生成
- 💡 多言語対応（英語、中国語等）
- 💡 オフライン機能
- 💡 プッシュ通知

## 🤝 コントリビューション

プロジェクトへの貢献を歓迎します！

1. **Fork** このリポジトリ
2. **Feature Branch** を作成 (`git checkout -b feature/amazing-feature`)
3. **Commit** 変更内容 (`git commit -m 'Add amazing feature'`)
4. **Push** ブランチ (`git push origin feature/amazing-feature`)
5. **Pull Request** を作成

## 📄 ライセンス

このプロジェクトは **MIT License** の下で公開されています。

## 💬 サポート

- 🐛 **バグ報告**: [GitHub Issues](https://github.com/ohigashi-tky/travel-voice/issues)
- 💡 **機能提案**: [GitHub Discussions](https://github.com/ohigashi-tky/travel-voice/discussions)

---

<div align="center">

**🎧 音声ガイドで観光を楽しもう 🗾**

Made with ❤️ by Claude Code

</div>
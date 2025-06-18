# 🗾 日本観光ガイドアプリ

高品質なGoogle Maps APIを使用した、インタラクティブな日本観光ガイドアプリケーション

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com/)
[![Nuxt.js](https://img.shields.io/badge/Nuxt.js-3.x-green.svg)](https://nuxt.com/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)](https://www.typescriptlang.org/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-blue.svg)](https://tailwindcss.com/)
[![Google Maps](https://img.shields.io/badge/Google_Maps-API-green.svg)](https://developers.google.com/maps)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://www.docker.com/)

## 🌟 概要

このアプリケーションは、Google Maps APIを活用した高品質な地図表示で、日本各地の観光スポットを音声ガイド付きで紹介する次世代の観光アプリです。

### ✨ 主な機能

- 🗾 **Google Maps統合** - 高品質で正確な日本地図
- 🎯 **47都道府県対応** - 全都道府県のクリック可能なエリア  
- 🎧 **音声ガイド** - 各観光スポットの詳細な音声解説
- 📱 **レスポンシブ対応** - PC・タブレット・スマートフォン対応
- 💫 **モダンUI** - Glassmorphismデザインとスムーズなアニメーション
- 🔐 **セキュア認証** - Laravel Sanctum採用

## 🚀 クイックスタート

### 1. Google Maps APIキーの設定

**重要**: アプリケーションを起動する前に、Google Maps APIキーを設定してください。

#### APIキーの取得
1. [Google Cloud Console](https://console.cloud.google.com/) にアクセス
2. プロジェクトを作成または選択
3. 「APIs & Services」→「Library」→「Maps JavaScript API」を有効化
4. 「Credentials」→「Create Credentials」→「API Key」

#### .envファイルの設定
```bash
# /frontend/.env ファイルに以下を設定
NUXT_PUBLIC_GOOGLE_MAPS_API_KEY=AIzaSyxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
```

### 2. アプリケーションの起動

```bash
# プロジェクトルートで実行
docker compose up -d

# データベースセットアップ
docker compose exec backend php artisan migrate --seed
```

### 3. アクセス

- **フロントエンド**: http://localhost:3000 （美しいGoogle Maps日本地図）
- **バックエンド**: http://localhost:8000  
- **phpMyAdmin**: http://localhost:8080

### 🔑 テストアカウント

- **Email**: demo@example.com
- **Password**: password123

## 🏗️ 技術スタック

### Frontend
- **Nuxt.js 3** - Vue.js 3ベースのフルスタックフレームワーク
- **TypeScript** - 型安全性による開発効率向上
- **TailwindCSS** - ユーティリティファーストCSSフレームワーク
- **Pinia** - Vue.js用状態管理ライブラリ
- **Lucide Vue** - 美しいアイコンライブラリ

### Backend
- **Laravel 12** - 高性能PHPフレームワーク
- **PHP 8.2+** - 最新PHP機能活用
- **Laravel Sanctum** - API認証システム
- **MySQL 8.0** - 高性能リレーショナルデータベース

### Infrastructure
- **Docker** - コンテナ化によるポータブルな開発環境
- **Docker Compose** - 複数サービス統合管理

## 📁 プロジェクト構造

```
guide-app/
├── 🔧 docker-compose.yml     # Docker設定
├── 🌐 frontend/              # Nuxt.js アプリケーション
│   ├── 📄 pages/            # ページコンポーネント
│   ├── 🧩 components/       # 再利用可能コンポーネント
│   ├── 🎨 assets/           # スタイル・画像等
│   ├── 🔧 composables/      # Vue Composition関数
│   └── 📝 types/            # TypeScript型定義
├── ⚙️ backend/               # Laravel API
│   ├── 🎯 app/Http/         # コントローラー・ミドルウェア
│   ├── 📊 database/         # マイグレーション・シーダー
│   └── 🛣️ routes/           # API ルート定義
└── 📚 docs/                 # プロジェクトドキュメント
    ├── 📖 README.md         # プロジェクト概要
    ├── 🔧 setup.md          # セットアップガイド
    ├── 🌐 api.md            # API仕様書
    └── 🏗️ architecture.md   # アーキテクチャ設計
```

## 🎯 対応観光スポット

### 🗼 東京都 (5箇所)

| スポット | カテゴリ | 音声ガイド数 | 特徴 |
|---|---|---|---|
| **東京スカイツリー** | 観光施設 | 2本 | 世界一高い電波塔の魅力 |
| **浅草寺** | 寺院 | 2本 | 東京最古の寺院の歴史 |
| **明治神宮** | 神社 | 1本 | 都心の神聖な森 |
| **皇居東御苑** | 庭園 | 1本 | 江戸城跡の美しい庭園 |
| **上野動物園** | 動物園 | 1本 | 日本最古の動物園 |

## 📖 ドキュメント

- 📋 [**セットアップガイド**](./docs/setup.md) - 詳細なインストール手順
- 🌐 [**API仕様書**](./docs/api.md) - RESTful API完全リファレンス  
- 🏗️ [**アーキテクチャ設計**](./docs/architecture.md) - システム設計思想

## 🎨 デザインシステム

### カラーパレット
- 🔵 **Primary**: Blue (#3B82F6) - メインアクション・リンク
- 🟣 **Secondary**: Purple (#8B5CF6) - アクセント・特別な要素  
- 🟢 **Success**: Green (#10B981) - 成功状態・確認
- 🟡 **Warning**: Yellow (#F59E0B) - 注意・警告
- 🔴 **Error**: Red (#EF4444) - エラー・削除アクション

### コンポーネント設計
- **Atomic Design** 採用
- **再利用性** を重視した設計
- **アクセシビリティ** 対応

## 🔧 開発コマンド

```bash
# 🐳 Docker操作
docker-compose up -d          # 全サービス起動
docker-compose down          # 全サービス停止
docker-compose logs -f       # ログ確認

# ⚙️ Laravel操作
docker-compose exec backend php artisan migrate     # マイグレーション
docker-compose exec backend php artisan db:seed     # シーダー実行
docker-compose exec backend php artisan make:model Example  # モデル作成

# 🌐 Nuxt操作
docker-compose exec frontend npm run dev           # 開発サーバー
docker-compose exec frontend npm run build         # 本番ビルド
docker-compose exec frontend npm run type-check    # 型チェック
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

## 🔮 ロードマップ

### 🎯 Phase 1 (完了)
- ✅ 基本認証システム
- ✅ 東京都観光スポット
- ✅ 音声ガイド機能
- ✅ レスポンシブデザイン

### 🚀 Phase 2 (計画中)
- 🔄 他都道府県対応
- 🔄 ユーザーレビュー機能
- 🔄 お気に入り機能
- 🔄 ソーシャル機能

### 🌟 Phase 3 (構想中)
- 💡 AR/VR対応
- 💡 AI音声ガイド
- 💡 多言語対応
- 💡 オフライン機能

## 🤝 コントリビューション

プロジェクトへの貢献を歓迎します！

1. **Fork** このリポジトリ
2. **Feature Branch** を作成 (`git checkout -b feature/amazing-feature`)
3. **Commit** 変更内容 (`git commit -m 'Add amazing feature'`)
4. **Push** ブランチ (`git push origin feature/amazing-feature`)
5. **Pull Request** を作成

## 📄 ライセンス

このプロジェクトは **MIT License** の下で公開されています。
詳細は [LICENSE](./LICENSE) ファイルをご確認ください。

## 💬 サポート

- 🐛 **バグ報告**: [GitHub Issues](https://github.com/username/guide-app/issues)
- 💡 **機能提案**: [GitHub Discussions](https://github.com/username/guide-app/discussions)
- 📧 **お問い合わせ**: support@guide-app.com

---

<div align="center">

**🗾 日本の美しい観光地を、音声ガイドと共に発見しよう 🎧**

Made with ❤️ by Claude Code

</div>
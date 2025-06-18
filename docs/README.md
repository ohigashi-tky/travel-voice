# 観光地音声ガイドアプリ

日本の観光地を巡る音声ガイド付き観光アプリケーションです。モダンなWebテクノロジーを使用して、ユーザーフレンドリーな観光体験を提供します。

## 📋 概要

このアプリケーションは、日本各地の観光スポットを紹介し、音声ガイド機能を通じて詳細な解説を提供します。現在は東京都の主要観光地に対応しており、今後他の都道府県にも拡張予定です。

### 主な機能

- 🗾 **インタラクティブ日本地図** - 都道府県ごとに観光スポットを探索
- 🎧 **音声ガイド** - プロのナレーターによる高品質な音声解説
- 📱 **レスポンシブデザイン** - スマートフォン、タブレット、PCに対応
- 🔐 **ユーザー認証** - ログイン・新規登録機能
- 🔍 **スポット検索・フィルタリング** - カテゴリや地域で絞り込み
- ❤️ **お気に入り機能** - 気になるスポットを保存（予定）
- 📤 **シェア機能** - 観光スポット情報の共有

## 🏗️ 技術スタック

### フロントエンド
- **Framework**: Nuxt.js 3.x (Vue.js 3)
- **Language**: TypeScript
- **Styling**: TailwindCSS
- **Icons**: Lucide Vue Next
- **HTTP Client**: $fetch (Nuxt標準)
- **State Management**: Pinia

### バックエンド
- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum
- **API**: RESTful API

### インフラ・開発環境
- **Containerization**: Docker & Docker Compose
- **Development Server**: Vite (Nuxt), Laravel Artisan
- **Database Management**: phpMyAdmin

## 📁 プロジェクト構造

```
guide-app/
├── backend/                 # Laravel バックエンド
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── TouristSpotController.php
│   │   │   └── GuideController.php
│   │   └── Models/
│   │       ├── User.php
│   │       ├── TouristSpot.php
│   │       ├── Guide.php
│   │       └── AudioGuide.php
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   ├── api.php
│   │   └── web.php
│   └── Dockerfile
├── frontend/                # Nuxt.js フロントエンド
│   ├── components/
│   │   ├── layout/
│   │   │   └── AppHeader.vue
│   │   ├── ui/
│   │   │   ├── BaseButton.vue
│   │   │   └── BaseInput.vue
│   │   ├── AudioPlayer.vue
│   │   └── JapanMap.vue
│   ├── pages/
│   │   ├── index.vue
│   │   ├── login.vue
│   │   ├── register.vue
│   │   └── spots/
│   │       ├── index.vue
│   │       └── [id].vue
│   ├── composables/
│   │   └── useAuth.ts
│   ├── types/
│   │   └── index.ts
│   ├── plugins/
│   │   └── api.client.ts
│   ├── assets/css/
│   │   └── main.css
│   └── Dockerfile
├── docs/                   # ドキュメント
│   ├── README.md
│   ├── setup.md
│   ├── api.md
│   └── architecture.md
├── docker-compose.yml      # Docker設定
└── README.md
```

## 🚀 セットアップ手順

詳細なセットアップ手順は [setup.md](./setup.md) をご確認ください。

### クイックスタート

1. **リポジトリのクローン**
```bash
git clone <repository-url>
cd guide-app
```

2. **Dockerコンテナの起動**
```bash
docker-compose up -d
```

3. **データベースのセットアップ**
```bash
docker-compose exec backend php artisan migrate --seed
```

4. **アプリケーションにアクセス**
- フロントエンド: http://localhost:3000
- バックエンドAPI: http://localhost:8000
- phpMyAdmin: http://localhost:8080

## 🎯 対応観光スポット

### 東京都 (5スポット)

1. **東京スカイツリー**
   - 音声ガイド: 2本 (完全ガイド・建設秘話)
   - カテゴリ: 観光施設

2. **浅草寺**
   - 音声ガイド: 2本 (歴史と文化・仲見世通り)
   - カテゴリ: 寺院

3. **明治神宮**
   - 音声ガイド: 1本 (由来と意義)
   - カテゴリ: 神社

4. **皇居東御苑**
   - 音声ガイド: 1本 (江戸城跡)
   - カテゴリ: 庭園

5. **上野動物園**
   - 音声ガイド: 1本 (歴史と見どころ)
   - カテゴリ: 動物園

## 🔧 開発・運用

### 開発用コマンド

```bash
# フロントエンド開発サーバー起動
docker-compose exec frontend npm run dev

# バックエンド開発サーバー起動
docker-compose exec backend php artisan serve

# データベースマイグレーション
docker-compose exec backend php artisan migrate

# シーダー実行
docker-compose exec backend php artisan db:seed
```

### テストアカウント

- **Email**: demo@example.com
- **Password**: password123

## 🎨 デザインシステム

### カラーパレット
- **Primary**: Blue (#3B82F6系)
- **Secondary**: Purple (#8B5CF6系)
- **Success**: Green (#10B981系)
- **Warning**: Yellow (#F59E0B系)
- **Error**: Red (#EF4444系)

### コンポーネント
- **BaseButton**: 再利用可能なボタンコンポーネント
- **BaseInput**: フォーム入力コンポーネント
- **AudioPlayer**: 音声再生コンポーネント
- **JapanMap**: インタラクティブ地図コンポーネント

## 📊 API仕様

詳細なAPI仕様は [api.md](./api.md) をご確認ください。

### 主要エンドポイント

```
GET /api/tourist-spots          # 観光スポット一覧
GET /api/tourist-spots/{id}     # 観光スポット詳細
GET /api/tourist-spots/prefecture/{prefecture}  # 都道府県別スポット
POST /api/login                 # ログイン
POST /api/register              # 新規登録
POST /api/logout                # ログアウト
```

## 🔮 今後の拡張予定

- [ ] 他都道府県の観光スポット追加
- [ ] ユーザーレビュー・評価機能
- [ ] AR（拡張現実）機能
- [ ] オフライン音声ダウンロード
- [ ] 多言語対応（英語・中国語・韓国語）
- [ ] 観光ルート提案機能
- [ ] SNS連携
- [ ] PWA対応

## 📄 ライセンス

このプロジェクトは [MIT License](../LICENSE) の下で公開されています。

## 🤝 コントリビューション

プロジェクトへの貢献を歓迎します。詳細は [CONTRIBUTING.md](../CONTRIBUTING.md) をご確認ください。

## 📞 サポート

質問や問題がある場合は、以下の方法でお問い合わせください：

- Issues: GitHub Issues
- Email: support@guide-app.com
- Documentation: [docs/](./)
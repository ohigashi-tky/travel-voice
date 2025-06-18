# セットアップガイド

このドキュメントでは、観光地音声ガイドアプリの開発環境をセットアップする手順を説明します。

## 📋 前提条件

### 必要なソフトウェア

- **Docker**: 20.10.0 以上
- **Docker Compose**: 2.0.0 以上
- **Git**: 最新版

### 推奨環境

- **OS**: macOS / Linux / Windows 10+ (WSL2推奨)
- **RAM**: 8GB 以上
- **Storage**: 5GB 以上の空き容量

## 🚀 インストール手順

### 1. リポジトリのクローン

```bash
git clone <repository-url>
cd guide-app
```

### 2. 環境設定ファイルの作成

#### バックエンド設定

```bash
# Laravel環境設定ファイルをコピー
cp backend/.env.example backend/.env
```

`backend/.env` ファイルを編集し、以下の設定を確認・修正してください：

```env
APP_NAME="観光ガイドアプリ"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=guide_app
DB_USERNAME=root
DB_PASSWORD=password

# Laravel Sanctum設定
SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

#### フロントエンド設定

フロントエンドの設定は `nuxt.config.ts` で管理されているため、追加の環境設定ファイルは不要です。

### 3. Dockerコンテナの起動

```bash
# バックグラウンドでコンテナを起動
docker compose up -d

# ログを確認する場合
docker compose logs -f
```

### 4. Laravelの初期設定

```bash
# Composerの依存関係をインストール
docker compose exec backend composer install

# アプリケーションキーを生成
docker compose exec backend php artisan key:generate

# データベースマイグレーションを実行
docker compose exec backend php artisan migrate

# サンプルデータを投入
docker compose exec backend php artisan db:seed
```

### 5. Nuxtの初期設定

```bash
# Node.jsの依存関係をインストール
docker compose exec frontend npm install

# 開発サーバーを起動（既にdocker composeで起動済み）
# docker compose exec frontend npm run dev
```

## 🌐 アクセス確認

セットアップが完了したら、以下のURLにアクセスして動作を確認してください：

- **フロントエンド**: http://localhost:3000
- **バックエンドAPI**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

### テストアカウント

以下のアカウントでログインできます：

- **Email**: demo@example.com
- **Password**: password123

## 🔧 開発コマンド

### よく使用するコマンド

```bash
# コンテナの状態確認
docker compose ps

# コンテナの停止
docker compose down

# コンテナの再起動
docker compose restart

# コンテナのログ確認
docker compose logs [service-name]

# 特定のコンテナに入る
docker compose exec [service-name] bash
```

### Laravel関連

```bash
# マイグレーション実行
docker compose exec backend php artisan migrate

# マイグレーションのロールバック
docker compose exec backend php artisan migrate:rollback

# シーダー実行
docker compose exec backend php artisan db:seed

# データベースリセット（全削除 + マイグレーション + シーダー）
docker compose exec backend php artisan migrate:fresh --seed

# 新しいマイグレーション作成
docker compose exec backend php artisan make:migration create_example_table

# 新しいモデル作成
docker compose exec backend php artisan make:model ExampleModel

# 新しいコントローラー作成
docker compose exec backend php artisan make:controller ExampleController
```

### Nuxt関連

```bash
# 開発サーバー起動
docker compose exec frontend npm run dev

# ビルド
docker compose exec frontend npm run build

# 本番プレビュー
docker compose exec frontend npm run preview

# 型チェック
docker compose exec frontend npm run type-check

# 新しいページ作成（手動）
# frontend/pages/ にVueファイルを作成
```

## 🐛 トラブルシューティング

### よくある問題と解決法

#### 1. ポートが既に使用されている

**エラー**: `Port already in use`

**解決法**:
```bash
# 使用中のポートを確認
lsof -i :3000
lsof -i :8000
lsof -i :3306

# プロセスを終了
kill -9 <PID>

# または、docker compose.ymlでポートを変更
```

#### 2. データベース接続エラー

**エラー**: `Connection refused` または `Access denied`

**解決法**:
```bash
# MySQLコンテナの状態確認
docker compose logs mysql

# データベース設定確認
docker compose exec backend php artisan config:clear

# データベースリセット
docker compose exec backend php artisan migrate:fresh --seed
```

#### 3. npm install エラー

**エラー**: `npm install` 時の権限エラー

**解決法**:
```bash
# npmキャッシュをクリア
docker compose exec frontend npm cache clean --force

# node_modulesを削除して再インストール
docker compose exec frontend rm -rf node_modules
docker compose exec frontend npm install
```

#### 4. Laravelのキャッシュエラー

**解決法**:
```bash
# 各種キャッシュをクリア
docker compose exec backend php artisan cache:clear
docker compose exec backend php artisan config:clear
docker compose exec backend php artisan route:clear
docker compose exec backend php artisan view:clear
```

### 完全なリセット手順

すべてを初期状態に戻したい場合：

```bash
# 1. コンテナとボリュームを削除
docker compose down -v

# 2. イメージを削除（オプション）
docker compose down --rmi all

# 3. 再構築
docker compose build --no-cache

# 4. 起動
docker compose up -d

# 5. Laravel初期設定
docker compose exec backend composer install
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate --seed

# 6. Nuxt初期設定
docker compose exec frontend npm install
```

## 📝 開発tips

### 効率的な開発のために

1. **ホットリロード**
   - フロントエンドはファイル変更時に自動リロード
   - バックエンドAPI変更時はブラウザを手動更新

2. **デバッグ**
   - `console.log()` (フロントエンド)
   - `dd()` または `Log::info()` (Laravel)

3. **データベース確認**
   - phpMyAdmin (http://localhost:8080) を使用
   - MySQL Workbenchなど外部ツールも接続可能

4. **API テスト**
   - Postmanやcurlでエンドポイントをテスト
   - Laravel Sanctumのトークン認証に注意

## 🔐 本番環境への展開

本番環境への展開については、以下の点に注意してください：

1. **環境変数の設定**
   - `APP_DEBUG=false`
   - `APP_ENV=production`
   - 安全なパスワードとキーの使用

2. **HTTPSの設定**
   - SSL証明書の設定
   - Nginx/Apache の設定

3. **パフォーマンス最適化**
   - Laravel: `php artisan optimize`
   - Nuxt: `npm run build`

4. **セキュリティ**
   - ファイアウォールの設定
   - データベースアクセスの制限
   - 定期的なセキュリティアップデート
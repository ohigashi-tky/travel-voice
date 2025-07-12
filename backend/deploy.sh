#!/bin/bash
set -e  # Exit on any error

echo "🚀 Starting deployment initialization..."

# 0. 環境変数設定
export APP_ENV=production
export APP_DEBUG=false

# デフォルトポート設定
if [ -z "$PORT" ]; then
    export PORT=8000
fi

echo "📋 Environment: APP_ENV=$APP_ENV, PORT=$PORT"

# 1. キー生成（必要な場合）
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force --no-interaction
fi

# 2. データベースファイル作成確認
echo "💾 Setting up database..."
mkdir -p database
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi
chmod 664 database/database.sqlite || true

# 3. キャッシュクリア
echo "🧹 Clearing caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# 4. マイグレーション・シーダー実行
echo "📊 Running database setup..."
php artisan migrate --seed --force --no-interaction

# 5. 設定キャッシュ
echo "⚡ Optimizing application..."
php artisan config:cache || true

# 6. ストレージリンク作成
echo "🔗 Setting up storage..."
php artisan storage:link || true

# 7. 権限設定
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache || true

echo "✅ Deployment completed!"

# 8. アプリケーション起動
echo "🏃 Starting server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
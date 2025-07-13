#!/bin/bash
# Don't exit on error to see what's happening
# set -e  # Exit on any error

echo "🚀 Starting deployment initialization..."

# 0. 環境変数設定
export APP_ENV=production
export APP_DEBUG=false

# Railwayが提供するPORT環境変数を使用（通常8080）
# デフォルトポート設定
if [ -z "$PORT" ]; then
    export PORT=8080
fi

echo "📋 Environment: APP_ENV=$APP_ENV, PORT=$PORT"

# 1. キー生成（強制実行）
echo "🔑 Generating application key..."
php artisan key:generate --force --no-interaction
echo "APP_KEY: $APP_KEY"

# 2. データベースファイル作成確認
echo "💾 Setting up database..."
mkdir -p database
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    echo "Created new SQLite database file"
fi
chmod 666 database/database.sqlite || true
ls -la database/database.sqlite

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

# 8. Laravelの設定テスト
echo "🔍 Testing Laravel configuration..."
php artisan about || echo "Laravel config test failed"

# 8. アプリケーション起動
echo "🏃 Starting server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
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
echo "📋 Database: DB_CONNECTION=$DB_CONNECTION"

# MySQL環境変数チェック
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "📋 MySQL Config: HOST=$MYSQLHOST, DATABASE=$MYSQLDATABASE, USER=$MYSQLUSER"
    if [ -z "$MYSQLHOST" ]; then
        echo "❌ ERROR: MySQL environment variables not set!"
        echo "Please add MySQL service to Railway project"
        exit 1
    fi
fi

# 1. キー生成（強制実行）
echo "🔑 Generating application key..."
php artisan key:generate --force --no-interaction
echo "APP_KEY: $APP_KEY"

# 2. データベース接続確認（MySQL）
echo "💾 Testing MySQL connection..."
php artisan migrate:status || echo "Database connection test - will be established during migration"

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
#!/bin/bash

# Railway環境での初期化スクリプト

echo "🚀 Starting deployment initialization..."

# 0. 環境変数設定
export APP_ENV=production
export APP_DEBUG=false

# デフォルトポート設定
if [ -z "$PORT" ]; then
    export PORT=8000
fi

echo "📋 Environment check:"
echo "APP_ENV: $APP_ENV"
echo "APP_DEBUG: $APP_DEBUG"
echo "PORT: $PORT"
echo "PWD: $(pwd)"

# 1. 依存関係確認
echo "📦 Checking dependencies..."
composer --version
php --version

# 2. キー生成（必要な場合）
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force --no-interaction
fi

# 3. キャッシュクリア
echo "🧹 Clearing caches..."
php artisan config:clear --quiet
php artisan cache:clear --quiet
php artisan route:clear --quiet
php artisan view:clear --quiet

# 4. データベースファイル作成確認
echo "💾 Checking database..."
if [ ! -f database/database.sqlite ]; then
    echo "Creating SQLite database file..."
    touch database/database.sqlite
fi

# 5. マイグレーション・シーダー実行
echo "📊 Running database migrations and seeders..."
php artisan migrate --seed --force --no-interaction

# 6. 設定キャッシュ（マイグレーション後）
echo "⚡ Optimizing application..."
php artisan config:cache --quiet
php artisan route:cache --quiet

# 7. ストレージリンク作成
echo "🔗 Creating storage link..."
php artisan storage:link --quiet || echo "Storage link already exists or failed"

# 8. 権限設定
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod 664 database/database.sqlite || echo "Could not set database permissions"

echo "✅ Deployment initialization completed!"

# 9. サーバー起動前の最終チェック
echo "🔍 Final health check..."
php artisan route:list --quiet | head -5 || echo "Route list failed"

# 10. アプリケーション起動
echo "🏃 Starting application server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT --no-reload
#!/bin/bash

# Railway環境での初期化スクリプト

echo "🚀 Starting deployment initialization..."

# 0. 環境変数設定
export APP_ENV=production
export APP_DEBUG=false

# 1. キー生成（必要な場合）
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# 2. キャッシュクリア
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 3. 設定キャッシュ
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache

# 4. マイグレーション・シーダー実行
echo "📊 Running database migrations and seeders..."
php artisan migrate --seed --force

# 5. ストレージリンク作成
echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Deployment initialization completed!"

# 6. アプリケーション起動
echo "🏃 Starting application server..."
php artisan serve --host=0.0.0.0 --port=$PORT
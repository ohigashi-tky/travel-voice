#!/bin/bash

# Railway環境での初期化スクリプト

echo "🚀 Starting deployment initialization..."

# 1. マイグレーション・シーダー実行
echo "📊 Running database migrations and seeders..."
php artisan migrate:fresh --seed --force

# 2. place_id更新（Google Places API）
echo "🗺️ Updating place IDs..."
php artisan travel-spots:update-place-ids --force

# 3. 観光地画像取得（Google Places API）
echo "📸 Fetching travel spot images..."
php artisan travel-spots:fetch-images --force

# 4. イベント情報取得（OpenRouter API）
echo "🎉 Fetching event information..."
php artisan events:fetch --force

echo "✅ Deployment initialization completed!"

# 5. アプリケーション起動
echo "🏃 Starting application server..."
php artisan serve --host=0.0.0.0 --port=$PORT
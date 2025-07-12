#!/bin/bash

# Railway環境での初期化スクリプト

echo "🚀 Starting deployment initialization..."

# 1. マイグレーション・シーダー実行
echo "📊 Running database migrations and seeders..."
php artisan migrate --seed --force

# 2. 観光地画像取得（新規追加分のみ）
echo "📸 Fetching images for new spots..."
php artisan travel-spots:fetch-images

# 3. イベント情報取得（eventsテーブルが空の場合のみ）
echo "🎉 Checking event data..."
EVENT_COUNT=$(php artisan tinker --execute="echo App\\Models\\Event::count();")
if [ "$EVENT_COUNT" -eq "0" ]; then
    echo "No events found. Fetching event information..."
    php artisan events:fetch --force
else
    echo "Events already exist ($EVENT_COUNT events). Skipping event fetch."
fi

echo "✅ Deployment initialization completed!"

# 4. アプリケーション起動
echo "🏃 Starting application server..."
php artisan serve --host=0.0.0.0 --port=$PORT
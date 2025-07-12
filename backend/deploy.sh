#!/bin/bash

# Railway環境での初期化スクリプト

echo "🚀 Starting deployment initialization..."

# 1. マイグレーション・シーダー実行
echo "📊 Running database migrations and seeders..."
php artisan migrate --seed --force

echo "✅ Deployment initialization completed!"

# 2. アプリケーション起動
echo "🏃 Starting application server..."
php artisan serve --host=0.0.0.0 --port=$PORT
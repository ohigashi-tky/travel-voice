# Travel Voice - アプリケーション仕様書

## 目次

1. [プロジェクト概要](#プロジェクト概要)
2. [アーキテクチャ](#アーキテクチャ)
3. [技術スタック](#技術スタック)
4. [データベース設計](#データベース設計)
5. [API仕様](#API仕様)
6. [フロントエンド構成](#フロントエンド構成)
7. [機能仕様](#機能仕様)
8. [デプロイメント](#デプロイメント)
9. [開発環境](#開発環境)
10. [テスト](#テスト)

---

## プロジェクト概要

### アプリケーション名
**Travel Voice（トラベルボイス）**

### 目的
日本全国の観光地を音声ガイド付きで案内する観光アプリケーション。高品質な音声ナレーションと現代的なUIを組み合わせ、観光客に最高の体験を提供する。

### 主要機能
- 🎧 **音声ガイド**: 各観光スポットの詳細な音声ナレーション
- 🌓 **ダークモード**: ライト/ダークテーマの切り替え
- 🔐 **認証システム**: ユーザーログイン・登録・ログアウト
- 📱 **レスポンシブデザイン**: PC、タブレット、スマートフォン対応
- ⚡ **高パフォーマンス**: Nuxt 3による最適化
- 🎨 **モダンUI**: グラデーションデザインとアニメーション
- 🗾 **インタラクティブマップ**: 都道府県ベースのナビゲーション
- 🔍 **検索・フィルター**: スポット検索とカテゴリフィルター

### ターゲットユーザー
- 日本を訪れる外国人観光客
- 日本国内の観光客
- 地域の観光情報を求める人々

---

## アーキテクチャ

### システム構成
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend      │    │   Database      │
│   (Nuxt.js 3)   │◄──►│   (Laravel 11)  │◄──►│   (MySQL 8.0)   │
│   Port: 3000    │    │   Port: 8000    │    │   Port: 3306    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌─────────────────┐
                       │   phpMyAdmin    │
                       │   Port: 8080    │
                       └─────────────────┘
```

### 主要コンポーネント
1. **フロントエンド**: Nuxt.js 3 + Vue.js 3 + TypeScript
2. **バックエンド**: Laravel 11 + PHP 8.2
3. **データベース**: MySQL 8.0
4. **認証**: Laravel Sanctum
5. **コンテナ化**: Docker + Docker Compose

---

## 技術スタック

### フロントエンド
- **フレームワーク**: Nuxt.js 3
- **言語**: TypeScript
- **UIライブラリ**: Vue.js 3 (Composition API)
- **スタイリング**: TailwindCSS
- **状態管理**: Pinia
- **ユーティリティ**: VueUse
- **アイコン**: Lucide Vue Next
- **地図**: Google Maps API

### バックエンド
- **フレームワーク**: Laravel 11
- **言語**: PHP 8.2+
- **認証**: Laravel Sanctum
- **ORM**: Eloquent
- **データベース**: MySQL 8.0
- **APIドキュメント**: OpenAPI/Swagger対応

### インフラ・ツール
- **コンテナ**: Docker & Docker Compose
- **Webサーバー**: Apache (Laravel用)
- **データベース管理**: phpMyAdmin
- **バージョン管理**: Git
- **CI/CD**: GitHub Actions（予定）

### 外部サービス
- **画像API**: Unsplash API
- **地図API**: Google Maps API
- **AI API**: OpenRouter API

---

## データベース設計

### ERD概要
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│    User     │    │TouristSpot  │    │   Guide     │
│─────────────│    │─────────────│    │─────────────│
│ id          │    │ id          │    │ id          │
│ name        │    │ name        │◄──►│ tourist_spot_id
│ email       │    │ description │    │ title       │
│ password    │    │ prefecture  │    │ content     │
│ ...         │    │ latitude    │    │ type        │
└─────────────┘    │ longitude   │    │ duration    │
                   │ ...         │    │ ...         │
                   └─────────────┘    └─────────────┘
                                              │
                                              ▼
                                    ┌─────────────┐
                                    │ AudioGuide  │
                                    │─────────────│
                                    │ id          │
                                    │ guide_id    │
                                    │ audio_file_path
                                    │ duration    │
                                    │ ...         │
                                    └─────────────┘
```

### テーブル詳細

#### 1. users テーブル
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 2. tourist_spots テーブル
```sql
CREATE TABLE tourist_spots (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    prefecture VARCHAR(50) NOT NULL,
    city VARCHAR(100),
    address TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    category VARCHAR(100),
    images JSON,
    opening_hours JSON,
    admission_fee VARCHAR(255),
    website VARCHAR(255),
    phone VARCHAR(20),
    access_info TEXT,
    highlights JSON,
    best_visit_time VARCHAR(100),
    difficulty_level ENUM('easy', 'moderate', 'hard'),
    estimated_duration_minutes INT,
    accessibility_info TEXT,
    parking_info TEXT,
    nearby_facilities JSON,
    seasonal_info JSON,
    weather_considerations TEXT,
    photography_rules TEXT,
    cultural_significance TEXT,
    historical_background TEXT,
    local_tips TEXT,
    safety_information TEXT,
    contact_info JSON,
    social_media JSON,
    booking_required BOOLEAN DEFAULT FALSE,
    booking_info TEXT,
    group_discounts JSON,
    facilities JSON,
    transportation JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 3. guides テーブル
```sql
CREATE TABLE guides (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tourist_spot_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    type ENUM('text', 'audio', 'video') DEFAULT 'text',
    duration_minutes INT,
    order_index INT DEFAULT 0,
    highlights JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tourist_spot_id) REFERENCES tourist_spots(id) ON DELETE CASCADE
);
```

#### 4. audio_guides テーブル
```sql
CREATE TABLE audio_guides (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    guide_id BIGINT NOT NULL,
    audio_file_path VARCHAR(255),
    audio_file_url VARCHAR(255),
    duration_seconds INT,
    file_size BIGINT,
    format VARCHAR(20),
    voice_actor VARCHAR(100),
    language VARCHAR(10) DEFAULT 'ja',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (guide_id) REFERENCES guides(id) ON DELETE CASCADE
);
```

#### 5. personal_access_tokens テーブル（Laravel Sanctum）
```sql
CREATE TABLE personal_access_tokens (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## API仕様

### ベースURL
- **開発環境**: `http://localhost:8000/api`
- **本番環境**: `https://your-domain.com/api`

### 共通レスポンス形式
```json
{
    "success": true,
    "data": {},
    "message": "Success",
    "errors": []
}
```

### 認証エンドポイント

#### POST /api/register
ユーザー登録

**リクエスト**:
```json
{
    "name": "山田太郎",
    "email": "yamada@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**レスポンス**:
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "山田太郎",
            "email": "yamada@example.com"
        },
        "token": "1|abcdef123456..."
    },
    "message": "User registered successfully"
}
```

#### POST /api/login
ユーザーログイン

**リクエスト**:
```json
{
    "email": "yamada@example.com",
    "password": "password123"
}
```

**レスポンス**:
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "山田太郎",
            "email": "yamada@example.com"
        },
        "token": "1|abcdef123456..."
    },
    "message": "Login successful"
}
```

#### POST /api/logout
ユーザーログアウト（認証必須）

**ヘッダー**:
```
Authorization: Bearer {token}
```

**レスポンス**:
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

#### GET /api/user
ユーザー情報取得（認証必須）

**レスポンス**:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "山田太郎",
        "email": "yamada@example.com",
        "created_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 観光スポットエンドポイント

#### GET /api/tourist-spots
観光スポット一覧取得

**クエリパラメータ**:
- `prefecture`: 都道府県名
- `category`: カテゴリ名
- `limit`: 取得件数（デフォルト: 20）
- `page`: ページ番号

**レスポンス**:
```json
{
    "success": true,
    "data": {
        "spots": [
            {
                "id": 1,
                "name": "東京スカイツリー",
                "description": "東京の新しいシンボル...",
                "prefecture": "東京都",
                "city": "墨田区",
                "latitude": 35.7101,
                "longitude": 139.8107,
                "category": "展望台",
                "images": ["image1.jpg", "image2.jpg"],
                "opening_hours": {...},
                "admission_fee": "大人 2,100円",
                "guides_count": 3
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 5,
            "total_items": 100
        }
    }
}
```

#### GET /api/tourist-spots/{id}
観光スポット詳細取得

**レスポンス**:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "東京スカイツリー",
        "description": "東京の新しいシンボル...",
        "prefecture": "東京都",
        "city": "墨田区",
        "address": "東京都墨田区押上1-1-2",
        "latitude": 35.7101,
        "longitude": 139.8107,
        "category": "展望台",
        "images": ["image1.jpg", "image2.jpg"],
        "opening_hours": {...},
        "admission_fee": "大人 2,100円",
        "website": "https://www.tokyo-skytree.jp/",
        "phone": "03-1234-5678",
        "access_info": "押上駅より徒歩1分",
        "guides": [
            {
                "id": 1,
                "title": "スカイツリーの歴史",
                "content": "東京スカイツリーは...",
                "type": "audio",
                "duration_minutes": 5,
                "audio_guides": [
                    {
                        "id": 1,
                        "audio_file_url": "https://example.com/audio1.mp3",
                        "duration_seconds": 300,
                        "voice_actor": "田中花子",
                        "language": "ja"
                    }
                ]
            }
        ]
    }
}
```

#### GET /api/tourist-spots/prefecture/{prefecture}
都道府県別観光スポット取得

**レスポンス**:
```json
{
    "success": true,
    "data": {
        "prefecture": "東京都",
        "spots": [...]
    }
}
```

### ガイドエンドポイント

#### GET /api/guides/{id}
ガイド詳細取得

**レスポンス**:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "スカイツリーの歴史",
        "content": "東京スカイツリーは...",
        "type": "audio",
        "duration_minutes": 5,
        "tourist_spot": {
            "id": 1,
            "name": "東京スカイツリー"
        },
        "audio_guides": [...]
    }
}
```

---

## フロントエンド構成

### ディレクトリ構造
```
frontend/
├── components/           # Vue コンポーネント
│   ├── AppHeader.vue    # ヘッダーコンポーネント
│   ├── AppFooter.vue    # フッターコンポーネント
│   ├── AudioGuidePlayer.vue  # 音声プレイヤー
│   ├── UnsplashImage.vue     # 画像コンポーネント
│   └── ...
├── composables/          # Composition API ファンクション
│   ├── useTouristSpots.ts    # 観光スポット関連
│   ├── useLanguage.ts        # 言語切り替え
│   ├── useUnsplashImages.ts  # 画像取得
│   └── ...
├── layouts/              # レイアウトテンプレート
│   └── default.vue      # デフォルトレイアウト
├── middleware/           # ミドルウェア
│   ├── auth.ts          # 認証チェック
│   └── guest.ts         # ゲストユーザー制御
├── pages/                # ページコンポーネント
│   ├── index.vue        # トップページ
│   ├── login.vue        # ログインページ
│   ├── register.vue     # 登録ページ
│   ├── spots/
│   │   ├── index.vue    # スポット一覧
│   │   └── [id].vue     # スポット詳細
│   ├── prefecture/
│   │   └── [name].vue   # 都道府県別ページ
│   └── ...
├── plugins/              # プラグイン
│   ├── api.client.ts    # API クライアント
│   └── 01.auth.client.ts # 認証プラグイン
├── server/               # サーバーAPI
│   └── api/
│       ├── chat.post.ts      # チャット API
│       ├── translate.post.ts # 翻訳 API
│       └── unsplash/
│           └── search.post.ts # 画像検索 API
├── stores/               # Pinia ストア
│   └── auth.ts          # 認証ストア
├── types/                # TypeScript 型定義
│   ├── index.ts         # 共通型
│   └── google-maps.d.ts # Google Maps 型
└── nuxt.config.ts       # Nuxt 設定
```

### 主要コンポーネント

#### AppHeader.vue
```vue
<template>
  <header class="bg-white dark:bg-gray-800 shadow-sm">
    <nav class="container mx-auto px-4 py-4">
      <div class="flex justify-between items-center">
        <NuxtLink to="/" class="text-2xl font-bold text-primary">
          Travel Voice
        </NuxtLink>
        <div class="flex items-center space-x-4">
          <button @click="toggleDarkMode" class="p-2">
            <Icon :name="isDark ? 'sun' : 'moon'" />
          </button>
          <div v-if="user" class="flex items-center space-x-2">
            <span>{{ user.name }}</span>
            <button @click="logout" class="btn-secondary">
              ログアウト
            </button>
          </div>
          <div v-else class="space-x-2">
            <NuxtLink to="/login" class="btn-primary">
              ログイン
            </NuxtLink>
            <NuxtLink to="/register" class="btn-secondary">
              登録
            </NuxtLink>
          </div>
        </div>
      </div>
    </nav>
  </header>
</template>
```

#### AudioGuidePlayer.vue
```vue
<template>
  <div class="audio-player bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <div class="flex items-center space-x-4">
      <button @click="togglePlay" class="play-button">
        <Icon :name="isPlaying ? 'pause' : 'play'" size="24" />
      </button>
      <div class="flex-1">
        <h3 class="font-semibold">{{ audioGuide.title }}</h3>
        <div class="flex items-center space-x-2 mt-2">
          <span class="text-sm text-gray-500">
            {{ formatTime(currentTime) }}
          </span>
          <div class="flex-1 bg-gray-200 rounded-full h-2">
            <div 
              class="bg-blue-500 h-2 rounded-full"
              :style="{ width: `${progress}%` }"
            ></div>
          </div>
          <span class="text-sm text-gray-500">
            {{ formatTime(duration) }}
          </span>
        </div>
      </div>
    </div>
    <audio
      ref="audioRef"
      :src="audioGuide.audio_file_url"
      @timeupdate="updateProgress"
      @ended="onEnded"
    ></audio>
  </div>
</template>
```

### Composables

#### useTouristSpots.ts
```typescript
export const useTouristSpots = () => {
  const { $api } = useNuxtApp()
  
  const spots = ref<TouristSpot[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  
  const fetchSpots = async (params?: {
    prefecture?: string
    category?: string
    limit?: number
    page?: number
  }) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await $api.get('/tourist-spots', { params })
      spots.value = response.data.spots
    } catch (err) {
      error.value = 'スポットの取得に失敗しました'
      console.error(err)
    } finally {
      loading.value = false
    }
  }
  
  const fetchSpotById = async (id: number) => {
    const response = await $api.get(`/tourist-spots/${id}`)
    return response.data
  }
  
  return {
    spots: readonly(spots),
    loading: readonly(loading),
    error: readonly(error),
    fetchSpots,
    fetchSpotById
  }
}
```

### 型定義

#### types/index.ts
```typescript
export interface TouristSpot {
  id: number
  name: string
  description: string
  prefecture: string
  city?: string
  address?: string
  latitude?: number
  longitude?: number
  category?: string
  images?: string[]
  opening_hours?: Record<string, any>
  admission_fee?: string
  website?: string
  phone?: string
  access_info?: string
  guides?: Guide[]
  created_at: string
  updated_at: string
}

export interface Guide {
  id: number
  tourist_spot_id: number
  title: string
  content?: string
  type: 'text' | 'audio' | 'video'
  duration_minutes?: number
  order_index: number
  highlights?: string[]
  audio_guides?: AudioGuide[]
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface AudioGuide {
  id: number
  guide_id: number
  audio_file_path?: string
  audio_file_url?: string
  duration_seconds?: number
  file_size?: number
  format?: string
  voice_actor?: string
  language: string
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface User {
  id: number
  name: string
  email: string
  email_verified_at?: string
  created_at: string
  updated_at: string
}

export interface AuthResponse {
  user: User
  token: string
}

export interface ApiResponse<T = any> {
  success: boolean
  data: T
  message: string
  errors?: string[]
}
```

---

## 機能仕様

### 1. 認証機能

#### ユーザー登録
- **画面**: `/register`
- **機能**: 
  - 名前、メールアドレス、パスワード入力
  - パスワード確認
  - バリデーション（メール形式、パスワード強度）
  - 登録成功時自動ログイン

#### ログイン
- **画面**: `/login`
- **機能**:
  - メールアドレス、パスワード入力
  - ログイン状態の永続化
  - ログイン後のリダイレクト

#### ログアウト
- **機能**:
  - セッション終了
  - トークン無効化
  - トップページへリダイレクト

### 2. 観光スポット機能

#### スポット一覧
- **画面**: `/spots`
- **機能**:
  - スポット一覧表示
  - 都道府県フィルタ
  - カテゴリフィルタ
  - ページネーション
  - レスポンシブグリッド表示

#### スポット詳細
- **画面**: `/spots/[id]`
- **機能**:
  - スポット詳細情報表示
  - 画像ギャラリー
  - アクセス情報
  - 営業時間・料金
  - 音声ガイド一覧
  - Google Maps 連携

#### 都道府県別ページ
- **画面**: `/prefecture/[name]`
- **機能**:
  - 都道府県内スポット一覧
  - 地域別フィルタ
  - おすすめスポット表示

### 3. 音声ガイド機能

#### 音声プレイヤー
- **機能**:
  - 音声再生・停止・一時停止
  - 進捗バー表示・操作
  - 音量調整
  - スキップ機能
  - バックグラウンド再生

#### ガイド管理
- **機能**:
  - ガイド一覧表示
  - 順序による自動再生
  - お気に入り機能（将来実装）
  - 再生履歴（将来実装）

### 4. ユーザーインターフェース

#### ダークモード
- **機能**:
  - ライト/ダークテーマ切り替え
  - 設定の永続化
  - システム設定との連動

#### レスポンシブデザイン
- **対応デバイス**:
  - デスクトップ（1024px+）
  - タブレット（768px - 1023px）
  - スマートフォン（〜767px）

#### 多言語対応（将来実装）
- **対応言語**:
  - 日本語（デフォルト）
  - 英語
  - 中国語（簡体字・繁体字）
  - 韓国語

### 5. 検索・フィルタ機能

#### 全文検索
- **機能**:
  - スポット名・説明文検索
  - 都道府県・市区町村検索
  - カテゴリ検索

#### 高度なフィルタ
- **条件**:
  - 都道府県
  - カテゴリ
  - 入場料金
  - 営業時間
  - アクセス方法

### 6. 地図機能

#### Google Maps 連携
- **機能**:
  - スポット位置表示
  - ルート検索
  - 周辺スポット表示
  - ストリートビュー

#### インタラクティブマップ
- **機能**:
  - 都道府県選択
  - ズーム・パン操作
  - マーカークリック詳細表示

---

## デプロイメント

### Docker Compose 構成

#### docker-compose.yml
```yaml
version: '3.8'

services:
  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile
    ports:
      - "3000:3000"
    environment:
      - NUXT_API_BASE_URL=http://backend:8000/api
      - NUXT_PUBLIC_GOOGLE_MAPS_API_KEY=${GOOGLE_MAPS_API_KEY}
      - NUXT_OPENROUTER_API_KEY=${OPENROUTER_API_KEY}
    depends_on:
      - backend
    volumes:
      - ./frontend:/app
      - /app/node_modules

  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    environment:
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=travel_voice
      - DB_USERNAME=root
      - DB_PASSWORD=password
      - APP_URL=http://localhost:8000
      - FRONTEND_URL=http://localhost:3000
      - SANCTUM_STATEFUL_DOMAINS=localhost:3000
    depends_on:
      - mysql
    volumes:
      - ./backend:/var/www/html

  mysql:
    image: mysql:8.0
    ports:
      - "3306:3306"
    environment:
      - MYSQL_ROOT_PASSWORD=password
      - MYSQL_DATABASE=travel_voice
      - MYSQL_USER=user
      - MYSQL_PASSWORD=password
    volumes:
      - mysql_data:/var/lib/mysql
      - ./mysql/init:/docker-entrypoint-initdb.d

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8080:80"
    environment:
      - PMA_HOST=mysql
      - PMA_USER=root
      - PMA_PASSWORD=password
    depends_on:
      - mysql

volumes:
  mysql_data:
```

### 環境変数

#### フロントエンド (.env)
```bash
# API設定
NUXT_API_BASE_URL=http://localhost:8000/api
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000/api

# 外部API
NUXT_PUBLIC_GOOGLE_MAPS_API_KEY=your_google_maps_api_key
NUXT_OPENROUTER_API_KEY=your_openrouter_api_key

# Unsplash API
NUXT_UNSPLASH_ACCESS_KEY=your_unsplash_access_key
```

#### バックエンド (.env)
```bash
# アプリケーション設定
APP_NAME="Travel Voice"
APP_ENV=local
APP_KEY=base64:your_app_key
APP_DEBUG=true
APP_URL=http://localhost:8000

# フロントエンド設定
FRONTEND_URL=http://localhost:3000
SANCTUM_STATEFUL_DOMAINS=localhost:3000

# データベース設定
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=travel_voice
DB_USERNAME=root
DB_PASSWORD=password

# メール設定（将来実装）
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### デプロイ手順

#### 開発環境
```bash
# 1. リポジトリクローン
git clone https://github.com/your-username/travel-voice.git
cd travel-voice

# 2. 環境変数設定
cp frontend/.env.example frontend/.env
cp backend/.env.example backend/.env

# 3. Docker コンテナ起動
docker-compose up -d

# 4. Laravel セットアップ
docker-compose exec backend composer install
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate --seed

# 5. フロントエンド セットアップ
docker-compose exec frontend npm install
```

#### 本番環境
```bash
# 1. サーバー設定
# - Docker & Docker Compose インストール
# - SSL証明書設定（Let's Encrypt）
# - ドメイン設定

# 2. 環境変数設定（本番用）
# - データベース認証情報
# - API キー
# - ドメイン設定

# 3. デプロイ
docker-compose -f docker-compose.prod.yml up -d

# 4. データベース初期化
docker-compose exec backend php artisan migrate --force
docker-compose exec backend php artisan db:seed --force
```

---

## 開発環境

### 必要なツール
- **Docker Desktop**: 最新版
- **Node.js**: 18.x 以上
- **PHP**: 8.2 以上
- **Composer**: 2.x 以上
- **Git**: 最新版

### 開発サーバー起動
```bash
# 全サービス起動
docker-compose up -d

# 個別サービス起動
docker-compose up frontend backend mysql

# ログ確認
docker-compose logs -f frontend
docker-compose logs -f backend
```

### データベース操作
```bash
# マイグレーション実行
docker-compose exec backend php artisan migrate

# シーダー実行
docker-compose exec backend php artisan db:seed

# データベースリセット
docker-compose exec backend php artisan migrate:fresh --seed
```

### デバッグ
```bash
# Laravel ログ
docker-compose exec backend tail -f storage/logs/laravel.log

# Nuxt デバッグモード
docker-compose exec frontend npm run dev -- --debug

# データベースアクセス
# phpMyAdmin: http://localhost:8080
# MySQL CLI: docker-compose exec mysql mysql -u root -p
```

---

## テスト

### バックエンドテスト

#### PHPUnit 設定
```php
// phpunit.xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

#### テスト実行
```bash
# 全テスト実行
docker-compose exec backend php artisan test

# 特定テスト実行
docker-compose exec backend php artisan test --filter AuthTest

# カバレッジ生成
docker-compose exec backend php artisan test --coverage
```

### フロントエンドテスト

#### Jest 設定
```javascript
// jest.config.js
module.exports = {
  testEnvironment: 'jsdom',
  moduleNameMapping: {
    '^@/(.*)$': '<rootDir>/$1',
    '^~/(.*)$': '<rootDir>/$1'
  },
  transform: {
    '^.+\\.(js|jsx|ts|tsx)$': 'babel-jest'
  },
  collectCoverageFrom: [
    'components/**/*.vue',
    'pages/**/*.vue',
    'composables/**/*.ts'
  ]
}
```

#### テスト実行
```bash
# 単体テスト
docker-compose exec frontend npm run test

# E2Eテスト
docker-compose exec frontend npm run test:e2e

# テストカバレッジ
docker-compose exec frontend npm run test:coverage
```

### 統合テスト

#### API テスト
```bash
# Postman Collection
# - 認証エンドポイント
# - 観光スポットエンドポイント
# - ガイドエンドポイント

# Newman（CLI）実行
newman run postman_collection.json -e environment.json
```

#### ブラウザテスト
```bash
# Cypress
docker-compose exec frontend npx cypress open

# Playwright
docker-compose exec frontend npx playwright test
```

---

## 今後の拡張予定

### 近期（3ヶ月以内）
- [ ] 多言語対応（英語、中国語、韓国語）
- [ ] お気に入り機能
- [ ] レビュー・評価機能
- [ ] プッシュ通知
- [ ] オフライン対応

### 中期（6ヶ月以内）
- [ ] AR機能
- [ ] ソーシャル機能（SNS連携）
- [ ] 決済機能（プレミアムコンテンツ）
- [ ] 管理者ダッシュボード
- [ ] 分析・レポート機能

### 長期（1年以内）
- [ ] AI音声ガイド生成
- [ ] 360度バーチャルツアー
- [ ] チャットボット
- [ ] モバイルアプリ（iOS/Android）
- [ ] ウェアラブル端末対応

---

## 付録

### A. 都道府県マスタデータ
```json
{
  "prefectures": [
    { "code": "01", "name": "北海道", "region": "北海道" },
    { "code": "02", "name": "青森県", "region": "東北" },
    { "code": "03", "name": "岩手県", "region": "東北" },
    { "code": "04", "name": "宮城県", "region": "東北" },
    { "code": "05", "name": "秋田県", "region": "東北" },
    { "code": "06", "name": "山形県", "region": "東北" },
    { "code": "07", "name": "福島県", "region": "東北" },
    { "code": "08", "name": "茨城県", "region": "関東" },
    { "code": "09", "name": "栃木県", "region": "関東" },
    { "code": "10", "name": "群馬県", "region": "関東" },
    { "code": "11", "name": "埼玉県", "region": "関東" },
    { "code": "12", "name": "千葉県", "region": "関東" },
    { "code": "13", "name": "東京都", "region": "関東" },
    { "code": "14", "name": "神奈川県", "region": "関東" },
    { "code": "15", "name": "新潟県", "region": "中部" },
    { "code": "16", "name": "富山県", "region": "中部" },
    { "code": "17", "name": "石川県", "region": "中部" },
    { "code": "18", "name": "福井県", "region": "中部" },
    { "code": "19", "name": "山梨県", "region": "中部" },
    { "code": "20", "name": "長野県", "region": "中部" },
    { "code": "21", "name": "岐阜県", "region": "中部" },
    { "code": "22", "name": "静岡県", "region": "中部" },
    { "code": "23", "name": "愛知県", "region": "中部" },
    { "code": "24", "name": "三重県", "region": "近畿" },
    { "code": "25", "name": "滋賀県", "region": "近畿" },
    { "code": "26", "name": "京都府", "region": "近畿" },
    { "code": "27", "name": "大阪府", "region": "近畿" },
    { "code": "28", "name": "兵庫県", "region": "近畿" },
    { "code": "29", "name": "奈良県", "region": "近畿" },
    { "code": "30", "name": "和歌山県", "region": "近畿" },
    { "code": "31", "name": "鳥取県", "region": "中国" },
    { "code": "32", "name": "島根県", "region": "中国" },
    { "code": "33", "name": "岡山県", "region": "中国" },
    { "code": "34", "name": "広島県", "region": "中国" },
    { "code": "35", "name": "山口県", "region": "中国" },
    { "code": "36", "name": "徳島県", "region": "四国" },
    { "code": "37", "name": "香川県", "region": "四国" },
    { "code": "38", "name": "愛媛県", "region": "四国" },
    { "code": "39", "name": "高知県", "region": "四国" },
    { "code": "40", "name": "福岡県", "region": "九州" },
    { "code": "41", "name": "佐賀県", "region": "九州" },
    { "code": "42", "name": "長崎県", "region": "九州" },
    { "code": "43", "name": "熊本県", "region": "九州" },
    { "code": "44", "name": "大分県", "region": "九州" },
    { "code": "45", "name": "宮崎県", "region": "九州" },
    { "code": "46", "name": "鹿児島県", "region": "九州" },
    { "code": "47", "name": "沖縄県", "region": "沖縄" }
  ]
}
```

### B. カテゴリマスタデータ
```json
{
  "categories": [
    { "id": "temple", "name": "寺院・神社", "icon": "torii-gate" },
    { "id": "castle", "name": "城・歴史建造物", "icon": "castle" },
    { "id": "museum", "name": "博物館・美術館", "icon": "museum" },
    { "id": "nature", "name": "自然・公園", "icon": "tree" },
    { "id": "mountain", "name": "山・高原", "icon": "mountain" },
    { "id": "beach", "name": "海・ビーチ", "icon": "waves" },
    { "id": "hotspring", "name": "温泉", "icon": "hot-springs" },
    { "id": "shopping", "name": "ショッピング", "icon": "shopping-bag" },
    { "id": "food", "name": "グルメ", "icon": "utensils" },
    { "id": "entertainment", "name": "エンターテイメント", "icon": "ticket" },
    { "id": "observation", "name": "展望台", "icon": "eye" },
    { "id": "traditional", "name": "伝統文化", "icon": "fan" }
  ]
}
```

### C. テストデータ
```sql
-- 観光スポットテストデータ
INSERT INTO tourist_spots (name, description, prefecture, city, latitude, longitude, category) VALUES
('東京スカイツリー', '東京の新しいシンボル、634mの電波塔', '東京都', '墨田区', 35.7101, 139.8107, 'observation'),
('浅草寺', '東京最古の寺院、雷門で有名', '東京都', '台東区', 35.7148, 139.7967, 'temple'),
('明治神宮', '明治天皇を祀る神社、初詣で人気', '東京都', '渋谷区', 35.6764, 139.6993, 'temple'),
('皇居東御苑', '江戸城の遺構が残る美しい庭園', '東京都', '千代田区', 35.6852, 139.7528, 'nature'),
('上野動物園', '日本最古の動物園、パンダで有名', '東京都', '台東区', 35.7181, 139.7714, 'entertainment');
```

---

## 改訂履歴

| バージョン | 日付 | 変更内容 | 担当者 |
|-----------|------|---------|--------|
| 1.0.0 | 2024-01-01 | 初版作成 | 開発チーム |
| 1.1.0 | 2024-02-01 | API仕様追加 | 開発チーム |
| 1.2.0 | 2024-03-01 | フロントエンド仕様追加 | 開発チーム |

---

*© 2024 Travel Voice Development Team. All rights reserved.*
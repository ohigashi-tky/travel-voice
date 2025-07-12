# API仕様書

観光地音声ガイドアプリのREST API仕様について説明します。

## 📋 概要

- **Base URL**: `http://localhost:8000/api`
- **Content Type**: `application/json`
- **Authentication**: Laravel Sanctum (Bearer Token)

## 🔐 認証

### ログイン

```http
POST /api/login
```

**Request Body:**
```json
{
  "email": "demo@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "name": "デモユーザー",
    "email": "demo@example.com",
    "email_verified_at": null,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|abc123def456..."
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["認証情報が正しくありません。"]
  }
}
```

### 新規登録

```http
POST /api/register
```

**Request Body:**
```json
{
  "name": "山田太郎",
  "email": "yamada@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "user": {
    "id": 2,
    "name": "山田太郎",
    "email": "yamada@example.com",
    "email_verified_at": null,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "2|xyz789abc123..."
}
```

### ログアウト

```http
POST /api/logout
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "ログアウトしました"
}
```

### ユーザー情報取得

```http
GET /api/user
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "name": "デモユーザー",
  "email": "demo@example.com",
  "email_verified_at": null,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

## 🗾 観光スポット

### 観光スポット一覧取得

```http
GET /api/travel-spots
```

**Query Parameters:**
- `prefecture` (optional): 都道府県で絞り込み
- `category` (optional): カテゴリで絞り込み

**Example:**
```http
GET /api/travel-spots?prefecture=東京都&category=寺院
```

**Response (200):**
```json
[
  {
    "id": 1,
    "name": "東京スカイツリー",
    "description": "東京の新しいシンボルタワー。高さ634mの世界一高い自立式電波塔で、展望デッキからは関東平野を一望できます。",
    "prefecture": "東京都",
    "city": "墨田区",
    "address": "東京都墨田区押上1-1-2",
    "latitude": "35.71006300",
    "longitude": "139.81070000",
    "category": "観光施設",
    "images": [
      "https://example.com/skytree1.jpg",
      "https://example.com/skytree2.jpg"
    ],
    "access_info": "東武スカイツリーライン「とうきょうスカイツリー駅」すぐ、東京メトロ半蔵門線「押上駅」徒歩5分",
    "website": "https://www.tokyo-skytree.jp/",
    "phone": "0570-55-0634",
    "opening_hours": "8:00-22:00（最終入場21:00）",
    "admission_fee": "大人2,100円〜、中学生1,550円〜、小学生950円〜",
    "is_active": true,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "active_guides": [
      {
        "id": 1,
        "title": "東京スカイツリー完全ガイド",
        "type": "audio",
        "duration_minutes": 8,
        "order": 1
      }
    ]
  }
]
```

### 観光スポット詳細取得

```http
GET /api/travel-spots/{id}
```

**Response (200):**
```json
{
  "id": 1,
  "name": "東京スカイツリー",
  "description": "東京の新しいシンボルタワー。高さ634mの世界一高い自立式電波塔で、展望デッキからは関東平野を一望できます。",
  "prefecture": "東京都",
  "city": "墨田区",
  "address": "東京都墨田区押上1-1-2",
  "latitude": "35.71006300",
  "longitude": "139.81070000",
  "category": "観光施設",
  "images": [
    "https://example.com/skytree1.jpg",
    "https://example.com/skytree2.jpg"
  ],
  "access_info": "東武スカイツリーライン「とうきょうスカイツリー駅」すぐ、東京メトロ半蔵門線「押上駅」徒歩5分",
  "website": "https://www.tokyo-skytree.jp/",
  "phone": "0570-55-0634",
  "opening_hours": "8:00-22:00（最終入場21:00）",
  "admission_fee": "大人2,100円〜、中学生1,550円〜、小学生950円〜",
  "is_active": true,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z",
  "active_guides": [
    {
      "id": 1,
      "travel_spot_id": 1,
      "title": "東京スカイツリー完全ガイド",
      "content": "2012年に開業した東京スカイツリーは、高さ634mの世界一高い自立式電波塔です。...",
      "type": "audio",
      "duration_minutes": 8,
      "order": 1,
      "highlights": [
        "世界一高い自立式電波塔（634m）",
        "最新の耐震技術を採用",
        "晴れた日には富士山も見える展望デッキ",
        "伝統技術と最新技術の融合"
      ],
      "is_active": true,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "active_audio_guides": [
        {
          "id": 1,
          "guide_id": 1,
          "audio_file_path": "/audio/skytree_complete_guide.mp3",
          "audio_file_url": "https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3",
          "duration_seconds": 480,
          "format": "mp3",
          "file_size": 7680000,
          "voice_actor": "田中美咲",
          "language": "ja",
          "is_active": true,
          "created_at": "2024-01-01T00:00:00.000000Z",
          "updated_at": "2024-01-01T00:00:00.000000Z"
        }
      ]
    }
  ]
}
```

**Error Response (404):**
```json
{
  "message": "No query results for model [App\\Models\\TravelSpot] 999"
}
```

### 都道府県別観光スポット取得

```http
GET /api/travel-spots/prefecture/{prefecture}
```

**Example:**
```http
GET /api/travel-spots/prefecture/東京都
```

**Response (200):**
```json
[
  {
    "id": 1,
    "name": "東京スカイツリー",
    "description": "東京の新しいシンボルタワー。...",
    "prefecture": "東京都",
    "city": "墨田区",
    // ... 他のフィールド
    "active_guides": [...]
  },
  {
    "id": 2,
    "name": "浅草寺",
    "description": "東京最古の寺院として知られ、...",
    "prefecture": "東京都",
    "city": "台東区",
    // ... 他のフィールド
    "active_guides": [...]
  }
]
```

## 🎧 ガイド

### ガイド詳細取得

```http
GET /api/guides/{id}
```

**Response (200):**
```json
{
  "id": 1,
  "travel_spot_id": 1,
  "title": "東京スカイツリー完全ガイド",
  "content": "2012年に開業した東京スカイツリーは、高さ634mの世界一高い自立式電波塔です。その名前の由来は「空」を表すSkyと「木」を表すTreeを組み合わせたもので、日本の伝統的な建築技術と最新技術が融合した驚くべき建造物です。...",
  "type": "audio",
  "duration_minutes": 8,
  "order": 1,
  "highlights": [
    "世界一高い自立式電波塔（634m）",
    "最新の耐震技術を採用",
    "晴れた日には富士山も見える展望デッキ",
    "伝統技術と最新技術の融合"
  ],
  "is_active": true,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z",
  "tourist_spot": {
    "id": 1,
    "name": "東京スカイツリー",
    "prefecture": "東京都",
    "city": "墨田区"
  },
  "active_audio_guides": [
    {
      "id": 1,
      "audio_file_url": "https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3",
      "duration_seconds": 480,
      "format": "mp3",
      "voice_actor": "田中美咲",
      "language": "ja"
    }
  ]
}
```

## 📊 データモデル

### TravelSpot (観光スポット)

| フィールド | 型 | 説明 |
|---|---|---|
| id | integer | ID |
| name | string | 名称 |
| description | text | 説明 |
| prefecture | string | 都道府県 |
| city | string | 市区町村 |
| address | string | 住所 |
| latitude | decimal | 緯度 |
| longitude | decimal | 経度 |
| category | string | カテゴリ |
| images | json | 画像URL配列 |
| access_info | text | アクセス情報 |
| website | string | 公式サイト |
| phone | string | 電話番号 |
| opening_hours | text | 営業時間 |
| admission_fee | text | 入場料 |
| is_active | boolean | 有効フラグ |

### Guide (ガイド)

| フィールド | 型 | 説明 |
|---|---|---|
| id | integer | ID |
| travel_spot_id | integer | 観光スポットID |
| title | string | タイトル |
| content | text | 内容 |
| type | string | タイプ (text/audio/video) |
| duration_minutes | integer | 所要時間（分） |
| order | integer | 表示順 |
| highlights | json | ハイライト配列 |
| is_active | boolean | 有効フラグ |

### AudioGuide (音声ガイド)

| フィールド | 型 | 説明 |
|---|---|---|
| id | integer | ID |
| guide_id | integer | ガイドID |
| audio_file_path | string | 音声ファイルパス |
| audio_file_url | string | 音声ファイルURL |
| duration_seconds | integer | 長さ（秒） |
| format | string | ファイル形式 |
| file_size | integer | ファイルサイズ |
| voice_actor | string | 声優名 |
| language | string | 言語 |
| is_active | boolean | 有効フラグ |

## ⚠️ エラーレスポンス

### 一般的なエラー

#### 認証エラー (401)
```json
{
  "message": "Unauthenticated."
}
```

#### 認可エラー (403)
```json
{
  "message": "This action is unauthorized."
}
```

#### リソースが見つからない (404)
```json
{
  "message": "No query results for model [App\\Models\\TravelSpot] 999"
}
```

#### バリデーションエラー (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

#### サーバーエラー (500)
```json
{
  "message": "Server Error"
}
```

## 🔧 レート制限

現在、レート制限は設定されていませんが、本番環境では以下の制限を予定しています：

- 認証なし: 100リクエスト/分
- 認証あり: 300リクエスト/分

## 📝 使用例

### JavaScript (Fetch API)

```javascript
// ログイン
const loginResponse = await fetch('http://localhost:8000/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'demo@example.com',
    password: 'password123'
  })
});

const { user, token } = await loginResponse.json();

// 観光スポット取得
const spotsResponse = await fetch('http://localhost:8000/api/travel-spots?prefecture=東京都', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});

const spots = await spotsResponse.json();
```

### cURL

```bash
# ログイン
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@example.com","password":"password123"}'

# 観光スポット取得
curl -X GET "http://localhost:8000/api/travel-spots?prefecture=東京都" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```
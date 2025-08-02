# 観光ガイドアプリ API仕様書

Laravel + Nuxt.js による音声ガイド付き観光アプリケーションのAPI仕様書

**Base URL**: `http://localhost:8000` (開発環境) / `https://travel-voice-production.up.railway.app` (本番環境)

## 目次

1. [認証 / Authentication](#認証--authentication)
2. [観光地 / Travel Spots](#観光地--travel-spots)
3. [都道府県 / Prefectures](#都道府県--prefectures)
4. [人気スポット / Popular Spots](#人気スポット--popular-spots)
5. [イベント / Events](#イベント--events)
6. [音声ガイド / Audio Guide](#音声ガイド--audio-guide)

---

## 認証 / Authentication

### ログイン
`POST /api/login`

**リクエストボディ**:
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**レスポンス**:
```json
{
  "access_token": "1|abcdef123456...",
  "token_type": "Bearer"
}
```

### ユーザー登録
`POST /api/register`

**リクエストボディ**:
```json
{
  "name": "ユーザー名",
  "email": "user@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### ログアウト
`POST /api/logout`

**認証必須**: Bearer Token

### ユーザー情報取得
`GET /api/user`

**認証必須**: Bearer Token

---

## 観光地 / Travel Spots

### 観光地一覧の取得
`GET /api/travel-spots`

すべての観光地情報を取得します。各観光地には関連する画像情報も含まれます。

**レスポンス例**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "東京タワー",
      "description": "東京のシンボルとして知られる電波塔",
      "latitude": 35.6586,
      "longitude": 139.7454,
      "address": "東京都港区芝公園4丁目2-8",
      "place_id": "ChIJCewJkL2LGGAR3Qmk0vCTGkg",
      "category": "観光地",
      "prefecture_id": 13,
      "spot_images": [
        {
          "id": 1,
          "travel_spot_id": 1,
          "image_url": "https://example.com/image1.jpg",
          "photo_reference": "ATplDJYc...",
          "order": 1
        }
      ]
    }
  ]
}
```

### 観光地詳細の取得
`GET /api/travel-spots/{id}`

指定したIDの観光地の詳細情報を取得します。

**パラメータ**:
- `id` (integer, required): 観光地のID

**レスポンス例 (成功時)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "東京タワー",
    "description": "東京のシンボルとして知られる電波塔",
    "latitude": 35.6586,
    "longitude": 139.7454,
    "address": "東京都港区芝公園4丁目2-8",
    "place_id": "ChIJCewJkL2LGGAR3Qmk0vCTGkg",
    "category": "観光地",
    "prefecture_id": 13,
    "spot_images": []
  }
}
```

**レスポンス例 (404エラー)**:
```json
{
  "success": false,
  "message": "Travel spot not found"
}
```

### 都道府県別観光地の取得
`GET /api/travel-spots/prefecture/{prefectureId}`

指定した都道府県の観光地一覧を取得します。

**パラメータ**:
- `prefectureId` (integer, required): 都道府県ID

---

## 都道府県 / Prefectures

### 都道府県一覧の取得
`GET /api/prefectures`

すべての都道府県情報を取得します。

### 利用可能な都道府県の取得
`GET /api/prefectures/available`

観光地が登録されている都道府県のみを取得します。

### 注目の都道府県の取得
`GET /api/prefectures/featured`

注目の都道府県情報を取得します。

### 地域別都道府県の取得
`GET /api/prefectures/by-region`

都道府県を地域別にグループ化して取得します。

**レスポンス例**:
```json
{
  "北海道・東北": [
    {"id": 1, "name": "北海道"},
    {"id": 2, "name": "青森県"}
  ],
  "関東": [
    {"id": 13, "name": "東京都"},
    {"id": 14, "name": "神奈川県"}
  ]
}
```

### 都道府県詳細の取得
`GET /api/prefectures/{id}`

**パラメータ**:
- `id` (integer, required): 都道府県ID

### 都道府県の観光地一覧
`GET /api/prefectures/{id}/spots`

指定した都道府県に属する観光地一覧を取得します。

### 都道府県名で観光地検索
`GET /api/prefectures/name/{name}/spots`

**パラメータ**:
- `name` (string, required): 都道府県名（例: "東京都"）

---

## 人気スポット / Popular Spots

### 人気観光地の取得
`GET /api/popular-spots`

人気の観光地情報を取得します（キャッシュ対応）。

### キャッシュクリア
`DELETE /api/popular-spots/cache`

人気観光地のキャッシュをクリアします。

**認証必須**: Bearer Token（管理者権限）

---

## イベント / Events

### イベント一覧の取得
`GET /api/events`

**クエリパラメータ**:
- `prefecture` (string, optional): 都道府県でフィルタ
- `tag` (string, optional): タグでフィルタ
- `month` (integer, optional): 月でフィルタ

### イベント詳細の取得
`GET /api/events/{id}`

**パラメータ**:
- `id` (integer, required): イベントID

---

## 音声ガイド / Audio Guide

### 利用可能な音声一覧を取得
`GET /api/audio-guide/voices`

Amazon Pollyで利用可能な日本語音声の一覧を取得します。

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "voices": [
      {
        "id": "Takumi",
        "name": "Takumi",
        "gender": "Male",
        "language_code": "ja-JP",
        "supported_engines": ["neural", "standard"]
      },
      {
        "id": "Tomoko",
        "name": "Tomoko",
        "gender": "Female",
        "language_code": "ja-JP",
        "supported_engines": ["neural", "standard"]
      }
    ],
    "recommended": ["Takumi", "Tomoko", "Mizuki", "Kazuha"]
  }
}
```

### 観光地音声ガイドの生成
`POST /api/audio-guide/tourist-spot`

観光地の音声ガイドを生成します。

**リクエストボディ**:
```json
{
  "spot_id": 1,
  "voice_id": "Takumi",
  "regenerate": false
}
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "audio_url": "http://localhost:8000/storage/audio/polly/polly_audio_abc123.mp3",
    "cache_hit": false,
    "text_length": 150,
    "filename": "audio/polly/polly_audio_abc123.mp3"
  }
}
```

### キャッシュクリア
`DELETE /api/audio-guide/cache`

音声ガイドのキャッシュをクリアします。

**認証必須**: Bearer Token（管理者権限）

---

## エラーレスポンス

すべてのAPIエンドポイントは以下の形式でエラーを返します：

```json
{
  "success": false,
  "message": "エラーメッセージ",
  "errors": {
    "field_name": ["エラー詳細"]
  }
}
```

### HTTPステータスコード

- `200 OK`: 成功
- `201 Created`: リソース作成成功
- `400 Bad Request`: リクエストパラメータエラー
- `401 Unauthorized`: 認証エラー
- `403 Forbidden`: 権限エラー
- `404 Not Found`: リソースが見つからない
- `422 Unprocessable Entity`: バリデーションエラー
- `500 Internal Server Error`: サーバーエラー

---

## 開発者向け情報

### Postman Collection
[Download Postman Collection](/docs/collection.json)

### OpenAPI Specification
[Download OpenAPI Spec](/docs/openapi.yaml)

### API Documentation (Interactive)
開発環境: http://localhost:8000/docs

本番環境: https://travel-voice-production.up.railway.app/docs

---

## 更新履歴

- 2025-07-27: 初版作成（Laravel Scribeによる自動生成）
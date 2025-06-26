# API仕様

本アプリケーションのバックエンドはLaravelで構築されており、RESTful APIを提供します。フロントエンドはこれらのAPIを介してデータと連携します。

## 1. 認証API

### 1.1. ユーザー登録
- **エンドポイント:** `POST /api/register`
- **説明:** 新規ユーザーを登録します。
- **リクエストボディ:**
    ```json
    {
        "name": "string",
        "email": "string",
        "password": "string",
        "password_confirmation": "string"
    }
    ```
- **レスポンス (成功: 201 Created):**
    ```json
    {
        "user": {
            "id": 1,
            "name": "Test User",
            "email": "test@example.com",
            "email_verified_at": null,
            "created_at": "2023-01-01T00:00:00.000000Z",
            "updated_at": "2023-01-01T00:00:00.000000Z"
        },
        "token": "string (Sanctum API Token)"
    }
    ```
- **レスポンス (エラー: 422 Unprocessable Entity):**
    ```json
    {
        "message": "The given data was invalid.",
        "errors": {
            "email": ["The email has already been taken."]
        }
    }
    ```

### 1.2. ユーザーログイン
- **エンドポイント:** `POST /api/login`
- **説明:** ユーザーを認証し、APIトークンを発行します。
- **リクエストボディ:**
    ```json
    {
        "email": "string",
        "password": "string"
    }
    ```
- **レスポンス (成功: 200 OK):**
    ```json
    {
        "user": {
            "id": 1,
            "name": "Test User",
            "email": "test@example.com",
            "email_verified_at": null,
            "created_at": "2023-01-01T00:00:00.000000Z",
            "updated_at": "2023-01-01T00:00:00.000000Z"
        },
        "token": "string (Sanctum API Token)"
    }
    ```
- **レスポンス (エラー: 401 Unauthorized):**
    ```json
    {
        "message": "認証情報が正しくありません。"
    }
    ```

### 1.3. ユーザーログアウト
- **エンドポイント:** `POST /api/logout`
- **説明:** 認証済みのユーザーのAPIトークンを削除し、ログアウトします。
- **認証:** Sanctumトークン (Bearer Token)
- **リクエストボディ:** なし
- **レスポンス (成功: 200 OK):**
    ```json
    {
        "message": "Logged out successfully"
    }
    ```

### 1.4. 認証済みユーザー情報取得
- **エンドポイント:** `GET /api/user`
- **説明:** 現在認証されているユーザーの情報を取得します。
- **認証:** Sanctumトークン (Bearer Token)
- **リクエストボディ:** なし
- **レスポンス (成功: 200 OK):**
    ```json
    {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "email_verified_at": null,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
    ```
- **レスポンス (エラー: 401 Unauthorized):**
    ```json
    {
        "message": "Unauthenticated."
    }
    ```

## 2. 観光地API

### 2.1. 観光地一覧取得
- **エンドポイント:** `GET /api/tourist-spots`
- **説明:** 観光地の一覧を取得します。フィルタリング、ページネーション、ソートなどのオプションが将来的に追加される可能性があります。
- **クエリパラメータ:**
    - `prefecture`: string (都道府県名でフィルタリング)
    - `category`: string (カテゴリ名でフィルタリング)
    - `search`: string (キーワードで観光地名、説明、概要などを検索)
- **レスポンス (成功: 200 OK):**
    ```json
    [
        {
            "id": 1,
            "name": "東京スカイツリー",
            "description": "高さ634mの世界最高クラスの電波塔。",
            "overview": "東京スカイツリーは、東京都墨田区押上にある電波塔で、2012年に開業しました。",
            "highlights": ["展望デッキ", "スカイツリータウン"],
            "history": "2008年に着工し、2012年に完成。",
            "prefecture": "東京都",
            "city": "墨田区",
            "address": "東京都墨田区押上1-1-2",
            "latitude": 35.7101,
            "longitude": 139.8107,
            "category": "展望台",
            "images": ["url1", "url2"],
            "access_info": "電車でのアクセスが便利です。",
            "public_transport": [
                {"route": "東武スカイツリーライン", "station": "とうきょうスカイツリー駅", "time": "徒歩1分"}
            ],
            "car_access": [
                {"from": "首都高速6号向島線", "exit": "駒形出口", "time": "約5分"}
            ],
            "parking_info": "周辺に有料駐車場あり。",
            "walking_info": "押上駅から徒歩5分。",
            "website": "https://www.tokyo-skytree.jp/",
            "phone": "0570-55-0634",
            "opening_hours": "8:00-22:00",
            "admission_fee": "大人2100円",
            "is_active": true,
            "created_at": "2023-01-01T00:00:00.000000Z",
            "updated_at": "2023-01-01T00:00:00.000000Z"
        }
        // ... 他の観光地
    ]
    ```

### 2.2. 特定の観光地情報取得
- **エンドポイント:** `GET /api/tourist-spots/{id}`
- **説明:** 指定されたIDの観光地情報を取得します。
- **パスパラメータ:**
    - `id`: integer (観光地ID)
- **レスポンス (成功: 200 OK):**
    ```json
    {
        "id": 1,
        "name": "東京スカイツリー",
        "description": "高さ634mの世界最高クラスの電波塔。",
        "overview": "東京スカイツリーは、東京都墨田区押上にある電波塔で、2012年に開業しました。",
        "highlights": ["展望デッキ", "スカイツリータウン"],
        "history": "2008年に着工し、2012年に完成。",
        "prefecture": "東京都",
        "city": "墨田区",
        "address": "東京都墨田区押上1-1-2",
        "latitude": 35.7101,
        "longitude": 139.8107,
        "category": "展望台",
        "images": ["url1", "url2"],
        "access_info": "電車でのアクセスが便利です。",
        "public_transport": [
            {"route": "東武スカイツリーライン", "station": "とうきょうスカイツリー駅", "time": "徒歩1分"}
        ],
        "car_access": [
            {"from": "首都高速6号向島線", "exit": "駒形出口", "time": "約5分"}
        ],
        "parking_info": "周辺に有料駐車場あり。",
        "walking_info": "押上駅から徒歩5分。",
        "website": "https://www.tokyo-skytree.jp/",
        "phone": "0570-55-0634",
        "opening_hours": "8:00-22:00",
        "admission_fee": "大人2100円",
        "is_active": true,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
    ```
- **レスポンス (エラー: 404 Not Found):**
    ```json
    {
        "message": "No query results for model [App\\Models\\TouristSpot] {id}"
    }
    ```

### 2.3. 都道府県別観光地一覧取得
- **エンドポイント:** `GET /api/tourist-spots/prefecture/{prefecture}`
- **説明:** 指定された都道府県の観光地一覧を取得します。
- **パスパラメータ:**
    - `prefecture`: string (都道府県名)
- **レスポンス (成功: 200 OK):**
    ```json
    [
        {
            "id": 1,
            "name": "東京スカイツリー",
            "description": "高さ634mの世界最高クラスの電波塔。",
            "prefecture": "東京都",
            "category": "展望台",
            "is_active": true,
            "created_at": "2023-01-01T00:00:00.000000Z",
            "updated_at": "2023-01-01T00:00:00.000000Z"
        }
        // ... 他の東京都の観光地
    ]
    ```

## 3. ガイドAPI

### 3.1. 特定のガイド情報取得
- **エンドポイント:** `GET /api/guides/{guide}`
- **説明:** 指定されたIDのガイド情報を取得します。音声ガイド情報も含まれます。
- **パスパラメータ:**
    - `guide`: integer (ガイドID)
- **レスポンス (成功: 200 OK):**
    ```json
    {
        "id": 1,
        "tourist_spot_id": 1,
        "title": "東京スカイツリーの歴史と魅力",
        "content": "東京スカイツリーは、日本の技術の粋を集めて建設された電波塔です。",
        "type": "audio",
        "duration_minutes": 5,
        "order": 1,
        "highlights": ["建設技術", "展望"],
        "is_active": true,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z",
        "audio_guides": [
            {
                "id": 101,
                "guide_id": 1,
                "audio_file_path": "/path/to/audio/skytree_ja.mp3",
                "audio_file_url": "https://example.com/audio/skytree_ja.mp3",
                "duration_seconds": 300,
                "format": "mp3",
                "file_size": 5000000,
                "voice_actor": "山田太郎",
                "language": "ja",
                "is_active": true,
                "created_at": "2023-01-01T00:00:00.000000Z",
                "updated_at": "2023-01-01T00:00:00.000000Z"
            }
        ]
    }
    ```
- **レスポンス (エラー: 404 Not Found):**
    ```json
    {
        "message": "No query results for model [App\\Models\\Guide] {guide}"
    }
    ```

## 4. AIチャットAPI

### 4.1. AIチャットメッセージ送信
- **エンドポイント:** `POST /api/chat`
- **説明:** AIエージェントにメッセージを送信し、応答を取得します。
- **リクエストボディ:**
    ```json
    {
        "message": "string",
        "conversation": [
            {"role": "user", "content": "string"},
            {"role": "assistant", "content": "string"}
        ]
    }
    ```
    - `message`: ユーザーの現在のメッセージ。
    - `conversation`: 過去の会話履歴（オプション）。
- **レスポンス (成功: 200 OK):**
    ```json
    {
        "content": "string (AIの応答メッセージ)"
    }
    ```
- **レスポンス (エラー: 500 Internal Server Error など):**
    ```json
    {
        "error": "string",
        "details": "string (オプション)"
    }
    ```
# アーキテクチャ設計書

観光地音声ガイドアプリのシステムアーキテクチャについて説明します。

## 🏛️ システム概要

### アーキテクチャパターン

本アプリケーションは以下のアーキテクチャパターンを採用しています：

- **Frontend**: SPA (Single Page Application) - Nuxt.js
- **Backend**: API-First - Laravel (RESTful API)
- **Database**: リレーショナルデータベース - MySQL
- **Architecture**: クリーンアーキテクチャの軽量版

## 🔧 技術選定理由

### フロントエンド: Nuxt.js + TypeScript

**選定理由:**
- **SEO対応**: SSR/SSGによる検索エンジン最適化
- **TypeScript**: 型安全性による開発効率向上
- **Vue.js 3**: Composition APIによるコンポーネント設計
- **自動ルーティング**: ファイルベースのルーティング
- **豊富なエコシステム**: TailwindCSS、Pinia等の統合

**メリット:**
- 開発効率が高い
- パフォーマンスが優秀
- モバイルフレンドリー
- PWA対応が容易

### バックエンド: Laravel + Sanctum

**選定理由:**
- **高い生産性**: Eloquent ORM、Artisanコマンド
- **セキュリティ**: Laravel Sanctumによる認証
- **豊富な機能**: ミドルウェア、バリデーション、シーダー
- **大規模対応**: スケーラビリティと保守性

**メリット:**
- 迅速な開発が可能
- セキュアな認証システム
- 充実したドキュメント
- 豊富なサードパーティライブラリ

### データベース: MySQL

**選定理由:**
- **信頼性**: 長年の実績と安定性
- **性能**: 高いパフォーマンス
- **互換性**: 多くのホスティングサービスで対応
- **Laravel統合**: Eloquent ORMとの親和性

## 🏗️ システム構成図

```
┌─────────────────────────────────────────────────────────────┐
│                        Client Side                          │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │   Mobile    │  │   Tablet    │  │   Desktop   │          │
│  │   Browser   │  │   Browser   │  │   Browser   │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ HTTPS
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Nuxt.js Frontend                      │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │    Pages    │  │ Components  │  │ Composables │          │
│  │             │  │             │  │             │          │
│  │ - index     │  │ - UI Base   │  │ - useAuth   │          │
│  │ - login     │  │ - Layout    │  │ - useApi    │          │
│  │ - spots     │  │ - Maps      │  │             │          │
│  │             │  │ - Audio     │  │             │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
│                              │                              │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │                 TailwindCSS                             │ │
│  └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ HTTP/API
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Backend                         │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │   Routes    │  │ Controllers │  │ Middleware  │          │
│  │             │  │             │  │             │          │
│  │ - api.php   │  │ - Auth      │  │ - Sanctum   │          │
│  │             │  │ - Tourist   │  │ - CORS      │          │
│  │             │  │ - Guide     │  │             │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
│                              │                              │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │   Models    │  │ Migrations  │  │   Seeders   │          │
│  │             │  │             │  │             │          │
│  │ - User      │  │ - Tables    │  │ - Sample    │          │
│  │ - Spot      │  │ - Indexes   │  │   Data      │          │
│  │ - Guide     │  │             │  │             │          │
│  │ - Audio     │  │             │  │             │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ SQL
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                       MySQL Database                       │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │    users    │  │travel_spots│  │   guides    │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
│                              │                              │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │audio_guides │  │personal_    │  │   cache     │          │
│  │             │  │access_tokens│  │             │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
```

## 📊 データモデル設計

### ER図

```
┌─────────────────┐
│      users      │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email (UNIQUE)  │
│ password        │
│ created_at      │
│ updated_at      │
└─────────────────┘
         │
         │ 1:N (future)
         ▼
┌─────────────────┐    1:N    ┌─────────────────┐
│  travel_spots  │◄─────────►│     guides      │
├─────────────────┤           ├─────────────────┤
│ id (PK)         │           │ id (PK)         │
│ name            │           │ travel_spot_id │
│ description     │           │ title           │
│ prefecture      │           │ content         │
│ city            │           │ type            │
│ address         │           │ duration_min    │
│ latitude        │           │ order           │
│ longitude       │           │ highlights      │
│ category        │           │ is_active       │
│ images (JSON)   │           │ created_at      │
│ access_info     │           │ updated_at      │
│ website         │           └─────────────────┘
│ phone           │                    │
│ opening_hours   │                    │ 1:N
│ admission_fee   │                    ▼
│ is_active       │           ┌─────────────────┐
│ created_at      │           │  audio_guides   │
│ updated_at      │           ├─────────────────┤
└─────────────────┘           │ id (PK)         │
                              │ guide_id (FK)   │
                              │ audio_file_path │
                              │ audio_file_url  │
                              │ duration_sec    │
                              │ format          │
                              │ file_size       │
                              │ voice_actor     │
                              │ language        │
                              │ is_active       │
                              │ created_at      │
                              │ updated_at      │
                              └─────────────────┘
```

### リレーション設計

1. **TravelSpot ↔ Guide**: 1対多
   - 1つの観光スポットに複数のガイドが存在
   - CASCADE DELETE設定

2. **Guide ↔ AudioGuide**: 1対多
   - 1つのガイドに複数の音声ファイル（言語別など）
   - CASCADE DELETE設定

3. **User ↔ Favorites**: 1対多（将来機能）
   - ユーザーのお気に入り機能

## 🔐 セキュリティ設計

### 認証・認可

**Laravel Sanctum**
- SPA認証に最適化
- トークンベース認証
- CSRF保護
- XSS対策

**実装方針:**
```php
// 認証が必要なルート
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    // 将来のプライベート機能
});

// 公開API
Route::get('/tourist-spots', [TravelSpotController::class, 'index']);
```

### データ保護

1. **パスワードハッシュ化**: bcrypt
2. **SQLインジェクション対策**: Eloquent ORM
3. **XSS対策**: エスケープ処理
4. **CSRF対策**: トークン検証

## 🚀 パフォーマンス設計

### フロントエンド最適化

**Nuxt.js最適化:**
- Code Splitting: 自動的なチャンク分割
- Lazy Loading: 画像・コンポーネントの遅延読み込み
- Tree Shaking: 未使用コードの除去
- Compression: Gzip/Brotli圧縮

**実装例:**
```vue
<!-- 遅延コンポーネント読み込み -->
<script setup>
const AudioPlayer = defineAsyncComponent(() => 
  import('~/components/AudioPlayer.vue')
)
</script>
```

### バックエンド最適化

**Laravel最適化:**
- Eager Loading: N+1問題の解決
- Database Indexing: 検索性能向上
- Query Optimization: 効率的なクエリ
- Caching: Redis/Memcached（将来）

**実装例:**
```php
// Eager Loading
$spots = TravelSpot::with(['activeGuides.activeAudioGuides'])
    ->where('prefecture', $prefecture)
    ->get();

// Database Index
Schema::table('travel_spots', function (Blueprint $table) {
    $table->index('prefecture');
    $table->index('category');
    $table->index(['prefecture', 'is_active']);
});
```

## 📱 レスポンシブ対応

### ブレークポイント設計

TailwindCSSの標準ブレークポイント:
- `sm`: 640px以上
- `md`: 768px以上  
- `lg`: 1024px以上
- `xl`: 1280px以上

**実装戦略:**
- Mobile First Design
- Progressive Enhancement
- Touch-friendly UI

## 🔄 状態管理

### フロントエンド状態管理

**Pinia Store:**
```typescript
// stores/auth.ts
export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = useCookie('auth-token')
  
  const login = async (credentials: LoginCredentials) => {
    // ログイン処理
  }
  
  const logout = async () => {
    // ログアウト処理
  }
  
  return { user, login, logout }
})
```

### グローバル状態vs局所状態

**グローバル状態 (Pinia):**
- ユーザー認証情報
- アプリケーション設定
- 共有データ

**局所状態 (ref/reactive):**
- フォーム入力値
- UI状態（モーダル表示など）
- ページ固有のデータ

## 🧪 テスト戦略

### フロントエンドテスト

**計画中のテスト:**
- Unit Tests: Vitest
- Component Tests: Vue Test Utils
- E2E Tests: Playwright

### バックエンドテスト

**計画中のテスト:**
- Unit Tests: PHPUnit
- Feature Tests: Laravel Testing
- API Tests: Postman/Newman

## 🚀 デプロイ戦略

### 開発環境

**Docker Compose:**
- フロントエンド: Node.js 18
- バックエンド: PHP 8.2 + Apache
- データベース: MySQL 8.0
- 管理ツール: phpMyAdmin

### 本番環境設計

**推奨構成:**
- **Frontend**: Vercel/Netlify (JAMstack)
- **Backend**: AWS EC2/DigitalOcean
- **Database**: AWS RDS/Managed MySQL
- **CDN**: CloudFlare
- **Storage**: AWS S3 (音声ファイル)

## 📈 スケーラビリティ

### 水平スケーリング対応

**フロントエンド:**
- CDN配信による負荷分散
- Static Generation活用

**バックエンド:**
- ロードバランサー導入
- データベースレプリケーション
- Redis Cluster（キャッシュ）

### パフォーマンス監視

**監視項目:**
- レスポンス時間
- データベースクエリ性能
- メモリ使用量
- CDNヒット率

## 🔮 将来の拡張計画

### 機能拡張

1. **多言語対応**
   - i18n実装
   - 音声ガイド多言語化

2. **リアルタイム機能**
   - WebSocket通信
   - 位置情報連動

3. **AI機能**
   - 音声認識
   - 自動翻訳
   - パーソナライゼーション

### 技術的拡張

1. **マイクロサービス化**
   - サービス分離
   - API Gateway導入

2. **データ分析基盤**
   - BigQuery/DataWarehouse
   - ユーザー行動分析

3. **モバイルアプリ**
   - React Native
   - Flutter

この設計により、スケーラブルで保守性の高いアプリケーションを実現しています。
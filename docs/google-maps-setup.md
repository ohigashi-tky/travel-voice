# Google Maps API セットアップガイド

## 概要

この観光ガイドアプリでは、美しく正確な日本地図の表示にGoogle Maps APIを使用しています。APIキーが設定されていない場合は、自動的にSVG地図にフォールバックします。

## Google Maps API キーの取得手順

### 1. Google Cloud Consoleにアクセス
https://console.cloud.google.com/ にアクセスし、Googleアカウントでログインします。

### 2. プロジェクトを作成または選択
- 新しいプロジェクトを作成するか、既存のプロジェクトを選択します
- プロジェクト名例: "Japan Tourism Guide"

### 3. Maps JavaScript APIを有効化
1. ナビゲーションメニューから「API とサービス」→「ライブラリ」を選択
2. "Maps JavaScript API" を検索
3. "Maps JavaScript API" を選択し、「有効にする」をクリック

### 4. APIキーの作成
1. 「API とサービス」→「認証情報」を選択
2. 「認証情報を作成」→「APIキー」をクリック
3. 作成されたAPIキーをコピー

### 5. APIキーの制限設定（推奨）
セキュリティのため、以下の制限を設定することを強く推奨します：

#### アプリケーションの制限
- 「HTTPリファラー（ウェブサイト）」を選択
- 許可するリファラーを追加：
  - `http://localhost:3000/*` (開発環境)
  - `https://yourdomain.com/*` (本番環境)

#### API制限
- 「キーを制限」を選択
- 「Maps JavaScript API」のみを選択

## 環境変数の設定

### 開発環境
1. `/frontend/.env` ファイルを作成（既に存在する場合は編集）
2. 以下を追加：
```bash
NUXT_PUBLIC_GOOGLE_MAPS_API_KEY=YOUR_ACTUAL_API_KEY_HERE
```

### 本番環境
本番環境では、環境変数 `NUXT_PUBLIC_GOOGLE_MAPS_API_KEY` を設定してください。

## 料金について

Google Maps APIは使用量に応じて課金されます：

- **Maps JavaScript API**: 1,000回のマップロードごとに $7.00
- **無料枠**: 月間 $200 のクレジット（約28,000回のマップロード）

詳細: https://cloud.google.com/maps-platform/pricing

## 実装詳細

### 使用コンポーネント
- `GoogleJapanMap.vue` - Google Maps APIを使用した地図コンポーネント
- `UltraPreciseJapanMap.vue` - SVG フォールバック地図
- `JapanMapWrapper.vue` - 自動切り替えラッパー

### 地図データ
- GeoJSON形式の都道府県境界データを使用
- `useJapanGeoData.ts` composableで管理
- 実際の正確な境界データは外部データソースから取得可能

### 推奨データソース
より正確な境界データが必要な場合：
- [dataofjapan/land](https://github.com/dataofjapan/land) (MIT License)
- [国土地理院 行政区域データ](https://www.gsi.go.jp/)
- [Natural Earth データ](https://www.naturalearthdata.com/)

## トラブルシューティング

### APIキーが無効な場合
- ブラウザコンソールに「Google Maps API未設定」メッセージが表示される
- 自動的にSVG地図にフォールバック

### APIキーが動作しない場合
1. APIキーが正しくコピーされているか確認
2. Maps JavaScript APIが有効化されているか確認
3. APIキーの制限設定を確認
4. 請求アカウントが設定されているか確認

### 開発者向けデバッグ
ブラウザコンソールで以下の情報を確認できます：
```
🗾 Japan Map Component Status:
- Google Maps API Key Valid: true/false
- Using: Google Maps/SVG Map
```

## セキュリティのベストプラクティス

1. **APIキーの制限**: 必ず適切なリファラー制限を設定
2. **使用量監視**: Google Cloud Consoleで使用量を定期的に確認
3. **環境変数**: APIキーは環境変数で管理し、コードにハードコーディングしない
4. **アラート設定**: 使用量が一定量を超えた場合のアラートを設定

## 観光地詳細ページの地図機能

### 新機能: 詳細地図表示
観光地詳細ページ（`/spots/[id]`）では、以下の地図機能が提供されます：

#### 表示内容
- **観光地の正確な位置**: 赤色マーカーで表示
- **周辺の駅・交通機関**: 青色マーカーで表示（半径1km以内）
- **近隣の観光スポット**: 緑色マーカーで表示（半径2km以内）
- **詳細な道路情報**: 適切なズームレベル（16）で表示

#### 技術仕様
- **使用API**: Maps JavaScript API + Places API
- **座標データ**: 主要観光地の座標を内蔵
- **検索機能**: 周辺施設の自動検索
- **レスポンシブ対応**: モバイル・デスクトップ両対応

#### APIキー設定
詳細地図機能には以下の設定が必要です：
```bash
GOOGLE_MAPS_API_KEY=AIzaSyDBTzeSepixQFP2y2pQcNciOSj8kYlDzh4
```

#### 必要なAPI有効化
- Maps JavaScript API
- Places API

### Docker環境での設定
```yaml
# docker-compose.yml
environment:
  - NUXT_PUBLIC_GOOGLE_MAPS_API_KEY=${GOOGLE_MAPS_API_KEY}
```

### 内蔵座標データ
以下の観光地の座標が事前設定されています：
- 東京都: 東京スカイツリー、浅草寺、明治神宮
- 大阪府: 大阪城、通天閣、海遊館
- 京都府: 清水寺、金閣寺、伏見稲荷大社
- 北海道: 札幌時計台、函館山、小樽運河
- 愛知県: 名古屋城、熱田神宮、トヨタ産業技術記念館
- 広島県: 原爆ドーム、厳島神社、広島城
- 福岡県: 太宰府天満宮、福岡城跡、博多駅

## よくある質問

**Q: APIキー無しでも使用できますか？**
A: はい、自動的にSVG地図にフォールバックします。

**Q: 料金はかかりますか？**
A: Google Maps APIは使用量に応じて課金されますが、月間$200の無料枠があります。

**Q: 他の地図サービスは使用できますか？**
A: はい、OpenStreetMap、Mapbox等への置き換えも可能です。

**Q: オフラインでも動作しますか？**
A: Google Maps部分はオフラインでは動作しませんが、SVG地図は動作します。

**Q: 詳細地図が表示されない場合は？**
A: 1) APIキーが正しく設定されているか確認、2) Places APIが有効化されているか確認、3) ブラウザコンソールでエラーを確認してください。
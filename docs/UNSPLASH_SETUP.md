# Unsplash API設定ガイド

## 概要
Travel Voiceアプリでは、観光地の高品質な画像を表示するためにUnsplash APIを使用しています。
現在はフォールバック画像を使用していますが、より多様で高品質な画像を取得するために実際のAPIキーを設定することができます。

## Unsplash APIキーの取得方法

### 1. Unsplash Developersアカウントの作成
1. [Unsplash Developers](https://unsplash.com/developers) にアクセス
2. 「Register as a developer」をクリック
3. アカウントを作成またはログイン

### 2. アプリケーションの登録
1. ダッシュボードで「New Application」をクリック
2. 利用規約に同意
3. アプリケーション情報を入力：
   - **Application name**: Travel Voice
   - **Description**: Tourist spot guide application with audio guides
   - **Website URL**: https://your-domain.com（開発中はlocalhost:3000でも可）

### 3. APIキーの取得
1. 作成したアプリケーションを開く
2. 「Access Key」をコピー

### 4. 環境変数の設定
`.env`ファイルを更新：

```bash
# 他の環境変数...
UNSPLASH_ACCESS_KEY=your_actual_access_key_here
```

## 利用制限
- **Demo（無料）**: 50リクエスト/時間
- **Production**: 5,000リクエスト/時間（申請後）

## 利用規約
Unsplash APIを使用する場合、以下の利用規約を遵守する必要があります：

1. **クレジット表記**: 写真の作者名とUnsplashのクレジット表記
2. **UTM追跡**: ダウンロード時にUnsplashのエンドポイントを呼び出し
3. **適切な使用**: 写真の改変や商標的使用の禁止

## 現在の実装状況
- ✅ サーバーサイドAPI実装済み
- ✅ フォールバック画像システム
- ✅ エラーハンドリング
- ✅ キャッシュ機能
- ⏳ 実際のAPIキー設定待ち

## フォールバック画像
APIキーが設定されていない場合、以下の高品質なUnsplash画像を直接使用：

- 東京スカイツリー: 現代的な電波塔の美しい写真
- 浅草寺: 伝統的な寺院の雰囲気ある写真
- 大阪城: 歴史ある城郭の威厳ある写真
- その他の観光地: 適切にキュレーションされた画像

## 技術詳細
- **API**: Unsplash Search API
- **キャッシュ**: メモリベースの画像URLキャッシュ
- **画像サイズ**: regular (800x600) を標準使用
- **品質**: q=80 で高品質を維持
- **フォーマット**: WebP対応ブラウザで自動最適化
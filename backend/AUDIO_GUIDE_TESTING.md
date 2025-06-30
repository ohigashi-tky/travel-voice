# 音声ガイド品質テストドキュメント

## 概要

このドキュメントでは、全ての観光地の音声ガイドが汎用テンプレートではなく、詳細で質の高いコンテンツを提供しているかを検証するテストの実行方法を説明します。

## テストの目的

1. **汎用テンプレート検出**: 「多くの興味深いエピソードがあります」等の汎用フレーズを使用している観光地を特定
2. **品質基準確認**: 各観光地が最低限の品質基準（文字数、エピソード数、具体性等）を満たしているかチェック
3. **データ整合性検証**: 音声ガイドデータの構造と型が正しいかを確認
4. **既存データ保護**: 既存の詳細な音声ガイドが正しく保持されているかを確認

## テスト対象

以下の観光地IDが対象となります：

### 東京都 (1-99)
- 1: 東京スカイツリー
- 2: 浅草寺
- 3: 明治神宮
- 4: 銀座
- 5: 上野公園
- 6: 渋谷スクランブル交差点

### 大阪府 (100-199)
- 101: 大阪城
- 102: 通天閣
- 103: 海遊館
- 104: 道頓堀
- 105: 新世界
- 106: 大阪駅・梅田

### 京都府 (200-299)
- 201: 清水寺
- 202: 金閣寺
- 203: 伏見稲荷大社

### 北海道 (300-399)
- 301: 札幌時計台
- 302: 函館山
- 303: 小樽運河

### 愛知県 (400-499)
- 401: 名古屋城
- 402: 熱田神宮
- 403: トヨタ産業技術記念館

### 福岡県 (500-599)
- 501: 太宰府天満宮
- 502: 福岡城跡
- 503: 博多駅

### 広島県 (600-699)
- 601: 原爆ドーム
- 602: 厳島神宮
- 603: 広島城

### 福島県 (700-799)
- 701: 会津若松城
- 702: 五色沼

### 愛媛県 (800-899)
- 801: 道後温泉
- 802: 松山城

### 埼玉県 (900-999)
- 901: 川越
- 902: 秩父神社
- 903: 長瀞

### 新潟県 (1000-1099)
- 1001: 佐渡島
- 1002: 弥彦神社
- 1003: 苗場スキー場

### 山口県 (1100-1199)
- 1101: 錦帯橋
- 1102: 秋芳洞
- 1103: 萩城下町

### 徳島県 (1200-1299)
- 1201: 鳴門の渦潮
- 1202: 阿波踊り会館
- 1203: 大塚国際美術館

### 鹿児島県 (1300-1399)
- 1301: 桜島
- 1302: 屋久島
- 1303: 霧島神宮

## テスト実行方法

### 1. PHPUnitテストの実行

#### 全体テストの実行
```bash
# バックエンドディレクトリに移動
cd /Users/takuya/src/guide-app/backend

# 全ての音声ガイドテストを実行
./vendor/bin/phpunit tests/Feature/AudioGuideQualityTest.php

# ユニットテストも含めて実行
./vendor/bin/phpunit tests/Unit/AudioGuideDataTest.php

# 全テストを一括実行
./vendor/bin/phpunit --testsuite=Feature,Unit
```

#### 特定のテストのみ実行
```bash
# 汎用テンプレート検出テストのみ
./vendor/bin/phpunit --filter test_all_tourist_spots_have_non_generic_audio_guides

# 川越の詳細テストのみ
./vendor/bin/phpunit --filter test_specific_tourist_spot_quality

# 既存データ保護テストのみ
./vendor/bin/phpunit --filter test_existing_detailed_spots_are_preserved

# パフォーマンステストのみ
./vendor/bin/phpunit --filter test_audio_guide_generation_performance
```

#### 詳細出力での実行
```bash
# 詳細な実行結果を表示
./vendor/bin/phpunit tests/Feature/AudioGuideQualityTest.php --verbose

# デバッグ情報も含めて実行
./vendor/bin/phpunit tests/Feature/AudioGuideQualityTest.php --debug
```

### 2. Artisanコマンドの実行

#### 全観光地の品質チェック
```bash
# 基本的な品質チェック
php artisan audio-guide:quality-check

# 詳細な出力付きで実行
php artisan audio-guide:quality-check --verbose

# レポートファイルも出力
php artisan audio-guide:quality-check --export-report
```

#### 特定の観光地のみチェック
```bash
# 川越（ID: 901）のみチェック
php artisan audio-guide:quality-check --spot-id=901

# 東京スカイツリー（ID: 1）のみチェック
php artisan audio-guide:quality-check --spot-id=1 --verbose
```

### 3. CI/CDでの自動実行

#### GitHub Actionsでの設定例
```yaml
name: Audio Guide Quality Tests

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  audio-guide-tests:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install dependencies
      run: |
        cd backend
        composer install --no-dev --optimize-autoloader
        
    - name: Run Audio Guide Quality Tests
      run: |
        cd backend
        ./vendor/bin/phpunit tests/Feature/AudioGuideQualityTest.php
        php artisan audio-guide:quality-check --export-report
        
    - name: Upload test reports
      uses: actions/upload-artifact@v3
      if: always()
      with:
        name: audio-guide-reports
        path: backend/storage/logs/audio_guide_quality_report*.json
```

## 品質基準

### 必須要件
1. **最小文字数**: 200文字以上
2. **エピソード数**: 2個以上の具体的なエピソード
3. **具体的な歴史情報**: 年代、人名、出来事等の具体的な情報
4. **具体的な見どころ**: 汎用的でない具体的な見どころ
5. **数値情報**: 高さ、年代、規模等の具体的な数値

### 禁止パターン
以下のフレーズが含まれている場合は汎用テンプレートと判定：
- 「多くの興味深いエピソードがあります」
- 「訪れる人々に感動と発見を提供し続けています」
- 「歴史的価値、文化的意義、美しい景観」
- 「観光地#[数字]」
- 「日本を代表する素晴らしい観光地です。歴史と文化が息づく魅力的な場所で、多くの訪問者に愛され続けています」

## テスト結果の確認

### 成功時の出力例
```
✅ テスト成功: 79箇所の観光地全てで詳細な音声ガイドが確認されました

📊 チェック結果サマリー
┌──────────┬─────┐
│ 項目     │ 値  │
├──────────┼─────┤
│ 総観光地数 │ 79  │
│ 品質OK    │ 79  │
│ 要改善    │ 0   │
│ 品質スコア │ 100.0% │
└──────────┴─────┘

✅ 全ての観光地が品質基準を満たしています！
```

### 失敗時の出力例
```
❌ 要改善の観光地一覧:
┌────┬──────────┬─────────────────────────────────┐
│ ID │ 観光地名   │ 問題                            │
├────┼──────────┼─────────────────────────────────┤
│ 901│ 川越      │ 汎用テンプレート検出: 多くの興味深いエピソード │
│ 1001│ 佐渡島    │ 文字数不足: 150文字, エピソード不足: 1個      │
└────┴──────────┴─────────────────────────────────┘
```

## レポート出力

### JSONレポートファイル
`storage/logs/audio_guide_quality_report_YYYY-MM-DD_HH-mm-ss.json`

```json
{
  "check_date": "2025-06-30T14:30:00.000000Z",
  "total_spots": 79,
  "quality_spots": 77,
  "issue_spots": 2,
  "results": {
    "1": {
      "is_quality": true,
      "spot_name": "東京スカイツリー",
      "issues": [],
      "metrics": {
        "text_length": 616,
        "episode_count": 3,
        "has_numbers": true,
        "has_year_info": true
      }
    },
    // ... 他の観光地の結果
  }
}
```

## トラブルシューティング

### よくある問題と解決策

#### 1. テスト失敗: "Array to string conversion"
**原因**: AudioGuideControllerのデータ構造に問題がある
**解決策**: 
```bash
# データ構造テストを実行して詳細を確認
./vendor/bin/phpunit tests/Unit/AudioGuideDataTest.php --filter test_all_tourist_spots_have_required_data_structure
```

#### 2. メモリ不足エラー
**原因**: 大量の観光地データを一度に処理している
**解決策**:
```bash
# PHPのメモリ制限を増加
php -d memory_limit=512M artisan audio-guide:quality-check
```

#### 3. タイムアウトエラー
**原因**: ネットワーク接続やAWS Pollyサービスの遅延
**解決策**:
```bash
# Pollyサービスを使わずにテキスト生成のみテスト
./vendor/bin/phpunit tests/Unit/AudioGuideDataTest.php
```

### デバッグ方法

#### 1. 特定の観光地の詳細確認
```bash
# 特定の観光地の音声ガイドテキストを確認
php artisan audio-guide:quality-check --spot-id=901 --verbose
```

#### 2. APIエンドポイントでの直接確認
```bash
# cURLでの音声ガイド生成テスト
curl -X POST http://localhost:8000/api/audio-guide/tourist-spot \
  -H "Content-Type: application/json" \
  -d '{"spot_id": 901, "voice_id": "Tomoko"}' | jq .
```

#### 3. ログファイルの確認
```bash
# Laravelログの確認
tail -f storage/logs/laravel.log

# 音声ガイド品質レポートの確認
cat storage/logs/audio_guide_quality_report_*.json | jq .
```

## 継続的品質管理

### 定期実行の設定

#### 1. Cronでの定期実行
```bash
# crontabに追加（毎日午前2時に実行）
0 2 * * * cd /path/to/backend && php artisan audio-guide:quality-check --export-report
```

#### 2. Laravelスケジューラーでの設定
`app/Console/Kernel.php`に追加：
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('audio-guide:quality-check --export-report')
             ->daily()
             ->at('02:00');
}
```

### アラート設定

品質スコアが100%を下回った場合のアラート設定例：
```bash
#!/bin/bash
# quality_check_with_alert.sh

cd /path/to/backend
php artisan audio-guide:quality-check --export-report

# 最新のレポートファイルを取得
LATEST_REPORT=$(ls -t storage/logs/audio_guide_quality_report_*.json | head -1)

# 品質スコアを確認
ISSUE_COUNT=$(cat "$LATEST_REPORT" | jq .issue_spots)

if [ "$ISSUE_COUNT" -gt 0 ]; then
    # Slackやメールでアラート送信
    echo "⚠️ 音声ガイド品質に問題があります: $ISSUE_COUNT 件の要改善項目" | curl -X POST -H 'Content-type: application/json' --data '{"text":"'"$(cat)"'"}' YOUR_SLACK_WEBHOOK_URL
fi
```

## まとめ

このテストフレームワークにより、全ての観光地の音声ガイドが汎用テンプレートではなく、詳細で質の高いコンテンツを提供していることを継続的に監視できます。定期的にテストを実行し、品質の維持・向上を図ってください。
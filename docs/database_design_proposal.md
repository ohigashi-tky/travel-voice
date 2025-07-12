# 観光地タグ管理システム - データベース設計提案

## 現状の問題点
- タグが定数ファイルにハードコーディングされている
- 新しいタグの追加や変更にコード修正が必要
- タグの使用統計や人気度の分析ができない

## 提案するデータベース設計

### 1. テーブル構成

#### `tags` テーブル
```sql
CREATE TABLE tags (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    color_class VARCHAR(100) DEFAULT 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `travel_spot_tags` テーブル（中間テーブル）
```sql
CREATE TABLE travel_spot_tags (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    travel_spot_id BIGINT NOT NULL,
    tag_id BIGINT NOT NULL,
    priority INT DEFAULT 1, -- タグの表示優先度
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (travel_spot_id) REFERENCES travel_spots(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_spot_tag (travel_spot_id, tag_id)
);
```

#### `tag_rules` テーブル（自動タグ付与ルール）
```sql
CREATE TABLE tag_rules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tag_id BIGINT NOT NULL,
    rule_type ENUM('name_contains', 'category_equals', 'prefecture_equals') NOT NULL,
    rule_value VARCHAR(100) NOT NULL,
    priority INT DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
```

### 2. Laravel実装例

#### Tag Model
```php
class Tag extends Model
{
    protected $fillable = ['name', 'color_class', 'description', 'is_active'];
    
    public function travelSpots()
    {
        return $this->belongsToMany(TravelSpot::class, 'travel_spot_tags')
                    ->withPivot('priority')
                    ->orderByPivot('priority');
    }
    
    public function rules()
    {
        return $this->hasMany(TagRule::class);
    }
}
```

#### TravelSpot Model 拡張
```php
class TravelSpot extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'travel_spot_tags')
                    ->withPivot('priority')
                    ->orderByPivot('priority');
    }
    
    // 自動タグ付与
    public function autoAssignTags()
    {
        $rules = TagRule::where('is_active', true)->with('tag')->get();
        
        foreach ($rules as $rule) {
            $shouldAssign = false;
            
            switch ($rule->rule_type) {
                case 'name_contains':
                    $shouldAssign = str_contains($this->name, $rule->rule_value);
                    break;
                case 'category_equals':
                    $shouldAssign = $this->category === $rule->rule_value;
                    break;
                case 'prefecture_equals':
                    $shouldAssign = $this->prefecture === $rule->rule_value;
                    break;
            }
            
            if ($shouldAssign && !$this->tags->contains($rule->tag_id)) {
                $this->tags()->attach($rule->tag_id, ['priority' => $rule->priority]);
            }
        }
    }
}
```

#### API エンドポイント
```php
// api/travel-spots/{id}/tags
public function getTags(TravelSpot $touristSpot)
{
    return response()->json([
        'success' => true,
        'data' => $touristSpot->tags->map(function ($tag) {
            return [
                'name' => $tag->name,
                'color_class' => $tag->color_class,
                'priority' => $tag->pivot->priority
            ];
        })
    ]);
}
```

### 3. フロントエンド実装例

#### APIからタグ取得
```typescript
// composables/useTravelSpotTags.ts
export const useTravelSpotTags = () => {
  const getTags = async (spotId: number): Promise<SpotTag[]> => {
    try {
      const config = useRuntimeConfig()
      const apiBaseUrl = process.server ? config.apiBaseServer : config.public.apiBase
      
      const response = await $fetch<{
        success: boolean
        data: SpotTag[]
      }>(`${apiBaseUrl}/api/travel-spots/${spotId}/tags`)
      
      return response.data || []
    } catch (error) {
      // フォールバック: 定数ファイルから取得
      return getSpotTags(spotName, category)
    }
  }
  
  return { getTags }
}
```

### 4. 管理機能

#### タグ管理画面
- タグの追加・編集・削除
- タグルールの設定
- タグ使用統計の表示
- 色の設定

#### 自動化機能
- 新しい観光地追加時の自動タグ付与
- バッチ処理での既存データへのタグ付与
- タグ使用状況の分析

### 5. 移行計画

#### Phase 1（現在完了）
- ✅ 定数ファイルでタグ管理
- ✅ ハードコーディング削除

#### Phase 2（短期）
- データベーステーブル作成
- 既存タグデータの移行
- 基本的なCRUD API実装

#### Phase 3（中期）
- 自動タグ付与ルール実装
- 管理画面開発
- APIからのタグ取得実装

#### Phase 4（長期）
- タグ使用統計・分析機能
- AI による自動タグ提案
- タグベースの推薦機能

## メリット

### 保守性
- コード修正なしでタグ管理が可能
- ルールベースの自動タグ付与
- 一元的なタグ管理

### 拡張性
- 新しいタグタイプの追加が容易
- 複雑なタグ付与ルールの実装可能
- 多言語対応（タグの翻訳）

### 分析性
- タグ使用統計
- 人気タグの分析
- ユーザー行動分析

この設計により、観光地タグ管理がよりプロフェッショナルで拡張可能なシステムになります。
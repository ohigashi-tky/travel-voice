<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prefecture;

class PrefectureSortOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 地域の並び順（北から南へ）
        $regionOrders = [
            '北海道・東北' => 1,
            '関東' => 2,
            '中部' => 3,
            '近畿' => 4,
            '中国' => 5,
            '四国' => 6,
            '九州・沖縄' => 7
        ];

        // 地域内での順番
        $orderInRegion = [
            // 関東
            '東京都' => 1,
            '神奈川県' => 2,
            '千葉県' => 3,
            '埼玉県' => 4,
            '茨城県' => 5,
            '栃木県' => 6,
            '群馬県' => 7,
            
            // 近畿
            '大阪府' => 1,
            '京都府' => 2,
            '奈良県' => 3,
            '兵庫県' => 4,
            '和歌山県' => 5,
            '滋賀県' => 6,
            '三重県' => 7,
            
            // 中部
            '愛知県' => 1,
            '静岡県' => 2,
            '長野県' => 3,
            '岐阜県' => 4,
            '石川県' => 5,
            '富山県' => 6,
            '山梨県' => 7,
            '新潟県' => 8,
            '福井県' => 9,
            
            // 九州・沖縄
            '福岡県' => 1,
            '熊本県' => 2,
            '鹿児島県' => 3,
            '長崎県' => 4,
            '大分県' => 5,
            '宮崎県' => 6,
            '佐賀県' => 7,
            '沖縄県' => 8,
            
            // 北海道・東北
            '北海道' => 1,
            '宮城県' => 2,
            '福島県' => 3,
            '青森県' => 4,
            '岩手県' => 5,
            '山形県' => 6,
            '秋田県' => 7,
            
            // 中国
            '広島県' => 1,
            '岡山県' => 2,
            '山口県' => 3,
            '島根県' => 4,
            '鳥取県' => 5,
            
            // 四国
            '愛媛県' => 1,
            '香川県' => 2,
            '徳島県' => 3,
            '高知県' => 4
        ];

        // 主要都道府県の並び順
        $featuredOrders = [
            '東京都' => 1,
            '大阪府' => 2,
            '京都府' => 3,
            '北海道' => 4,
            '沖縄県' => 5,
            '福岡県' => 6,
            '愛知県' => 7,
            '神奈川県' => 8,
            '広島県' => 9,
            '愛媛県' => 10,
            '山口県' => 11,
            '鹿児島県' => 12
        ];

        // 都道府県データを更新
        Prefecture::all()->each(function ($prefecture) use ($regionOrders, $orderInRegion, $featuredOrders) {
            $updates = [];
            
            // 地域の並び順
            if (isset($regionOrders[$prefecture->region])) {
                $updates['region_order'] = $regionOrders[$prefecture->region];
            }
            
            // 地域内での順番
            if (isset($orderInRegion[$prefecture->name])) {
                $updates['order_in_region'] = $orderInRegion[$prefecture->name];
            }
            
            // 主要都道府県の並び順
            if (isset($featuredOrders[$prefecture->name])) {
                $updates['featured_order'] = $featuredOrders[$prefecture->name];
            }
            
            if (!empty($updates)) {
                $prefecture->update($updates);
            }
        });
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TouristSpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $touristSpots = [
            [
                'name' => '東京スカイツリー',
                'description' => '東京の新しいシンボルタワー。高さ634mの世界一高い自立式電波塔で、展望デッキからは関東平野を一望できます。',
                'prefecture' => '東京都',
                'city' => '墨田区',
                'address' => '東京都墨田区押上1-1-2',
                'latitude' => 35.710063,
                'longitude' => 139.810700,
                'category' => '観光施設',
                'images' => [
                    'https://example.com/skytree1.jpg',
                    'https://example.com/skytree2.jpg'
                ],
                'access_info' => '東武スカイツリーライン「とうきょうスカイツリー駅」すぐ、東京メトロ半蔵門線「押上駅」徒歩5分',
                'website' => 'https://www.tokyo-skytree.jp/',
                'phone' => '0570-55-0634',
                'opening_hours' => '8:00-22:00（最終入場21:00）',
                'admission_fee' => '大人2,100円〜、中学生1,550円〜、小学生950円〜',
                'is_active' => true,
            ],
            [
                'name' => '浅草寺',
                'description' => '東京最古の寺院として知られ、雷門の大提灯で有名。仲見世通りでの食べ歩きも楽しめる、日本の伝統文化を感じられるスポットです。',
                'prefecture' => '東京都',
                'city' => '台東区',
                'address' => '東京都台東区浅草2-3-1',
                'latitude' => 35.714765,
                'longitude' => 139.796742,
                'category' => '寺院',
                'images' => [
                    'https://example.com/asakusa1.jpg',
                    'https://example.com/asakusa2.jpg'
                ],
                'access_info' => '東京メトロ銀座線「浅草駅」徒歩5分、都営地下鉄浅草線「浅草駅」徒歩7分',
                'website' => 'https://www.senso-ji.jp/',
                'phone' => '03-3842-0181',
                'opening_hours' => '6:00-17:00（10月-3月は6:30-17:00）',
                'admission_fee' => '無料',
                'is_active' => true,
            ],
            [
                'name' => '明治神宮',
                'description' => '明治天皇と昭憲皇太后を祀る神社。都心にありながら豊かな森に囲まれた神聖な空間で、初詣では日本一の参拝者数を誇ります。',
                'prefecture' => '東京都',
                'city' => '渋谷区',
                'address' => '東京都渋谷区代々木神園町1-1',
                'latitude' => 35.676391,
                'longitude' => 139.699431,
                'category' => '神社',
                'images' => [
                    'https://example.com/meiji1.jpg',
                    'https://example.com/meiji2.jpg'
                ],
                'access_info' => 'JR山手線「原宿駅」徒歩3分、東京メトロ千代田線・副都心線「明治神宮前駅」徒歩3分',
                'website' => 'https://www.meijijingu.or.jp/',
                'phone' => '03-3379-5511',
                'opening_hours' => '日の出〜日没（季節により変動）',
                'admission_fee' => '無料（宝物殿は有料）',
                'is_active' => true,
            ],
            [
                'name' => '皇居東御苑',
                'description' => '江戸城の跡地に造られた庭園。四季折々の美しい自然と歴史を感じることができる、都心のオアシスです。',
                'prefecture' => '東京都',
                'city' => '千代田区',
                'address' => '東京都千代田区千代田1-1',
                'latitude' => 35.685175,
                'longitude' => 139.753348,
                'category' => '庭園',
                'images' => [
                    'https://example.com/kokyo1.jpg',
                    'https://example.com/kokyo2.jpg'
                ],
                'access_info' => 'JR「東京駅」徒歩10分、東京メトロ大手町駅徒歩5分',
                'website' => 'https://www.kunaicho.go.jp/',
                'phone' => '03-3213-1111',
                'opening_hours' => '9:00-17:00（入園は16:30まで）月曜・金曜休園',
                'admission_fee' => '無料',
                'is_active' => true,
            ],
            [
                'name' => '上野動物園',
                'description' => '日本最古の動物園。ジャイアントパンダをはじめ、約400種3000点の動物たちに出会えます。',
                'prefecture' => '東京都',
                'city' => '台東区',
                'address' => '東京都台東区上野公園9-83',
                'latitude' => 35.716626,
                'longitude' => 139.771306,
                'category' => '動物園',
                'images' => [
                    'https://example.com/ueno1.jpg',
                    'https://example.com/ueno2.jpg'
                ],
                'access_info' => 'JR「上野駅」徒歩5分、東京メトロ銀座線・日比谷線「上野駅」徒歩5分',
                'website' => 'https://www.tokyo-zoo.net/zoo/ueno/',
                'phone' => '03-3828-5171',
                'opening_hours' => '9:30-17:00（入園は16:00まで）月曜休園',
                'admission_fee' => '一般600円、中学生200円、小学生以下無料',
                'is_active' => true,
            ]
        ];

        foreach ($touristSpots as $spot) {
            \App\Models\TouristSpot::create($spot);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prefecture;

class PrefectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prefectures = [
            // 北海道・東北
            ['name' => '北海道', 'name_kana' => 'ホッカイドウ', 'region' => '北海道・東北', 'display_order' => 1],
            ['name' => '青森県', 'name_kana' => 'アオモリケン', 'region' => '北海道・東北', 'display_order' => 2],
            ['name' => '岩手県', 'name_kana' => 'イワテケン', 'region' => '北海道・東北', 'display_order' => 3],
            ['name' => '宮城県', 'name_kana' => 'ミヤギケン', 'region' => '北海道・東北', 'display_order' => 4],
            ['name' => '秋田県', 'name_kana' => 'アキタケン', 'region' => '北海道・東北', 'display_order' => 5],
            ['name' => '山形県', 'name_kana' => 'ヤマガタケン', 'region' => '北海道・東北', 'display_order' => 6],
            ['name' => '福島県', 'name_kana' => 'フクシマケン', 'region' => '北海道・東北', 'display_order' => 7],
            
            // 関東
            ['name' => '茨城県', 'name_kana' => 'イバラキケン', 'region' => '関東', 'display_order' => 8],
            ['name' => '栃木県', 'name_kana' => 'トチギケン', 'region' => '関東', 'display_order' => 9],
            ['name' => '群馬県', 'name_kana' => 'グンマケン', 'region' => '関東', 'display_order' => 10],
            ['name' => '埼玉県', 'name_kana' => 'サイタマケン', 'region' => '関東', 'display_order' => 11],
            ['name' => '千葉県', 'name_kana' => 'チバケン', 'region' => '関東', 'display_order' => 12],
            ['name' => '東京都', 'name_kana' => 'トウキョウト', 'region' => '関東', 'display_order' => 13],
            ['name' => '神奈川県', 'name_kana' => 'カナガワケン', 'region' => '関東', 'display_order' => 14],
            
            // 中部
            ['name' => '新潟県', 'name_kana' => 'ニイガタケン', 'region' => '中部', 'display_order' => 15],
            ['name' => '富山県', 'name_kana' => 'トヤマケン', 'region' => '中部', 'display_order' => 16],
            ['name' => '石川県', 'name_kana' => 'イシカワケン', 'region' => '中部', 'display_order' => 17],
            ['name' => '福井県', 'name_kana' => 'フクイケン', 'region' => '中部', 'display_order' => 18],
            ['name' => '山梨県', 'name_kana' => 'ヤマナシケン', 'region' => '中部', 'display_order' => 19],
            ['name' => '長野県', 'name_kana' => 'ナガノケン', 'region' => '中部', 'display_order' => 20],
            ['name' => '岐阜県', 'name_kana' => 'ギフケン', 'region' => '中部', 'display_order' => 21],
            ['name' => '静岡県', 'name_kana' => 'シズオカケン', 'region' => '中部', 'display_order' => 22],
            ['name' => '愛知県', 'name_kana' => 'アイチケン', 'region' => '中部', 'display_order' => 23],
            
            // 近畿
            ['name' => '三重県', 'name_kana' => 'ミエケン', 'region' => '近畿', 'display_order' => 24],
            ['name' => '滋賀県', 'name_kana' => 'シガケン', 'region' => '近畿', 'display_order' => 25],
            ['name' => '京都府', 'name_kana' => 'キョウトフ', 'region' => '近畿', 'display_order' => 26],
            ['name' => '大阪府', 'name_kana' => 'オオサカフ', 'region' => '近畿', 'display_order' => 27],
            ['name' => '兵庫県', 'name_kana' => 'ヒョウゴケン', 'region' => '近畿', 'display_order' => 28],
            ['name' => '奈良県', 'name_kana' => 'ナラケン', 'region' => '近畿', 'display_order' => 29],
            ['name' => '和歌山県', 'name_kana' => 'ワカヤマケン', 'region' => '近畿', 'display_order' => 30],
            
            // 中国
            ['name' => '鳥取県', 'name_kana' => 'トットリケン', 'region' => '中国', 'display_order' => 31],
            ['name' => '島根県', 'name_kana' => 'シマネケン', 'region' => '中国', 'display_order' => 32],
            ['name' => '岡山県', 'name_kana' => 'オカヤマケン', 'region' => '中国', 'display_order' => 33],
            ['name' => '広島県', 'name_kana' => 'ヒロシマケン', 'region' => '中国', 'display_order' => 34],
            ['name' => '山口県', 'name_kana' => 'ヤマグチケン', 'region' => '中国', 'display_order' => 35],
            
            // 四国
            ['name' => '徳島県', 'name_kana' => 'トクシマケン', 'region' => '四国', 'display_order' => 36],
            ['name' => '香川県', 'name_kana' => 'カガワケン', 'region' => '四国', 'display_order' => 37],
            ['name' => '愛媛県', 'name_kana' => 'エヒメケン', 'region' => '四国', 'display_order' => 38],
            ['name' => '高知県', 'name_kana' => 'コウチケン', 'region' => '四国', 'display_order' => 39],
            
            // 九州・沖縄
            ['name' => '福岡県', 'name_kana' => 'フクオカケン', 'region' => '九州・沖縄', 'display_order' => 40],
            ['name' => '佐賀県', 'name_kana' => 'サガケン', 'region' => '九州・沖縄', 'display_order' => 41],
            ['name' => '長崎県', 'name_kana' => 'ナガサキケン', 'region' => '九州・沖縄', 'display_order' => 42],
            ['name' => '熊本県', 'name_kana' => 'クマモトケン', 'region' => '九州・沖縄', 'display_order' => 43],
            ['name' => '大分県', 'name_kana' => 'オオイタケン', 'region' => '九州・沖縄', 'display_order' => 44],
            ['name' => '宮崎県', 'name_kana' => 'ミヤザキケン', 'region' => '九州・沖縄', 'display_order' => 45],
            ['name' => '鹿児島県', 'name_kana' => 'カゴシマケン', 'region' => '九州・沖縄', 'display_order' => 46],
            ['name' => '沖縄県', 'name_kana' => 'オキナワケン', 'region' => '九州・沖縄', 'display_order' => 47],
        ];

        // 現在観光地データがある都道府県を取得
        $availablePrefectures = [
            '北海道', '東京都', '神奈川県', '埼玉県', '千葉県', '新潟県', '静岡県', '愛知県',
            '京都府', '大阪府', '兵庫県', '広島県', '山口県', '徳島県', '愛媛県', '福岡県',
            '福島県', '鹿児島県', '沖縄県'
        ];

        foreach ($prefectures as $prefecture) {
            $prefecture['is_available'] = in_array($prefecture['name'], $availablePrefectures);
            Prefecture::firstOrCreate(
                ['name' => $prefecture['name']],
                $prefecture
            );
        }
    }
}
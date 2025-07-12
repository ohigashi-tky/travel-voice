<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 各都道府県から代表的なイベントを1つずつ選出（47件）
        $events = [
            [
                'title' => 'さっぽろ雪まつり',
                'description' => '世界的に有名な雪と氷の祭典。大小様々な雪像や氷像が展示され、ライトアップも行われます。',
                'overview' => '大小様々な雪像・氷像が並ぶ冬の祭典。幻想的なライトアップやイベントも楽しめます。',
                'prefecture' => '北海道',
                'location' => '大通公園',
                'start_date' => '2026-02-01',
                'end_date' => '2026-02-12',
                'category' => 'その他',
                'tags' => json_encode(['雪まつり', '冬', '祭り', '氷像']),
                'access' => '札幌市営地下鉄大通駅、すすきの駅、さっぽろ駅直結',
                'url' => '',
                'organizer' => '',
                'display_order' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'ねぶた祭',
                'description' => '青森の夏を代表する伝統的な祭り。巨大なねぶた灯籠が街を練り歩きます。',
                'overview' => '巨大なねぶた灯籠が街を練り歩く青森の代表的な夏祭り。',
                'prefecture' => '青森県',
                'location' => '青森市中心部',
                'start_date' => '2025-08-02',
                'end_date' => '2025-08-07',
                'category' => 'その他',
                'tags' => json_encode(['ねぶた', '夏', '祭り', '伝統']),
                'access' => 'JR青森駅から徒歩約5分',
                'url' => '',
                'organizer' => '',
                'display_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '桜まつり',
                'description' => '東京の桜の名所で開催される春の風物詩。多くの屋台も出店し、家族連れに人気です。',
                'overview' => '都内有数の桜の名所で開催される春の祭典。屋台やライトアップも楽しめます。',
                'prefecture' => '東京都',
                'location' => '上野公園',
                'start_date' => '2025-03-25',
                'end_date' => '2025-04-10',
                'category' => 'その他',
                'tags' => json_encode(['桜', '春', '花見', '祭り']),
                'access' => 'JR上野駅公園口から徒歩2分',
                'url' => '',
                'organizer' => '',
                'display_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '天神祭',
                'description' => '大阪の夏を代表する日本三大祭りの一つ。華やかな船渡御と花火大会が有名です。',
                'overview' => '日本三大祭りの一つ。華やかな船渡御と花火大会で大阪の夏を彩ります。',
                'prefecture' => '大阪府',
                'location' => '大阪天満宮',
                'start_date' => '2025-07-24',
                'end_date' => '2025-07-25',
                'category' => 'その他',
                'tags' => json_encode(['天神祭', '夏', '祭り', '花火']),
                'access' => '地下鉄谷町線・堺筋線南森町駅から徒歩3分',
                'url' => '',
                'organizer' => '',
                'display_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '祇園祭',
                'description' => '京都の夏を代表する千年以上の歴史を持つ祇園祭。山鉾巡行は圧巻の光景です。',
                'overview' => '千年以上の歴史を持つ京都の代表的な祭り。華麗な山鉾巡行は必見です。',
                'prefecture' => '京都府',
                'location' => '八坂神社',
                'start_date' => '2025-07-01',
                'end_date' => '2025-07-31',
                'category' => 'その他',
                'tags' => json_encode(['祇園祭', '夏', '祭り', '山鉾']),
                'access' => '京阪祇園四条駅から徒歩5分',
                'url' => '',
                'organizer' => '',
                'display_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
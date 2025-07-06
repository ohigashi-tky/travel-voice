<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => '桜まつり',
                'description' => '美しい桜の花見を楽しめる春の風物詩。多くの屋台も出店し、家族連れに人気のイベントです。夜間はライトアップも行われ、幻想的な夜桜を楽しめます。',
                'prefecture' => '東京都',
                'location' => '上野公園',
                'start_date' => Carbon::create(2024, 3, 25),
                'end_date' => Carbon::create(2024, 4, 10),
                'image_url' => null,
                'tags' => ['桜', '花見', '春', '家族向け'],
                'is_active' => true,
            ],
            [
                'title' => '大阪城夏祭り',
                'description' => '大阪城公園で開催される夏の大イベント。伝統的な盆踊りから現代的なパフォーマンスまで、様々な催し物が楽しめます。フィナーレの花火大会は圧巻です。',
                'prefecture' => '大阪府',
                'location' => '大阪城公園',
                'start_date' => Carbon::create(2024, 7, 15),
                'end_date' => Carbon::create(2024, 7, 17),
                'image_url' => null,
                'tags' => ['夏祭り', '花火', '盆踊り', '伝統'],
                'is_active' => true,
            ],
            [
                'title' => '嵐山紅葉ライトアップ',
                'description' => '京都嵐山の美しい紅葉がライトアップされる幻想的なイベント。竹林の小径や渡月橋周辺が幻想的に照らされ、昼間とは異なる美しさを楽しめます。',
                'prefecture' => '京都府',
                'location' => '嵐山',
                'start_date' => Carbon::create(2024, 11, 1),
                'end_date' => Carbon::create(2024, 11, 30),
                'image_url' => null,
                'tags' => ['紅葉', 'ライトアップ', '秋', '竹林'],
                'is_active' => true,
            ],
            [
                'title' => '札幌雪まつり',
                'description' => '札幌の冬を代表する国際的なイベント。大通公園には巨大な雪像が並び、すすきの会場では氷の彫刻が楽しめます。北海道グルメも堪能できます。',
                'prefecture' => '北海道',
                'location' => '大通公園・すすきの',
                'start_date' => Carbon::create(2025, 2, 5),
                'end_date' => Carbon::create(2025, 2, 11),
                'image_url' => null,
                'tags' => ['雪まつり', '冬', '雪像', '北海道グルメ'],
                'is_active' => true,
            ],
            [
                'title' => '博多どんたく',
                'description' => '福岡の春を彩る市民総参加のお祭り。「博多どんたく港まつり」として親しまれ、パレードや舞台イベントが市内各所で開催されます。',
                'prefecture' => '福岡県',
                'location' => '博多駅周辺・天神',
                'start_date' => Carbon::create(2024, 5, 3),
                'end_date' => Carbon::create(2024, 5, 4),
                'image_url' => null,
                'tags' => ['どんたく', '春', 'パレード', '市民参加'],
                'is_active' => true,
            ],
            [
                'title' => '広島フラワーフェスティバル',
                'description' => '広島の平和大通りで開催される花の祭典。色とりどりの花で飾られたパレードや平和への願いを込めたイベントが行われます。',
                'prefecture' => '広島県',
                'location' => '平和大通り・平和記念公園',
                'start_date' => Carbon::create(2024, 5, 3),
                'end_date' => Carbon::create(2024, 5, 5),
                'image_url' => null,
                'tags' => ['花', '平和', 'パレード', 'ゴールデンウィーク'],
                'is_active' => true,
            ],
            [
                'title' => '愛媛みかんまつり',
                'description' => '愛媛県の特産品であるみかんをテーマにした収穫祭。みかん狩り体験やみかんを使った料理の試食、地元農家との交流が楽しめます。',
                'prefecture' => '愛媛県',
                'location' => '愛媛県庁周辺',
                'start_date' => Carbon::create(2024, 10, 15),
                'end_date' => Carbon::create(2024, 10, 17),
                'image_url' => null,
                'tags' => ['みかん', '収穫祭', '秋', '地域特産'],
                'is_active' => true,
            ],
            [
                'title' => '新潟雪国まつり',
                'description' => '新潟の豊かな雪国文化を体験できるイベント。雪灯籠の展示や雪国料理の試食、伝統的な雪国の暮らしを体験できます。',
                'prefecture' => '新潟県',
                'location' => '新潟駅周辺',
                'start_date' => Carbon::create(2025, 1, 20),
                'end_date' => Carbon::create(2025, 1, 22),
                'image_url' => null,
                'tags' => ['雪国', '冬', '雪灯籠', '郷土料理'],
                'is_active' => true,
            ],
            [
                'title' => '山口維新まつり',
                'description' => '明治維新発祥の地・山口で開催される歴史イベント。幕末の志士たちに扮した時代行列や歴史講演会が行われます。',
                'prefecture' => '山口県',
                'location' => '山口市中心街',
                'start_date' => Carbon::create(2024, 10, 5),
                'end_date' => Carbon::create(2024, 10, 7),
                'image_url' => null,
                'tags' => ['明治維新', '歴史', '時代行列', '幕末'],
                'is_active' => true,
            ],
            [
                'title' => '徳島阿波踊り',
                'description' => '徳島県の代表的な伝統芸能である阿波踊りの本場のお祭り。「踊る阿呆に見る阿呆、同じ阿呆なら踊らにゃ損々」で有名な夏の風物詩です。',
                'prefecture' => '徳島県',
                'location' => '徳島市内各所',
                'start_date' => Carbon::create(2024, 8, 12),
                'end_date' => Carbon::create(2024, 8, 15),
                'image_url' => null,
                'tags' => ['阿波踊り', '夏', '伝統芸能', '盆踊り'],
                'is_active' => true,
            ],
            [
                'title' => '鹿児島黒豚まつり',
                'description' => '鹿児島県の特産品である黒豚をテーマにしたグルメイベント。黒豚料理の試食や料理教室、生産者との交流が楽しめます。',
                'prefecture' => '鹿児島県',
                'location' => '鹿児島中央駅周辺',
                'start_date' => Carbon::create(2024, 9, 20),
                'end_date' => Carbon::create(2024, 9, 22),
                'image_url' => null,
                'tags' => ['黒豚', 'グルメ', '地域特産', '食文化'],
                'is_active' => true,
            ],
            [
                'title' => '埼玉コスモスまつり',
                'description' => '埼玉県の広大なコスモス畑で開催される花の祭典。一面に咲くコスモスの花海を楽しめるほか、地元農産物の販売も行われます。',
                'prefecture' => '埼玉県',
                'location' => '埼玉県内コスモス畑',
                'start_date' => Carbon::create(2024, 9, 15),
                'end_date' => Carbon::create(2024, 10, 10),
                'image_url' => null,
                'tags' => ['コスモス', '花', '秋', '農産物'],
                'is_active' => true,
            ]
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
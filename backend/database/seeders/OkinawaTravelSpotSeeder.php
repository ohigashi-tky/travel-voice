<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OkinawaTravelSpotSeeder extends Seeder
{
    public function run()
    {
        $okinawaSpots = [
            [
                'name' => '首里城',
                'prefecture_id' => 47, // 沖縄県のID
                'category_id' => 1, // 観光名所
                'description' => '琉球王国の王城として築かれた城で、沖縄県を代表する観光スポットです。世界遺産にも登録されており、琉球文化の象徴的存在です。',
                'address' => '沖縄県那覇市首里金城町1-2',
                'latitude' => 26.217,
                'longitude' => 127.719,
                'place_id' => null, // Google Places APIで後ほど取得
                'rating' => 4.5,
                'user_ratings_total' => 15000,
                'guide_voice_text' => '首里城へようこそ。ここは琉球王国の政治・外交・文化の中心地でした。1429年から1879年まで、約450年間にわたって琉球王国の居城として使用されました。赤瓦の美しい建築様式は、中国と日本の文化が融合した独特なものです。',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '美ら海水族館',
                'prefecture_id' => 47,
                'category_id' => 4, // テーマパーク・動物園・水族館
                'description' => '世界最大級の水族館で、ジンベエザメやマンタが悠々と泳ぐ巨大水槽「黒潮の海」が有名です。',
                'address' => '沖縄県国頭郡本部町石川424',
                'latitude' => 26.694,
                'longitude' => 127.878,
                'place_id' => null,
                'rating' => 4.6,
                'user_ratings_total' => 25000,
                'guide_voice_text' => '美ら海水族館へようこそ。ここには世界最大級の大水槽「黒潮の海」があり、ジンベエザメやマンタをはじめ、約740種21,000点の生き物たちが展示されています。特に、複数のジンベエザメを同時に飼育展示しているのは世界でもここだけです。',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '国際通り',
                'prefecture_id' => 47,
                'category_id' => 5, // ショッピング
                'description' => '那覇市の中心部にある約1.6kmの通りで、沖縄のお土産店や飲食店が立ち並ぶ観光のメインストリートです。',
                'address' => '沖縄県那覇市牧志',
                'latitude' => 26.211,
                'longitude' => 127.685,
                'place_id' => null,
                'rating' => 4.2,
                'user_ratings_total' => 18000,
                'guide_voice_text' => '国際通りへようこそ。戦後の焼け野原から奇跡的に復興を遂げたことから「奇跡の1マイル」とも呼ばれています。沖縄の伝統工芸品、泡盛、ちんすこうなどのお土産はもちろん、沖縄料理のレストランも多数あり、沖縄の文化を満喫できる場所です。',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($okinawaSpots as $spot) {
            DB::table('travel_spots')->insert($spot);
        }
    }
}
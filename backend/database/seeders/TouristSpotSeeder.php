<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TouristSpot;

class TouristSpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $touristSpots = [
            // 神奈川県
            [
                'name' => '鎌倉大仏（高徳院）',
                'description' => '日本で2番目に大きな大仏として親しまれる鎌倉のシンボル。高さ13.35mの阿弥陀如来坐像です。',
                'overview' => '鎌倉大仏は正式には高徳院の本尊である阿弥陀如来坐像で、鎌倉時代（1252年頃）に造立されました。高さ13.35m、重量約121トンの青銅製で、日本で2番目に大きな大仏として知られています。内部は空洞になっており、胎内拝観も可能です。',
                'highlights' => ['大仏殿跡の礎石', '胎内拝観', '観月堂', '与謝野晶子の歌碑'],
                'history' => '鎌倉時代の建長4年（1252年）頃に造立が開始されました。当初は大仏殿に安置されていましたが、室町時代の津波や地震により大仏殿が倒壊し、現在は露座の大仏として親しまれています。',
                'prefecture' => '神奈川県',
                'city' => '鎌倉市',
                'address' => '神奈川県鎌倉市長谷4-2-28',
                'latitude' => 35.316667,
                'longitude' => 139.536111,
                'category' => '寺院',
                'images' => [
                    'https://example.com/kamakura-daibutsu1.jpg',
                    'https://example.com/kamakura-daibutsu2.jpg',
                    'https://example.com/kamakura-daibutsu3.jpg',
                    'https://example.com/kamakura-daibutsu4.jpg',
                    'https://example.com/kamakura-daibutsu5.jpg'
                ],
                'access_info' => 'JR東海道線・小田急線「藤沢駅」から江ノ電「長谷駅」徒歩7分',
                'public_transport' => [
                    ['type' => '電車', 'route' => '江ノ島電鉄', 'station' => '長谷駅', 'time' => '徒歩7分'],
                    ['type' => 'バス', 'route' => '京急バス', 'station' => '大仏前', 'time' => '徒歩1分']
                ],
                'car_access' => [
                    ['from' => '横浜横須賀道路', 'exit' => '朝比奈IC', 'time' => '約30分']
                ],
                'parking_info' => '周辺有料駐車場利用・料金：1時間300円程度',
                'walking_info' => '鎌倉駅から徒歩約25分、長谷寺から徒歩約5分',
                'website' => 'https://www.kotoku-in.jp/',
                'phone' => '0467-22-0703',
                'opening_hours' => '8:00-17:30（10月〜3月は17:00まで）',
                'admission_fee' => '大人300円、小学生150円（胎内拝観別途30円）',
                'is_active' => true,
            ],
            [
                'name' => '箱根神社',
                'description' => '芦ノ湖畔に建つ湖上の鳥居で有名な神社。関東総鎮守として多くの参拝者が訪れます。',
                'overview' => '箱根神社は奈良時代の天平宝字元年（757年）に万巻上人により創建された古社です。芦ノ湖に立つ平和の鳥居は箱根のシンボルとして親しまれ、開運厄除・心願成就・交通安全などのご利益があるとされています。',
                'highlights' => ['平和の鳥居', '九頭龍神社新宮', '宝物殿', '杉並木'],
                'history' => '天平宝字元年（757年）、万巻上人により創建。源頼朝が深く信仰し、鎌倉幕府の守護神として崇敬されました。江戸時代には東海道の要衝として多くの参拝者が訪れ、現在も関東総鎮守として親しまれています。',
                'prefecture' => '神奈川県',
                'city' => '箱根町',
                'address' => '神奈川県足柄下郡箱根町元箱根80-1',
                'latitude' => 35.204444,
                'longitude' => 139.025556,
                'category' => '神社',
                'images' => [
                    'https://example.com/hakone-jinja1.jpg',
                    'https://example.com/hakone-jinja2.jpg',
                    'https://example.com/hakone-jinja3.jpg',
                    'https://example.com/hakone-jinja4.jpg',
                    'https://example.com/hakone-jinja5.jpg'
                ],
                'access_info' => 'JR東海道線「小田原駅」から箱根登山バス「元箱根」下車徒歩10分',
                'public_transport' => [
                    ['type' => 'バス', 'route' => '箱根登山バス', 'station' => '元箱根', 'time' => '徒歩10分'],
                    ['type' => '船', 'route' => '箱根海賊船', 'station' => '元箱根港', 'time' => '徒歩10分']
                ],
                'car_access' => [
                    ['from' => '東名高速道路', 'exit' => '御殿場IC', 'time' => '約30分']
                ],
                'parking_info' => '第1・第2駐車場あり・料金：1時間300円',
                'walking_info' => '芦ノ湖畔遊歩道散策、恩賜箱根公園まで徒歩約15分',
                'website' => 'http://hakonejinja.or.jp/',
                'phone' => '0460-83-7123',
                'opening_hours' => '参拝自由（宝物殿9:00-16:30）',
                'admission_fee' => '無料（宝物殿は大人500円）',
                'is_active' => true,
            ],

            // 千葉県
            [
                'name' => '成田山新勝寺',
                'description' => '真言宗智山派の大本山。初詣の参拝者数は全国屈指で、成田のお不動さんとして親しまれています。',
                'overview' => '成田山新勝寺は天慶3年（940年）に寛朝大僧正により開山された真言宗智山派の大本山です。本尊の不動明王は弘法大師空海が一刀三礼をもって敬刻開眼された御尊像で、1000年以上にわたって護摩の火が絶えることなく燃え続けています。',
                'highlights' => ['大本堂', '三重塔', '光明堂', '成田山公園'],
                'history' => '天慶3年（940年）、平将門の乱を鎮めるため朱雀天皇の勅命により寛朝大僧正が東国へ下向し、不動明王の霊力により乱を平定後、この地に成田山新勝寺を開山しました。江戸時代には「成田詣」として庶民に親しまれました。',
                'prefecture' => '千葉県',
                'city' => '成田市',
                'address' => '千葉県成田市成田1番地',
                'latitude' => 35.777778,
                'longitude' => 140.318889,
                'category' => '寺院',
                'images' => [
                    'https://example.com/naritasan1.jpg',
                    'https://example.com/naritasan2.jpg',
                    'https://example.com/naritasan3.jpg',
                    'https://example.com/naritasan4.jpg',
                    'https://example.com/naritasan5.jpg'
                ],
                'access_info' => 'JR成田線・京成本線「成田駅」から徒歩10分',
                'public_transport' => [
                    ['type' => 'JR', 'route' => '成田線', 'station' => '成田駅', 'time' => '徒歩10分'],
                    ['type' => '私鉄', 'route' => '京成本線', 'station' => '京成成田駅', 'time' => '徒歩10分']
                ],
                'car_access' => [
                    ['from' => '東関東自動車道', 'exit' => '成田IC', 'time' => '約10分']
                ],
                'parking_info' => '周辺有料駐車場多数・料金：1時間200円程度',
                'walking_info' => '成田山表参道散策、成田山公園まで徒歩約5分',
                'website' => 'https://www.naritasan.or.jp/',
                'phone' => '0476-22-2111',
                'opening_hours' => '6:00-16:30（時期により異なる）',
                'admission_fee' => '無料',
                'is_active' => true,
            ],
            [
                'name' => '鴨川シーワールド',
                'description' => '海の世界との出会いをテーマにした水族館。シャチやイルカのパフォーマンスで有名な日本を代表する海洋公園です。',
                'overview' => '鴨川シーワールドは1970年に開館した日本を代表する水族館です。800種11,000点の海の動物を飼育展示し、特にシャチのパフォーマンスは日本で唯一見ることができます。海の世界との出会いをテーマに、教育と娯楽を兼ね備えた施設として親しまれています。',
                'highlights' => ['シャチパフォーマンス', 'イルカパフォーマンス', 'ベルーガパフォーマンス', '笑うアシカ'],
                'history' => '1970年に開館。日本初のシャチの飼育展示を開始し、1998年には日本初のシャチの繁殖に成功しました。海洋生物の研究・保護活動にも力を入れ、多くの研究成果を上げています。',
                'prefecture' => '千葉県',
                'city' => '鴨川市',
                'address' => '千葉県鴨川市東町1464-18',
                'latitude' => 35.114722,
                'longitude' => 140.123611,
                'category' => '水族館',
                'images' => [
                    'https://example.com/kamogawa-seaworld1.jpg',
                    'https://example.com/kamogawa-seaworld2.jpg',
                    'https://example.com/kamogawa-seaworld3.jpg',
                    'https://example.com/kamogawa-seaworld4.jpg',
                    'https://example.com/kamogawa-seaworld5.jpg'
                ],
                'access_info' => 'JR外房線「安房鴨川駅」から無料送迎バス約10分',
                'public_transport' => [
                    ['type' => 'JR', 'route' => '外房線', 'station' => '安房鴨川駅', 'time' => 'バス10分'],
                    ['type' => 'バス', 'route' => '鴨川日東バス', 'station' => 'シーワールド前', 'time' => '徒歩すぐ']
                ],
                'car_access' => [
                    ['from' => '館山自動車道', 'exit' => '君津IC', 'time' => '約45分']
                ],
                'parking_info' => '無料駐車場1200台完備',
                'walking_info' => '鴨川温泉街まで徒歩約20分',
                'website' => 'https://www.kamogawa-seaworld.jp/',
                'phone' => '04-7093-4803',
                'opening_hours' => '9:00-17:00（季節により異なる）',
                'admission_fee' => '大人3300円、小人（4歳〜中学生）2000円',
                'is_active' => true,
            ],

            // 兵庫県
            [
                'name' => '姫路城',
                'description' => '白鷺城とも呼ばれる世界文化遺産の名城。日本の城郭建築の最高峰として国宝に指定されています。',
                'overview' => '姫路城は慶長14年（1609年）に池田輝政により現在の姿に完成した平山城です。白漆喰総塗籠造りの美しい外観から「白鷺城」と呼ばれ、日本初の世界文化遺産として登録されています。400年以上の歴史を持つ現存天守として、日本の城郭建築の最高傑作とされています。',
                'highlights' => ['大天守', '西の丸', '菱の門', '姫路城西御屋敷跡庭園好古園'],
                'history' => '建武3年（1336年）、赤松貞範が姫山に築城したのが始まり。その後、黒田官兵衛、池田輝政らにより拡張され、現在の姿になりました。明治の廃城令でも取り壊されず、戦災も免れた奇跡の城として知られています。',
                'prefecture' => '兵庫県',
                'city' => '姫路市',
                'address' => '兵庫県姫路市本町68番地',
                'latitude' => 34.839444,
                'longitude' => 134.693889,
                'category' => '歴史建造物',
                'images' => [
                    'https://example.com/himeji-castle1.jpg',
                    'https://example.com/himeji-castle2.jpg',
                    'https://example.com/himeji-castle3.jpg',
                    'https://example.com/himeji-castle4.jpg',
                    'https://example.com/himeji-castle5.jpg'
                ],
                'access_info' => 'JR山陽新幹線・山陽本線・播但線「姫路駅」から徒歩20分',
                'public_transport' => [
                    ['type' => 'JR', 'route' => '山陽新幹線', 'station' => '姫路駅', 'time' => '徒歩20分'],
                    ['type' => 'バス', 'route' => '神姫バス', 'station' => '大手門前', 'time' => '徒歩5分']
                ],
                'car_access' => [
                    ['from' => '山陽自動車道', 'exit' => '姫路東IC', 'time' => '約15分']
                ],
                'parking_info' => '大手門駐車場・料金：3時間以内600円、3時間超900円',
                'walking_info' => '好古園まで徒歩約5分、姫路市立美術館まで徒歩約10分',
                'website' => 'https://www.city.himeji.lg.jp/guide/castle/',
                'phone' => '079-285-1146',
                'opening_hours' => '9:00-16:00（夏期は17:00まで）',
                'admission_fee' => '大人1000円、小人（小学生・中学生・高校生）300円',
                'is_active' => true,
            ],
            [
                'name' => '有馬温泉',
                'description' => '日本三古湯の一つとして親しまれる歴史ある温泉地。金泉・銀泉の2つの泉質で多くの観光客を魅了しています。',
                'overview' => '有馬温泉は日本三古湯・日本三名泉の一つとして知られる温泉地です。茶褐色の金泉（含鉄ナトリウム塩化物強塩高温泉）と無色透明の銀泉（二酸化炭素泉・ラジウム泉）の2つの泉質があり、それぞれ異なる効能が楽しめます。',
                'highlights' => ['金の湯', '銀の湯', '有馬玩具博物館', '温泉寺'],
                'history' => '舒明天皇の時代（629年〜641年）に発見されたと伝えられ、奈良時代には行基により温泉寺が建立されました。平安時代には清少納言の「枕草子」にも記され、豊臣秀吉も愛した名湯として1400年の歴史を誇ります。',
                'prefecture' => '兵庫県',
                'city' => '神戸市',
                'address' => '兵庫県神戸市北区有馬町',
                'latitude' => 34.800556,
                'longitude' => 135.248611,
                'category' => '温泉',
                'images' => [
                    'https://example.com/arima-onsen1.jpg',
                    'https://example.com/arima-onsen2.jpg',
                    'https://example.com/arima-onsen3.jpg',
                    'https://example.com/arima-onsen4.jpg',
                    'https://example.com/arima-onsen5.jpg'
                ],
                'access_info' => '神戸電鉄有馬線「有馬温泉駅」下車すぐ',
                'public_transport' => [
                    ['type' => '私鉄', 'route' => '神戸電鉄有馬線', 'station' => '有馬温泉駅', 'time' => '徒歩すぐ'],
                    ['type' => 'バス', 'route' => '阪急バス', 'station' => '有馬温泉', 'time' => '徒歩すぐ']
                ],
                'car_access' => [
                    ['from' => '中国自動車道', 'exit' => '西宮北IC', 'time' => '約15分']
                ],
                'parking_info' => '各旅館・ホテルに駐車場あり、町営駐車場も利用可能',
                'walking_info' => '温泉街散策、瑞宝寺公園まで徒歩約15分',
                'website' => 'https://www.arima-onsen.com/',
                'phone' => '078-904-0708',
                'opening_hours' => '24時間（各施設により異なる）',
                'admission_fee' => '日帰り入浴：金の湯650円、銀の湯550円',
                'is_active' => true,
            ],

            // 静岡県
            [
                'name' => '富士山',
                'description' => '日本最高峰の霊峰。世界文化遺産に登録され、古来より信仰の対象として親しまれる日本のシンボルです。',
                'overview' => '富士山は標高3776mの日本最高峰の山で、その美しい円錐形の姿から「霊峰富士」と呼ばれています。2013年に「富士山-信仰の対象と芸術の源泉」として世界文化遺産に登録され、古来より山岳信仰の対象として、また芸術作品のモチーフとして親しまれてきました。',
                'highlights' => ['富士山本宮浅間大社', '富士五湖', '青木ヶ原樹海', '白糸の滝'],
                'history' => '富士山は約10万年前から活動を始めた活火山で、現在の美しい形は約1万年前に形成されました。古代から山岳信仰の対象とされ、平安時代には富士山本宮浅間大社が創建され、江戸時代には富士講により庶民の信仰を集めました。',
                'prefecture' => '静岡県',
                'city' => '富士宮市',
                'address' => '静岡県富士宮市粟倉',
                'latitude' => 35.360556,
                'longitude' => 138.727778,
                'category' => '自然・公園',
                'images' => [
                    'https://example.com/fujisan1.jpg',
                    'https://example.com/fujisan2.jpg',
                    'https://example.com/fujisan3.jpg',
                    'https://example.com/fujisan4.jpg',
                    'https://example.com/fujisan5.jpg'
                ],
                'access_info' => '富士宮口五合目まで：JR身延線「富士宮駅」からバス約1時間',
                'public_transport' => [
                    ['type' => 'バス', 'route' => '富士急静岡バス', 'station' => '富士宮口五合目', 'time' => '富士宮駅から約1時間'],
                    ['type' => 'バス', 'route' => '富士急行バス', 'station' => '河口湖口五合目', 'time' => '河口湖駅から約50分']
                ],
                'car_access' => [
                    ['from' => '東名高速道路', 'exit' => '富士IC', 'time' => '富士宮口五合目まで約1時間']
                ],
                'parking_info' => '五合目駐車場あり・登山期間中有料',
                'walking_info' => '登山期間：7月上旬〜9月上旬、山頂まで登山道4ルート',
                'website' => 'https://www.fujisan-climb.jp/',
                'phone' => '0544-27-5240（富士宮市観光課）',
                'opening_hours' => '24時間（登山期間限定）',
                'admission_fee' => '無料（登山道使用料・保全協力金あり）',
                'is_active' => true,
            ],
            [
                'name' => '熱海温泉',
                'description' => '古くから湯治場として栄えた日本有数の温泉リゾート。海と山に囲まれた美しい景観と豊富な湯量が自慢です。',
                'overview' => '熱海温泉は奈良時代から知られる歴史ある温泉地で、江戸時代には徳川家康も湯治に訪れました。相模湾を望む美しい景観と、毎分約23,000リットルの豊富な湯量を誇り、「日本の三大温泉」の一つとされています。現在も多くの温泉旅館やホテルが立ち並ぶ一大温泉リゾートです。',
                'highlights' => ['熱海梅園', '來宮神社', '熱海城', 'MOA美術館'],
                'history' => '奈良時代に発見されたと伝えられ、鎌倉時代には源頼朝も訪れました。江戸時代には徳川家康が湯治に通い、明治時代以降は政財界人や文人墨客に愛される温泉地として発展しました。',
                'prefecture' => '静岡県',
                'city' => '熱海市',
                'address' => '静岡県熱海市',
                'latitude' => 35.096111,
                'longitude' => 139.077778,
                'category' => '温泉',
                'images' => [
                    'https://example.com/atami-onsen1.jpg',
                    'https://example.com/atami-onsen2.jpg',
                    'https://example.com/atami-onsen3.jpg',
                    'https://example.com/atami-onsen4.jpg',
                    'https://example.com/atami-onsen5.jpg'
                ],
                'access_info' => 'JR東海道新幹線・東海道本線「熱海駅」下車',
                'public_transport' => [
                    ['type' => 'JR', 'route' => '東海道新幹線', 'station' => '熱海駅', 'time' => '徒歩すぐ'],
                    ['type' => 'JR', 'route' => '東海道本線', 'station' => '熱海駅', 'time' => '徒歩すぐ']
                ],
                'car_access' => [
                    ['from' => '東名高速道路', 'exit' => '厚木IC', 'time' => '約1時間']
                ],
                'parking_info' => '各宿泊施設に駐車場あり、市営駐車場も利用可能',
                'walking_info' => '温泉街散策、熱海梅園まで徒歩約15分',
                'website' => 'https://www.atami.gr.jp/',
                'phone' => '0557-81-0041',
                'opening_hours' => '24時間（各施設により異なる）',
                'admission_fee' => '施設により異なる',
                'is_active' => true,
            ],
        ];

        foreach ($touristSpots as $spot) {
            TouristSpot::create($spot);
        }
    }
}
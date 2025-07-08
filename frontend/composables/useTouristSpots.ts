import type { TouristSpot } from '~/types'

// 本来はAPIから取得すべきデータ
// 将来的にはLaravelのAPIエンドポイントから取得
const TOURIST_SPOTS_DATA: TouristSpot[] = [
  {
    id: 1,
    name: '東京スカイツリー',
    description: '高さ634mの世界最高クラスの電波塔。展望デッキからは東京の絶景を一望できます。',
    category: '展望台',
    prefecture: '東京都',
    place_id: 'ChIJ_3YKZnuLGGARYE4bbxAnJks',
    overview: '東京スカイツリーは、東京都墨田区押上にある電波塔で、2012年に開業しました。高さ634mは世界第2位の高さを誇り、東京の新たなランドマークとして親しまれています。',
    highlights: ['展望デッキ（350m・450m）', 'スカイツリータウン', 'ライトアップ', 'プラネタリウム'],
    history: '2008年に着工し、2012年に完成。武蔵国の旧国名に因んで634m（ムサシ）の高さに設計されました。建設には最新の制振技術が使われ、日本の伝統的な建築技法も取り入れられています。',
    images: []
  },
  {
    id: 2,
    name: '浅草寺',
    description: '東京最古の寺院。雷門と仲見世通りで有名な東京を代表する観光地です。',
    category: '寺院',
    prefecture: '東京都',
    place_id: 'ChIJ8T1GpMGOGGAR_t0nCB4YDUM',
    overview: '浅草寺は628年に創建された東京最古の寺院です。雷門から仲見世通りを通って本堂に至る参道は、常に多くの参拝者と観光客で賑わっています。',
    highlights: ['雷門', '仲見世通り', '本堂', '五重塔'],
    history: '推古天皇36年（628年）、隅田川で漁をしていた檜前浜成・竹成兄弟の網にかかった観音像を本尊として祀ったのが始まりとされています。江戸時代には徳川家の祈願所として栄えました。',
    images: []  },
  {
    id: 3,
    name: '皇居',
    description: '天皇陛下のお住まいである皇居。美しい庭園と江戸城の歴史を感じることができます。',
    category: '歴史建造物',
    prefecture: '東京都',
    place_id: 'ChIJ51cu8IcbXWAR_W6_upPpA5E',
    overview: '皇居は江戸城の跡地に建てられた天皇陛下のお住まいです。東御苑は一般公開されており、江戸城の遺構や美しい庭園を楽しむことができます。',
    highlights: ['東御苑', '二重橋', '皇居外苑', '桜田門'],
    history: '徳川家康が慶長8年（1603年）に江戸城として築城。明治維新後に皇居となり、現在に至っています。',
    images: []
  },
  {
    id: 4,
    name: '銀座',
    description: '日本を代表する高級商業地区。世界的なブランド店や老舗が立ち並ぶ大人の街です。',
    category: '観光エリア',
    prefecture: '東京都',
    place_id: 'ChIJ85F8iIuLGGARJp2e_OrR7tM',
    overview: '銀座は明治時代から続く日本を代表する商業地区です。高級ブランド店、老舗百貨店、美術館、レストランが集積し、洗練された大人の街として世界的に知られています。',
    highlights: ['銀座四丁目交差点', '歌舞伎座', '銀座三越', '中央通り'],
    history: '江戸時代に銀貨の鋳造所「銀座」が置かれたことが地名の由来。明治時代に煉瓦街として整備され、現在の高級商業地としての基礎が築かれました。',
    images: []
  },
  {
    id: 101,
    name: '大阪城',
    description: '豊臣秀吉が築城した名城。美しい天守閣と桜の名所として親しまれています。',
    category: '歴史建造物',
    prefecture: '大阪府',
    overview: '大阪城は豊臣秀吉によって1583年に築城された日本の名城の一つです。現在の天守閣は1931年に再建されたもので、大阪のシンボルとして親しまれています。',
    highlights: ['天守閣', '大阪城公園', '桜の名所', '歴史博物館'],
    history: '1583年、豊臣秀吉が石山本願寺の跡地に築城を開始。当時は「三国無双」と称される壮大な城でした。江戸時代には徳川幕府の直轄地となり、明治維新後は陸軍の施設として使用されました。',
    images: []  },
  {
    id: 201,
    name: '清水寺',
    description: '778年開創の京都最古の寺院。有名な清水の舞台と美しい景色で知られています。',
    category: '寺院',
    prefecture: '京都府',
    overview: '清水寺は京都東山にある法相宗の寺院で、「清水の舞台」で有名です。1994年にユネスコ世界文化遺産に登録されており、年間を通じて多くの観光客が訪れます。',
    highlights: ['清水の舞台', '音羽の滝', '地主神社', '三重塔'],
    history: '延暦17年（798年）、坂上田村麻呂によって建立されました。平安時代から「清水詣」として庶民に親しまれ、江戸時代には西国三十三箇所観音霊場の第16番札所として栄えました。',
    images: []  },
  {
    id: 301,
    name: '札幌時計台',
    description: '旧札幌農学校演武場として1878年に建設された北海道のシンボル的建造物です。',
    category: '歴史建造物',
    prefecture: '北海道',
    place_id: 'ChIJR3JQJ3YpC18R680ES0qomxs',
    overview: '札幌時計台は正式名称を「旧札幌農学校演武場」といい、1878年に建設された北海道の代表的な観光スポットです。現在は重要文化財に指定されています。',
    highlights: ['時計台の鐘', '歴史展示', 'クラーク博士の資料', 'コンサートホール'],
    history: '明治11年（1878年）、札幌農学校（現在の北海道大学）の演武場として建設されました。時計は明治14年に設置され、以来140年以上にわたって札幌の街に時を告げ続けています。',
    images: []  },
  {
    id: 202,
    name: '金閣寺',
    description: '足利義満の別荘として建てられた金箔で覆われた美しい楼閣。世界文化遺産です。',
    category: '寺院',
    prefecture: '京都府',
    overview: '金閣寺（鹿苑寺）は室町幕府三代将軍足利義満の別荘として1397年に建てられました。金箔で覆われた三層の楼閣が池に映る美しい姿は、京都を代表する風景です。',
    highlights: ['金閣（舎利殿）', '鏡湖池', '庭園', '夕佳亭'],
    history: '応永4年（1397年）、足利義満が西園寺家の山荘を譲り受けて「北山殿」を造営。義満の死後、禅寺となり鹿苑寺と名付けられました。昭和25年に放火により焼失しましたが、昭和30年に再建されました。',
    images: []  },
  // 愛知県
  {
    id: 401,
    name: '名古屋城',
    description: '徳川家康が築城した名古屋のシンボル。金の鯱鉾で有名な日本三大名城の一つです。',
    category: '歴史建造物',
    prefecture: '愛知県',
  },
  // 福岡県
  {
    id: 501,
    name: '太宰府天満宮',
    description: '学問の神様菅原道真公を祀る由緒ある神社。受験合格や学業成就を願う多くの参拝者が訪れます。',
    category: '神社',
    prefecture: '福岡県',
    place_id: 'ChIJ5SZMmDGmQzURgK1hKZ4qAQM',
    overview: '太宰府天満宮は学問の神様として親しまれる菅原道真公を祀る全国約12,000社の総本宮です。年間約1,000万人の参拝者が訪れ、学業成就・合格祈願の聖地として知られています。',
    highlights: ['本殿', '飛梅', '心字池', '宝物殿', '梅ヶ枝餅'],
    history: '延喜3年（903年）に菅原道真公が薨去された後、延喜19年（919年）に現在地に社殿が建立されました。道真公は優秀な学者・政治家として活躍し、後に「天神様」として崇敬されるようになりました。',
    images: []
  },
  {
    id: 502,
    name: '福岡城跡',
    description: '黒田如水・長政父子が築城した福岡藩52万石の居城跡。桜の名所としても有名です。',
    category: '歴史建造物',
    prefecture: '福岡県',
    place_id: 'ChIJBVyR8k-mQzURX1J4kPBLMWE',
    overview: '福岡城は慶長6年（1601年）から7年の歳月をかけて黒田長政によって築かれた平山城です。現在は舞鶴公園として整備され、春には約1,000本の桜が咲き誇る九州屈指の桜の名所となっています。',
    highlights: ['天守台跡', '多聞櫓', '潮見櫓', '桜並木', '黒田家の歴史'],
    history: '関ヶ原の戦いの功により筑前国に入封した黒田長政が、慶長6年（1601年）から築城を開始。総面積約25万平方メートルの巨大な城郭でしたが、明治時代に多くの建物が取り壊されました。',
    images: []
  },
  {
    id: 503,
    name: '博多駅',
    description: '九州の玄関口として親しまれる巨大ターミナル駅。グルメやショッピングも楽しめます。',
    category: '観光エリア',
    prefecture: '福岡県',
    place_id: 'ChIJ5SZMmDGmQzURX1J4kPBLMWE',
    overview: '博多駅は九州最大のターミナル駅として、新幹線・在来線の拠点となっています。駅ビルには約230の専門店や飲食店が入り、博多ラーメンや明太子など福岡グルメを堪能できる一大商業施設です。',
    highlights: ['博多ラーメン街道', '博多阪急', 'アミュプラザ博多', '屋上庭園', '福岡グルメ'],
    history: '明治22年（1889年）に九州鉄道の駅として開業。昭和50年に山陽新幹線が延伸し、九州の玄関口としての地位を確立しました。平成23年に現在の駅ビルが完成し、九州最大級の商業施設となりました。',
    images: []
  },
  {
    id: 504,
    name: 'キャナルシティ博多',
    description: '運河が流れる開放的なショッピングモール。ファッション、グルメ、エンターテイメントが集結する複合商業施設です。',
    category: '観光エリア',
    prefecture: '福岡県',
    place_id: 'ChIJ-QQ8yTGmQzURTElXKZ_qWQs',
    overview: 'キャナルシティ博多は「都市の中の運河」をコンセプトに1996年にオープンした複合商業施設です。中央を流れる運河と噴水ショーが印象的で、ショッピング、グルメ、映画、劇場が一体となった九州最大級のエンターテイメント施設です。',
    highlights: ['運河と噴水ショー', 'ファッション専門店', 'ラーメンスタジアム', '博多座', '映画館'],
    history: '1996年4月に開業。アメリカの建築家ジョン・ジャーディが設計し、「都市の劇場」をコンセプトとした斬新なデザインで注目を集めました。開業以来、福岡の新たな観光スポットとして多くの人々に愛され続けています。',
    images: []
  },
  {
    id: 505,
    name: '櫛田神社',
    description: '博多総鎮守として親しまれる古社。博多祇園山笠の出発点として知られ、博多っ子の心の拠り所です。',
    category: '神社',
    prefecture: '福岡県',
    place_id: 'ChIJOzKmjzGmQzURd5Q-Z7SqAQM',
    overview: '櫛田神社は博多の総鎮守として757年に創建された古社です。毎年7月に開催される博多祇園山笠の出発点として全国的に有名で、境内には実物大の飾り山が常設展示されています。商売繁盛や不老長寿のご利益があるとされています。',
    highlights: ['飾り山常設展示', '博多祇園山笠', '夫婦恵比須神社', '博多塀', '不老長寿の霊水'],
    history: '天平宝字元年（757年）に創建されたと伝えられます。古くから博多の総鎮守として崇敬され、特に博多祇園山笠との結びつきが深く、毎年7月1日から15日の山笠期間中は多くの参拝者で賑わいます。',
    images: []
  },
  {
    id: 506,
    name: '福岡PayPayドーム',
    description: '福岡ソフトバンクホークスの本拠地として親しまれる日本初の開閉式ドーム球場。野球以外にもコンサートなど多彩なイベントが開催されます。',
    category: '観光エリア',
    prefecture: '福岡県',
    place_id: 'ChIJdVyR8k-mQzURmjVqWQjNKAM',
    overview: '福岡PayPayドーム（旧ヤフオクドーム）は1993年に開業した日本初の開閉式屋根を持つドーム球場です。福岡ソフトバンクホークスの本拠地として親しまれ、野球シーズン以外にもコンサートや展示会など様々なイベントが開催される九州最大級のイベント施設です。',
    highlights: ['開閉式屋根', '福岡ソフトバンクホークス', 'コンサート会場', '王貞治ベースボールミュージアム', 'ホークスタウンモール'],
    history: '1993年4月に「福岡ドーム」として開業。2005年にソフトバンクが球団を買収後、「ヤフオクドーム」を経て2020年に「PayPayドーム」に改称されました。開業以来、九州のスポーツ・エンターテイメントの拠点として親しまれています。',
    images: []
  },
  // 神奈川県
  {
    id: 801,
    name: '鎌倉大仏（高徳院）',
    description: '日本で2番目に大きな大仏として親しまれる鎌倉のシンボル。高さ13.35mの阿弥陀如来坐像です。',
    category: '寺院',
    prefecture: '神奈川県',
    overview: '鎌倉大仏は正式には高徳院の本尊である阿弥陀如来坐像で、鎌倉時代（1252年頃）に造立されました。高さ13.35m、重量約121トンの青銅製で、日本で2番目に大きな大仏として知られています。内部は空洞になっており、胎内拝観も可能です。',
    highlights: ['大仏殿跡の礎石', '胎内拝観', '観月堂', '与謝野晶子の歌碑'],
    history: '鎌倉時代の建長4年（1252年）頃に造立が開始されました。当初は大仏殿に安置されていましたが、室町時代の津波や地震により大仏殿が倒壊し、現在は露座の大仏として親しまれています。',
    images: []
  },
  {
    id: 802,
    name: '箱根神社',
    description: '芦ノ湖畔に建つ湖上の鳥居で有名な神社。関東総鎮守として多くの参拝者が訪れます。',
    category: '神社',
    prefecture: '神奈川県',
    overview: '箱根神社は奈良時代の天平宝字元年（757年）に万巻上人により創建された古社です。芦ノ湖に立つ平和の鳥居は箱根のシンボルとして親しまれ、開運厄除・心願成就・交通安全などのご利益があるとされています。',
    highlights: ['平和の鳥居', '九頭龍神社新宮', '宝物殿', '杉並木'],
    history: '天平宝字元年（757年）、万巻上人により創建。源頼朝が深く信仰し、鎌倉幕府の守護神として崇敬されました。江戸時代には東海道の要衝として多くの参拝者が訪れ、現在も関東総鎮守として親しまれています。',
    images: []
  },
  // 千葉県
  {
    id: 901,
    name: '成田山新勝寺',
    description: '真言宗智山派の大本山。初詣の参拝者数は全国屈指で、成田のお不動さんとして親しまれています。',
    category: '寺院',
    prefecture: '千葉県',
    overview: '成田山新勝寺は天慶3年（940年）に寛朝大僧正により開山された真言宗智山派の大本山です。本尊の不動明王は弘法大師空海が一刀三礼をもって敬刻開眼された御尊像で、1000年以上にわたって護摩の火が絶えることなく燃え続けています。',
    highlights: ['大本堂', '三重塔', '光明堂', '成田山公園'],
    history: '天慶3年（940年）、平将門の乱を鎮めるため朱雀天皇の勅命により寛朝大僧正が東国へ下向し、不動明王の霊力により乱を平定後、この地に成田山新勝寺を開山しました。江戸時代には「成田詣」として庶民に親しまれました。',
    images: []
  },
  {
    id: 902,
    name: '鴨川シーワールド',
    description: '海の世界との出会いをテーマにした水族館。シャチやイルカのパフォーマンスで有名な日本を代表する海洋公園です。',
    category: '水族館',
    prefecture: '千葉県',
    overview: '鴨川シーワールドは1970年に開館した日本を代表する水族館です。800種11,000点の海の動物を飼育展示し、特にシャチのパフォーマンスは日本で唯一見ることができます。海の世界との出会いをテーマに、教育と娯楽を兼ね備えた施設として親しまれています。',
    highlights: ['シャチパフォーマンス', 'イルカパフォーマンス', 'ベルーガパフォーマンス', '笑うアシカ'],
    history: '1970年に開館。日本初のシャチの飼育展示を開始し、1998年には日本初のシャチの繁殖に成功しました。海洋生物の研究・保護活動にも力を入れ、多くの研究成果を上げています。',
    images: []
  },
  // 兵庫県
  {
    id: 1001,
    name: '姫路城',
    description: '白鷺城とも呼ばれる世界文化遺産の名城。日本の城郭建築の最高峰として国宝に指定されています。',
    category: '歴史建造物',
    prefecture: '兵庫県',
    overview: '姫路城は慶長14年（1609年）に池田輝政により現在の姿に完成した平山城です。白漆喰総塗籠造りの美しい外観から「白鷺城」と呼ばれ、日本初の世界文化遺産として登録されています。400年以上の歴史を持つ現存天守として、日本の城郭建築の最高傑作とされています。',
    highlights: ['大天守', '西の丸', '菱の門', '姫路城西御屋敷跡庭園好古園'],
    history: '建武3年（1336年）、赤松貞範が姫山に築城したのが始まり。その後、黒田官兵衛、池田輝政らにより拡張され、現在の姿になりました。明治の廃城令でも取り壊されず、戦災も免れた奇跡の城として知られています。',
    images: []
  },
  {
    id: 1002,
    name: '有馬温泉',
    description: '日本三古湯の一つとして親しまれる歴史ある温泉地。金泉・銀泉の2つの泉質で多くの観光客を魅了しています。',
    category: '温泉',
    prefecture: '兵庫県',
    overview: '有馬温泉は日本三古湯・日本三名泉の一つとして知られる温泉地です。茶褐色の金泉（含鉄ナトリウム塩化物強塩高温泉）と無色透明の銀泉（二酸化炭素泉・ラジウム泉）の2つの泉質があり、それぞれ異なる効能が楽しめます。',
    highlights: ['金の湯', '銀の湯', '有馬玩具博物館', '温泉寺'],
    history: '舒明天皇の時代（629年〜641年）に発見されたと伝えられ、奈良時代には行基により温泉寺が建立されました。平安時代には清少納言の「枕草子」にも記され、豊臣秀吉も愛した名湯として1400年の歴史を誇ります。',
    images: []
  },
  // 静岡県
  {
    id: 1101,
    name: '富士山',
    description: '日本最高峰の霊峰。世界文化遺産に登録され、古来より信仰の対象として親しまれる日本のシンボルです。',
    category: '自然・公園',
    prefecture: '静岡県',
    overview: '富士山は標高3776mの日本最高峰の山で、その美しい円錐形の姿から「霊峰富士」と呼ばれています。2013年に「富士山-信仰の対象と芸術の源泉」として世界文化遺産に登録され、古来より山岳信仰の対象として、また芸術作品のモチーフとして親しまれてきました。',
    highlights: ['富士山本宮浅間大社', '富士五湖', '青木ヶ原樹海', '白糸の滝'],
    history: '富士山は約10万年前から活動を始めた活火山で、現在の美しい形は約1万年前に形成されました。古代から山岳信仰の対象とされ、平安時代には富士山本宮浅間大社が創建され、江戸時代には富士講により庶民の信仰を集めました。',
    images: []
  },
  {
    id: 1102,
    name: '熱海温泉',
    description: '古くから湯治場として栄えた日本有数の温泉リゾート。海と山に囲まれた美しい景観と豊富な湯量が自慢です。',
    category: '温泉',
    prefecture: '静岡県',
    overview: '熱海温泉は奈良時代から知られる歴史ある温泉地で、江戸時代には徳川家康も湯治に訪れました。相模湾を望む美しい景観と、毎分約23,000リットルの豊富な湯量を誇り、「日本の三大温泉」の一つとされています。現在も多くの温泉旅館やホテルが立ち並ぶ一大温泉リゾートです。',
    highlights: ['熱海梅園', '來宮神社', '熱海城', 'MOA美術館'],
    history: '奈良時代に発見されたと伝えられ、鎌倉時代には源頼朝も訪れました。江戸時代には徳川家康が湯治に通い、明治時代以降は政財界人や文人墨客に愛される温泉地として発展しました。',
    images: []
  },
  // 広島県
  {
    id: 601,
    name: '原爆ドーム',
    description: '平和の象徴として世界中に知られる広島の代表的なランドマーク。ユネスコ世界文化遺産です。',
    category: '歴史建造物',
    prefecture: '広島県',
  },
  {
    id: 602,
    name: '厳島神社',
    description: '海に浮かぶ朱色の大鳥居で有名な日本三景の一つ。満潮時の美しさは格別です。',
    category: '神社',
    prefecture: '広島県',
  },
  // 愛媛県
  {
    id: 701,
    name: '道後温泉',
    description: '日本最古の温泉の一つ。夏目漱石の小説にも登場する歴史ある温泉街です。',
    category: '温泉',
    prefecture: '愛媛県',
  }
  // TODO: 他の都道府県のデータも追加予定
  // 現在はLaravelバックエンドのAPIエンドポイント開発中
]

export const useTouristSpots = () => {
  const spots = ref<TouristSpot[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // データの重複を除去するヘルパー関数
  const deduplicateSpots = (spotsList: TouristSpot[]) => {
    const seen = new Map()
    return spotsList.filter(spot => {
      // 名前と都道府県の組み合わせで重複判定
      const key = `${spot.name}_${spot.prefecture}`
      if (seen.has(key)) {
        return false
      }
      seen.set(key, true)
      return true
    })
  }

  // APIから観光スポットデータを取得
  const fetchSpots = async () => {
    loading.value = true
    error.value = null
    
    try {
      const config = useRuntimeConfig()
      let apiBaseUrl
      if (process.server) {
        apiBaseUrl = config.apiBaseServer
      } else {
        const isLocalDev = window.location.hostname === 'localhost' && window.location.port === '3000'
        apiBaseUrl = isLocalDev ? 'http://localhost:8000' : config.public.apiBase
      }
      const response = await $fetch(`${apiBaseUrl}/api/tourist-spots`)
      
      // APIデータとローカルデータをマージして重複除去
      const allSpots = [...response, ...TOURIST_SPOTS_DATA]
      spots.value = deduplicateSpots(allSpots)
    } catch (err) {
      error.value = 'Failed to fetch tourist spots'
      console.error('Error fetching tourist spots:', err)
      
      // フォールバック: エラー時はモックデータを使用（重複除去済み）
      spots.value = deduplicateSpots(TOURIST_SPOTS_DATA)
    } finally {
      loading.value = false
    }
  }

  // APIから県別スポットを取得
  const fetchSpotsByPrefecture = async (prefecture: string) => {
    loading.value = true
    error.value = null
    
    try {
      const config = useRuntimeConfig()
      let apiBaseUrl
      if (process.server) {
        apiBaseUrl = config.apiBaseServer
      } else {
        const isLocalDev = window.location.hostname === 'localhost' && window.location.port === '3000'
        apiBaseUrl = isLocalDev ? 'http://localhost:8000' : config.public.apiBase
      }
      const response = await $fetch(`${apiBaseUrl}/api/tourist-spots/prefecture/${encodeURIComponent(prefecture)}`)
      
      return response
    } catch (err) {
      error.value = 'Failed to fetch prefecture spots'
      console.error('Error fetching prefecture spots:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  // 県別でフィルタリング（ローカルデータから）
  const getSpotsByPrefecture = (prefecture: string) => {
    return spots.value.filter(spot => spot.prefecture === prefecture)
  }

  // カテゴリ別でフィルタリング
  const getSpotsByCategory = (category: string) => {
    return spots.value.filter(spot => spot.category === category)
  }

  // IDで検索
  const getSpotById = (id: number) => {
    return spots.value.find(spot => spot.id === id)
  }

  // ランダムに指定数取得
  const getRandomSpots = (count: number) => {
    const shuffled = [...spots.value].sort(() => 0.5 - Math.random())
    return shuffled.slice(0, count)
  }

  // AIで分析された人気スポットを取得
  const popularSpots = ref<TouristSpot[]>([])
  const popularSpotsLoading = ref(false)
  const popularSpotsError = ref<string | null>(null)

  const fetchPopularSpots = async () => {
    popularSpotsLoading.value = true
    popularSpotsError.value = null

    try {
      const config = useRuntimeConfig()
      let apiBaseUrl
      if (process.server) {
        apiBaseUrl = config.apiBaseServer
      } else {
        const isLocalDev = window.location.hostname === 'localhost' && window.location.port === '3000'
        apiBaseUrl = isLocalDev ? 'http://localhost:8000' : config.public.apiBase
      }
      
      const response = await $fetch<{
        success: boolean
        data: TouristSpot[]
        fallback?: boolean
        message?: string
      }>(`${apiBaseUrl}/api/popular-spots`)

      if (response.success) {
        // 人気スポットも重複除去
        popularSpots.value = deduplicateSpots(response.data)
        if (response.fallback) {
          console.warn('Using fallback popular spots:', response.message)
        }
      } else {
        throw new Error('Failed to fetch popular spots')
      }
    } catch (err) {
      console.error('Failed to fetch popular spots:', err)
      popularSpotsError.value = 'Failed to fetch popular spots'
      
      // Fallback to random spots (重複除去済みのデータから)
      popularSpots.value = getRandomSpots(5)
    } finally {
      popularSpotsLoading.value = false
    }
  }

  const getPopularSpots = () => {
    if (popularSpots.value.length === 0) {
      fetchPopularSpots()
    }
    return popularSpots.value
  }

  // 検索機能
  const searchSpots = (query: string) => {
    if (!query.trim()) return []
    
    const lowerQuery = query.toLowerCase()
    return spots.value.filter(spot =>
      spot.name.toLowerCase().includes(lowerQuery) ||
      spot.prefecture.toLowerCase().includes(lowerQuery) ||
      spot.category.toLowerCase().includes(lowerQuery) ||
      spot.description.toLowerCase().includes(lowerQuery)
    )
  }

  return {
    spots: readonly(spots),
    loading: readonly(loading),
    error: readonly(error),
    popularSpots: readonly(popularSpots),
    popularSpotsLoading: readonly(popularSpotsLoading),
    popularSpotsError: readonly(popularSpotsError),
    fetchSpots,
    fetchSpotsByPrefecture,
    fetchPopularSpots,
    getSpotsByPrefecture,
    getSpotsByCategory,
    getSpotById,
    getRandomSpots,
    getPopularSpots,
    searchSpots
  }
}
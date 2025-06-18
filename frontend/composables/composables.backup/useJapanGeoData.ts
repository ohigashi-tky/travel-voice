export const useJapanGeoData = () => {
  // 日本の都道府県GeoJSONデータ（簡略化版）
  // 実際の本格的な実装では、以下のような外部データソースを使用します：
  // - https://github.com/dataofjapan/land (MIT License)
  // - 国土地理院の行政区域データ
  // - Natural Earth データ
  
  const japanGeoJson = {
    "type": "FeatureCollection",
    "features": [
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "北海道",
          "ISO_3166_2": "JP-01",
          "NAME_EN": "Hokkaido"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [139.3671875, 45.089035564831015],
            [148.0078125, 45.089035564831015],
            [148.0078125, 41.244772343082076],
            [139.3671875, 41.244772343082076],
            [139.3671875, 45.089035564831015]
          ]]
        }
      },
      {
        "type": "Feature", 
        "properties": {
          "NAME_1": "青森県",
          "ISO_3166_2": "JP-02",
          "NAME_EN": "Aomori"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [139.5, 41.5],
            [141.5, 41.5],
            [141.5, 40.2],
            [139.5, 40.2],
            [139.5, 41.5]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "岩手県",
          "ISO_3166_2": "JP-03", 
          "NAME_EN": "Iwate"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [140.5, 40.2],
            [142.0, 40.2],
            [142.0, 38.8],
            [140.5, 38.8],
            [140.5, 40.2]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "宮城県",
          "ISO_3166_2": "JP-04",
          "NAME_EN": "Miyagi"
        },
        "geometry": {
          "type": "Polygon", 
          "coordinates": [[
            [140.3, 38.8],
            [141.8, 38.8],
            [141.8, 37.8],
            [140.3, 37.8],
            [140.3, 38.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "秋田県",
          "ISO_3166_2": "JP-05",
          "NAME_EN": "Akita"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [139.3, 40.2],
            [140.8, 40.2],
            [140.8, 38.8],
            [139.3, 38.8],
            [139.3, 40.2]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "山形県",
          "ISO_3166_2": "JP-06",
          "NAME_EN": "Yamagata"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [139.5, 38.8],
            [140.6, 38.8],
            [140.6, 37.6],
            [139.5, 37.6],
            [139.5, 38.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "福島県",
          "ISO_3166_2": "JP-07",
          "NAME_EN": "Fukushima"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [139.2, 37.8],
            [141.0, 37.8],
            [141.0, 36.8],
            [139.2, 36.8],
            [139.2, 37.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "茨城県",
          "ISO_3166_2": "JP-08",
          "NAME_EN": "Ibaraki"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [139.7, 36.8],
            [140.8, 36.8],
            [140.8, 35.7],
            [139.7, 35.7],
            [139.7, 36.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "栃木県",
          "ISO_3166_2": "JP-09",
          "NAME_EN": "Tochigi"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [139.0, 36.8],
            [140.2, 36.8],
            [140.2, 35.9],
            [139.0, 35.9],
            [139.0, 36.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "群馬県",
          "ISO_3166_2": "JP-10",
          "NAME_EN": "Gunma"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [138.4, 36.8],
            [139.5, 36.8],
            [139.5, 35.9],
            [138.4, 35.9],
            [138.4, 36.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "埼玉県",
          "ISO_3166_2": "JP-11",
          "NAME_EN": "Saitama"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [138.8, 36.2],
            [140.0, 36.2],
            [140.0, 35.7],
            [138.8, 35.7],
            [138.8, 36.2]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "千葉県",
          "ISO_3166_2": "JP-12",
          "NAME_EN": "Chiba"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [139.7, 36.1],
            [140.9, 36.1],
            [140.9, 34.9],
            [139.7, 34.9],
            [139.7, 36.1]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "東京都",
          "ISO_3166_2": "JP-13",
          "NAME_EN": "Tokyo"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [138.9, 35.9],
            [139.9, 35.9],
            [139.9, 35.5],
            [138.9, 35.5],
            [138.9, 35.9]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "神奈川県",
          "ISO_3166_2": "JP-14",
          "NAME_EN": "Kanagawa"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [138.9, 35.6],
            [139.8, 35.6],
            [139.8, 35.1],
            [138.9, 35.1],
            [138.9, 35.6]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "新潟県",
          "ISO_3166_2": "JP-15",
          "NAME_EN": "Niigata"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [137.6, 38.3],
            [139.9, 38.3],
            [139.9, 36.7],
            [137.6, 36.7],
            [137.6, 38.3]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "富山県",
          "ISO_3166_2": "JP-16",
          "NAME_EN": "Toyama"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [136.8, 37.0],
            [137.9, 37.0],
            [137.9, 36.3],
            [136.8, 36.3],
            [136.8, 37.0]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "石川県",
          "ISO_3166_2": "JP-17",
          "NAME_EN": "Ishikawa"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [136.0, 37.5],
            [137.3, 37.5],
            [137.3, 35.8],
            [136.0, 35.8],
            [136.0, 37.5]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "福井県",
          "ISO_3166_2": "JP-18",
          "NAME_EN": "Fukui"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [135.4, 36.4],
            [136.7, 36.4],
            [136.7, 35.3],
            [135.4, 35.3],
            [135.4, 36.4]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "山梨県",
          "ISO_3166_2": "JP-19",
          "NAME_EN": "Yamanashi"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [138.2, 36.0],
            [139.1, 36.0],
            [139.1, 35.1],
            [138.2, 35.1],
            [138.2, 36.0]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "長野県",
          "ISO_3166_2": "JP-20",
          "NAME_EN": "Nagano"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [137.3, 36.7],
            [138.8, 36.7],
            [138.8, 35.2],
            [137.3, 35.2],
            [137.3, 36.7]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "岐阜県",
          "ISO_3166_2": "JP-21",
          "NAME_EN": "Gifu"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [136.0, 36.6],
            [137.8, 36.6],
            [137.8, 35.2],
            [136.0, 35.2],
            [136.0, 36.6]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "静岡県",
          "ISO_3166_2": "JP-22",
          "NAME_EN": "Shizuoka"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [137.5, 35.4],
            [139.1, 35.4],
            [139.1, 34.6],
            [137.5, 34.6],
            [137.5, 35.4]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "愛知県",
          "ISO_3166_2": "JP-23",
          "NAME_EN": "Aichi"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [136.4, 35.4],
            [137.8, 35.4],
            [137.8, 34.5],
            [136.4, 34.5],
            [136.4, 35.4]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "三重県",
          "ISO_3166_2": "JP-24",
          "NAME_EN": "Mie"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [135.8, 35.2],
            [136.9, 35.2],
            [136.9, 33.7],
            [135.8, 33.7],
            [135.8, 35.2]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "滋賀県",
          "ISO_3166_2": "JP-25",
          "NAME_EN": "Shiga"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [135.7, 35.7],
            [136.4, 35.7],
            [136.4, 34.7],
            [135.7, 34.7],
            [135.7, 35.7]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "京都府",
          "ISO_3166_2": "JP-26",
          "NAME_EN": "Kyoto"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [134.8, 35.8],
            [136.0, 35.8],
            [136.0, 34.7],
            [134.8, 34.7],
            [134.8, 35.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "大阪府",
          "ISO_3166_2": "JP-27",
          "NAME_EN": "Osaka"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [135.2, 34.9],
            [135.8, 34.9],
            [135.8, 34.3],
            [135.2, 34.3],
            [135.2, 34.9]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "兵庫県",
          "ISO_3166_2": "JP-28",
          "NAME_EN": "Hyogo"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [134.0, 35.7],
            [135.4, 35.7],
            [135.4, 34.2],
            [134.0, 34.2],
            [134.0, 35.7]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "奈良県",
          "ISO_3166_2": "JP-29",
          "NAME_EN": "Nara"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [135.4, 34.7],
            [136.1, 34.7],
            [136.1, 33.9],
            [135.4, 33.9],
            [135.4, 34.7]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "和歌山県",
          "ISO_3166_2": "JP-30",
          "NAME_EN": "Wakayama"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [135.0, 34.4],
            [135.9, 34.4],
            [135.9, 33.4],
            [135.0, 33.4],
            [135.0, 34.4]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "鳥取県",
          "ISO_3166_2": "JP-31",
          "NAME_EN": "Tottori"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [133.3, 35.7],
            [134.6, 35.7],
            [134.6, 35.0],
            [133.3, 35.0],
            [133.3, 35.7]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "島根県",
          "ISO_3166_2": "JP-32",
          "NAME_EN": "Shimane"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [131.0, 36.0],
            [133.4, 36.0],
            [133.4, 34.0],
            [131.0, 34.0],
            [131.0, 36.0]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "岡山県",
          "ISO_3166_2": "JP-33",
          "NAME_EN": "Okayama"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [133.0, 35.2],
            [134.5, 35.2],
            [134.5, 34.2],
            [133.0, 34.2],
            [133.0, 35.2]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "広島県",
          "ISO_3166_2": "JP-34",
          "NAME_EN": "Hiroshima"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [132.0, 34.9],
            [133.4, 34.9],
            [133.4, 34.0],
            [132.0, 34.0],
            [132.0, 34.9]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "山口県",
          "ISO_3166_2": "JP-35",
          "NAME_EN": "Yamaguchi"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [130.8, 34.6],
            [132.2, 34.6],
            [132.2, 33.7],
            [130.8, 33.7],
            [130.8, 34.6]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "徳島県",
          "ISO_3166_2": "JP-36",
          "NAME_EN": "Tokushima"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [133.5, 34.4],
            [134.8, 34.4],
            [134.8, 33.5],
            [133.5, 33.5],
            [133.5, 34.4]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "香川県",
          "ISO_3166_2": "JP-37",
          "NAME_EN": "Kagawa"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [133.4, 34.5],
            [134.5, 34.5],
            [134.5, 34.0],
            [133.4, 34.0],
            [133.4, 34.5]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "愛媛県",
          "ISO_3166_2": "JP-38",
          "NAME_EN": "Ehime"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [132.3, 34.2],
            [133.8, 34.2],
            [133.8, 32.8],
            [132.3, 32.8],
            [132.3, 34.2]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "高知県",
          "ISO_3166_2": "JP-39",
          "NAME_EN": "Kochi"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [132.4, 33.8],
            [134.3, 33.8],
            [134.3, 32.7],
            [132.4, 32.7],
            [132.4, 33.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "福岡県",
          "ISO_3166_2": "JP-40",
          "NAME_EN": "Fukuoka"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [129.7, 34.0],
            [131.3, 34.0],
            [131.3, 33.0],
            [129.7, 33.0],
            [129.7, 34.0]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "佐賀県",
          "ISO_3166_2": "JP-41",
          "NAME_EN": "Saga"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [129.5, 33.8],
            [130.4, 33.8],
            [130.4, 32.9],
            [129.5, 32.9],
            [129.5, 33.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "長崎県",
          "ISO_3166_2": "JP-42",
          "NAME_EN": "Nagasaki"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [128.8, 34.7],
            [130.4, 34.7],
            [130.4, 32.2],
            [128.8, 32.2],
            [128.8, 34.7]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "熊本県",
          "ISO_3166_2": "JP-43",
          "NAME_EN": "Kumamoto"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [130.0, 33.3],
            [131.3, 33.3],
            [131.3, 32.2],
            [130.0, 32.2],
            [130.0, 33.3]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "大分県",
          "ISO_3166_2": "JP-44",
          "NAME_EN": "Oita"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [130.9, 33.8],
            [132.0, 33.8],
            [132.0, 32.7],
            [130.9, 32.7],
            [130.9, 33.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "宮崎県",
          "ISO_3166_2": "JP-45",
          "NAME_EN": "Miyazaki"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [130.8, 32.8],
            [131.9, 32.8],
            [131.9, 31.3],
            [130.8, 31.3],
            [130.8, 32.8]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "鹿児島県",
          "ISO_3166_2": "JP-46",
          "NAME_EN": "Kagoshima"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [129.0, 32.3],
            [131.5, 32.3],
            [131.5, 30.0],
            [129.0, 30.0],
            [129.0, 32.3]
          ]]
        }
      },
      {
        "type": "Feature",
        "properties": {
          "NAME_1": "沖縄県",
          "ISO_3166_2": "JP-47",
          "NAME_EN": "Okinawa"
        },
        "geometry": {
          "type": "Polygon",
          "coordinates": [[
            [123.0, 27.0],
            [128.0, 27.0],
            [128.0, 24.0],
            [123.0, 24.0],
            [123.0, 27.0]
          ]]
        }
      }
    ]
  }

  const getJapanGeoJson = () => {
    return japanGeoJson
  }

  const getPrefectureByName = (name: string) => {
    return japanGeoJson.features.find(feature => 
      feature.properties.NAME_1 === name
    )
  }

  // より高精度なGeoJSONデータを外部から取得する関数
  const fetchHighQualityGeoJson = async () => {
    try {
      // 実際の実装では、以下のようなデータソースを使用
      // const response = await fetch('https://raw.githubusercontent.com/dataofjapan/land/master/japan-with-islands.geojson')
      // return await response.json()
      
      // デモ用：現在のデータを返す
      return japanGeoJson
    } catch (error) {
      console.error('高精度GeoJSONデータの取得に失敗:', error)
      return japanGeoJson // フォールバック
    }
  }

  return {
    getJapanGeoJson,
    getPrefectureByName,
    fetchHighQualityGeoJson
  }
}
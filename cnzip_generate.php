<?php
$json = json_decode(file_get_contents("list.json"), true);
$provinces = json_decode(file_get_contents("cnprovince.json"), true);

$ary = [];
foreach ($json as $key => $value) {
    if(preg_match('/(\d\d)0000/', $key, $matches)) {
        $provId = $matches[1];
        $ary[$provId] = array(
            'name' =>  $value,
            'county' => []
        );
    }elseif(preg_match('/(\d\d)(\d\d)00/', $key, $matches)) {
        $provId = $matches[1];
        $countyId = $matches[2];
        $ary[$provId]["county"][$countyId] = array(
            'name' =>  $value,
            'town' => []
        );
    }elseif(preg_match('/(\d\d)(\d\d)(\d\d)/', $key, $matches)) {
        $provId = $matches[1];
        $countyId = $matches[2];
        $townId = $matches[3];
        $ary[$provId]["county"][$countyId]["town"][] = $value;
    }
}

$i = 1;
$data = [
    array(
        'id' => $i++,
        'prov' => '省级名称',
        'county' => '市级',
        'town' => '县区'
    )
];
foreach ($ary as $provId => $prov) {
    $provName = $prov["name"];
    if($provName == "台湾省"){
        continue;
    }
    foreach( $provinces as $province ) {
        $chinese = $province['chinese'];
        if ( mb_substr($provName, 0, 2) == mb_substr($chinese, 0, 2) ) {
            $provName = $chinese;
            break;
        }
    }
    foreach ($prov['county'] as $countyId => $county){
        $countyName = $county["name"] ?? $provName;
        if(count($county['town']) === 0) {
            if ($provName == "广东省" && $countyName == "东莞市") {
                $county['town'] = [
                    "东莞滨海湾新区", "莞城街道", "南城街道", "东城街道", "万江街道",
                    "石碣镇", "石龙镇", "茶山镇", "石排镇", "企石镇",
                    "横沥镇", "桥头镇", "谢岗镇", "东坑镇", "常平镇",
                    "寮步镇", "樟木头镇", "大朗镇", "黄江镇", "清溪镇",
                    "塘厦镇", "凤岗镇", "大岭山镇", "长安镇", "虎门镇",
                    "厚街镇", "沙田镇", "道滘镇", "洪梅镇", "麻涌镇",
                    "望牛墩镇", "中堂镇", "高埗镇", "东莞松山湖科技产业园区", "东莞湾", "东莞生态园"
                ];
            } elseif ($provName == "广东省" && $countyName == "中山市") {
                $county['town'] = [
                    "火炬开发区", "翠亨新区", "石岐街道", "东区街道", "中山港街道",
                    "西区街道", "南区街道", "五桂山街道", "小榄镇", "黄圃镇",
                    "民众街道", "东凤镇", "古镇镇", "沙溪镇", "坦洲镇",
                    "港口镇", "三角镇", "横栏镇", "南头镇", "阜沙镇",
                    "三乡镇", "板芙镇", "大涌镇", "神湾镇", "南朗街道"
                ];
            } elseif ($provName == "海南省" && $countyName == "儋州市") {
                $county['town'] = [
                    "那大镇", "和庆镇", "南丰镇", "大成镇", "雅星镇",
                    "兰洋镇", "光村镇", "木棠镇", "海头镇", "峨蔓镇",
                    "王五镇", "白马井镇", "中和镇", "排浦镇", "东成镇",
                    "新州镇", "洋浦经济开发区"
                ];
            } elseif ($provName == "甘肃省" && $countyName == "嘉峪关市") {
                $county['town'] = [
                    "钢城街道", "雄关街道", "文殊镇", "新城镇", "峪泉镇"
                ];
            }else{
                echo "$provName $countyName has no towns.\n";
            }
        }
        foreach ($county['town'] as $townName) {
            $data[] = array(
                'id' => $i++,
                'prov' => $provName,
                'county' => $countyName,
                'town' => $townName
            );
        }
    }
}

file_put_contents("cnzip.json", json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
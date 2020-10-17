<?php
error_reporting(0);

$DATA_URL = "https://live.kuaishou.com/m_graphql";
$VIDEO_URL = "https://live.kuaishou.com/u/";

#生成随机did,用于请求快手链接的cookie
$did = md5(time() . mt_rand(1,1000000));
#每次请求生成一个随机ip
$rip = Rand_IP();

//随机IP
function Rand_IP(){
	#第一种方法，直接生成
    $ip2id= round(rand(600000, 2550000) / 10000);
    $ip3id= round(rand(600000, 2550000) / 10000);
    $ip4id= round(rand(600000, 2550000) / 10000);
	#第二种方法，随机抽取
    $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");
    $randarr= mt_rand(0,count($arr_1)-1);
    $ip1id = $arr_1[$randarr];
    return $ip1id.".".$ip2id.".".$ip3id.".".$ip4id;
}
function crawl_user($uid) {
    $data = '{"operationName":"privateFeedsQuery","variables":{"principalId":"'.$uid.'","pcursor":"","count":24},"query":"query privateFeedsQuery($principalId: String, $pcursor: String, $count: Int) {\n  privateFeeds(principalId: $principalId, pcursor: $pcursor, count: $count) {\n    pcursor\n    list {\n      id\n      thumbnailUrl\n      poster\n      workType\n      type\n      useVideoPlayer\n      imgUrls\n      imgSizes\n      magicFace\n      musicName\n      caption\n      location\n      liked\n      onlyFollowerCanComment\n      relativeHeight\n      timestamp\n      width\n      height\n      counts {\n        displayView\n        displayLike\n        displayComment\n        __typename\n      }\n      user {\n        id\n        eid\n        name\n        avatar\n        __typename\n      }\n      expTag\n      __typename\n    }\n    __typename\n  }\n}\n"}';
    $headers_web = array(
        'Accept: */*',
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7',
        'Connection: keep-alive',
        'Content-Type: application/json',
        'Host: live.kuaishou.com',
        'Origin: https://live.kuaishou.com',
        'Referer: https://live.kuaishou.com/profile/' .$uid,
        'Pragma: no-cache',
        'Cache-Control: no-cache',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36',
        'X-FORWARDED-FOR:'.$rip,
        'CLIENT-IP:'.$rip,
        'cookie:did=web_'.$did.'; didv='.time().'000;clientid=3; client_key=6589'.rand(1000, 9999),
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['DATA_URL']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_web);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $result;
}

$uid = $_GET['uid'];
//$uid='qm1118668';
//获得该用户所有作品
$content = crawl_user($uid);
//echo $content;
//解析视频类型的作品id列表
$json = json_decode($content, true);
$list = array_map(function($item){
    return $item['id'];
},array_filter($json['data']['privateFeeds']['list'], function($item){
    return $item['workType']=='video';
}));
//随机获取一个视频id
$vid = $list[rand(0,count($list)-1)];
//echo $vid;
if($vid){
    $v_url = $VIDEO_URL.$uid."/".$vid;
    echo $v_url;
    //跳转解析视频地址
    header("Location: /gifshow/api.php?url=".$v_url);
    return;
}
echo ('解析失败');
exit;
?>
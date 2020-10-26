<?php
error_reporting(0);

$DATA_URL = "https://live.kuaishou.com/m_graphql";
$VIDEO_URL = "https://live.kuaishou.com/u/";

//获取cookie
function getCookie($from_github=true){
    if($from_github){
        //首先通过github上别人的项目中的cookie尝试
        $url='https://github.com/LBatsoft/spider/blob/main/gifshow.py';//gifshow.py、gifshow_04.py
        // $raw_url = str_replace('/blob/', '/', str_replace('github.com', 'raw.githubusercontent.com', $url));
        $cdn_url = str_replace('/blob/', '@', str_replace('github.com', 'cdn.jsdelivr.net/gh', $url));
        $content = file_get_contents($cdn_url);
        // print($content);
        $str_r = '/ {4,}[\'|"]Cookie[\'|"]:\s*[\'|"](.*?)[\'|"]/';
        if(preg_match_all($str_r, $content, $arr)){
            return $arr[1][0];
        }
    }

    
    //如果未取到，则通过手动设置的值获取
    $cookies = array(
        "kuaishou.live.bfb1s=477cb0011daca84b36b3a4676857e5a1; clientid=3; did=web_8b1ef0506c146c24627a858c9a646ad2; client_key=65890b29; Hm_lvt_86a27b7db2c5c0ae37fee4a8a35033ee=1600700772; userId=1717892941; WEBLOGGER_INCREAMENT_ID_KEY=1077; WEBLOGGER_HTTP_SEQ_ID=499; didv=1600953928773; sid=9ff1ca2ccca59fd641cf3190; logj=; kuaishou.live.web_st=ChRrdWFpc2hvdS5saXZlLndlYi5zdBKgAYKcjU0ix3GRIWBrIflwAnmB2hP5eJ7ekkhgZbLr-8KvXNdY0ZMAZkSWrvKIm41gMy6dQpDzhX7JcW63mLbvgJznZ4EsFDj-x_RdHaWKJeO2MZKdc0nAwD0BSGKhGGp1Qr04lKMJ4V1PgJ1TU0LPdRTa2ORBK3HKxFNHWKGc2qWygPqEUsV0qgX58JUbOsT5RFoqxWoVYoO2mbGbtIaPOaYaEvK5mSs2Ikx-mdXST2fm99svHCIgWE9UBxFaNVAu_uKY8FRp21fU0zydZkTVmviNS3vI8W0oBTAB; kuaishou.live.web_ph=f6b49f9ff9d05829e38fe8802fa1e233f600; userId=1717892941; Hm_lpvt_86a27b7db2c5c0ae37fee4a8a35033ee=1602384880; ktrace-context=1|MS42OTc4Njg0NTc2NzM4NjY5LjIxODgxODk2LjE2MDI0MDIxNjM0MzMuMTEyODY5MA==|MS42OTc4Njg0NTc2NzM4NjY5LjExOTc5NTU3LjE2MDI0MDIxNjM0MzMuMTEyODY5MQ==|0|kuaishou-frontend-live|webservice|false|NA"
        ,
        // 'did=web_'. md5(time() . mt_rand(1,1000000)) .'; didv='.time().'000;clientid=3; client_key=6589'.rand(1000, 9999),

    );
    return $cookies[array_rand($cookies)];
}

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

//获取某用户下的作品列表
function crawl_user($uid, $cookie_from_github=true) {
    // print('=');
    #每次请求生成一个随机ip
    $rip = Rand_IP();
    $cookie = getCookie($cookie_from_github);
    // echo $cookie;
    
    $data = '{"operationName":"privateFeedsQuery","variables":{"principalId":"'.$uid.'","pcursor":"","count":99},"query":"query privateFeedsQuery($principalId: String, $pcursor: String, $count: Int) {\n  privateFeeds(principalId: $principalId, pcursor: $pcursor, count: $count) {\n    pcursor\n    list {\n      id\n      thumbnailUrl\n      poster\n      workType\n      type\n      useVideoPlayer\n      imgUrls\n      imgSizes\n      magicFace\n      musicName\n      caption\n      location\n      liked\n      onlyFollowerCanComment\n      relativeHeight\n      timestamp\n      width\n      height\n      counts {\n        displayView\n        displayLike\n        displayComment\n        __typename\n      }\n      user {\n        id\n        eid\n        name\n        avatar\n        __typename\n      }\n      expTag\n      __typename\n    }\n    __typename\n  }\n}\n"}';
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
        'X-Real-IP:'.$rip,
        'CLIENT-IP:'.$rip,
        'Cookie: '. $cookie,
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
//解析视频类型的作品id列表
$json = json_decode($content, true);
$list = array_map(function($item){
    return $item['id'];
},array_filter($json['data']['privateFeeds']['list'], function($item){
    echo item['user'];
    return $item['workType']=='video' && item['user']['id'] === $uid;
}));

if(count($list)==0){
    $content = crawl_user($uid, false);
    //解析视频类型的作品id列表
    $json = json_decode($content, true);
    $list = array_map(function($item){
        return $item['id'];
    },array_filter($json['data']['privateFeeds']['list'], function($item){
        return $item['workType']=='video' && item['user']['id'] === $uid;
    }));
}
print("获取作品个数：". count($list)."\n");

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
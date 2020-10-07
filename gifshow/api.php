<?php
error_reporting(0);

$DATA_URL = "https://live.kuaishou.com/m_graphql";
$VIDEO_URL = "https://live.kuaishou.com/u/";
$PROFILE_URL = "https://live.kuaishou.com/profile/";
$WORK_URL = "https://m.gifshow.com/fw/photo/";
$COOKIE = "did=web_2e1d7aff6c8c4051a69560f52ed3acdf; didv=1599281802000; userId=811386833; kuaishou.live.web_st=ChRrdWFpc2hvdS5saXZlLndlYi5zdBKgAU-Feb108FoJTyeUrEncf1CSNYSaV6s4AndruRYSqwNElJXlMR0LkLq72Dcp9DEbUlT-a5fUIH8AUB1DdZ7bZgCtDMCPHqVoZLMBdZI0HizKMhoIG7bDdvY8IprQ9JSLgw5R7taQSeOYGbvMUgEaK1fVPUe52PNqCt1NKtRR4ELEKnfXFqC1JTDAv1GbQwglmL_6uYb1FkdfaI262fSmz5AaEjGueioax06vmORaF3eBQr3cQSIg7flK8V4FItjvh4noW-xqkaa1lmZPyCKRsaGx-c-0aRooBTAB;";
$COOKIE_MOBILE = "did=web_46ed8fb010564af086bf3109224266d1; didv=1602100840000; sid=5c373d9347cc606e2ae349b8; Hm_lvt_86a27b7db2c5c0ae37fee4a8a35033ee=1602100843; clientid=3; client_key=65890b29; Hm_lpvt_86a27b7db2c5c0ae37fee4a8a35033ee=1602100936";

function crawl_user($uid) {
    $data = '{"operationName":"privateFeedsQuery","variables":{"principalId":"'.$uid.'","pcursor":"","count":24},"query":"query privateFeedsQuery($principalId: String, $pcursor: String, $count: Int) {\n  privateFeeds(principalId: $principalId, pcursor: $pcursor, count: $count) {\n    pcursor\n    list {\n      id\n      thumbnailUrl\n      poster\n      workType\n      type\n      useVideoPlayer\n      imgUrls\n      imgSizes\n      magicFace\n      musicName\n      caption\n      location\n      liked\n      onlyFollowerCanComment\n      relativeHeight\n      timestamp\n      width\n      height\n      counts {\n        displayView\n        displayLike\n        displayComment\n        __typename\n      }\n      user {\n        id\n        eid\n        name\n        avatar\n        __typename\n      }\n      expTag\n      __typename\n    }\n    __typename\n  }\n}\n"}';
    $headers_web = array(
        'accept: */*',
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7',
        'Connection: keep-alive',
        'Content-Type: application/json',
        'Host: live.kuaishou.com',
        'Origin: https://live.kuaishou.com',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36',
        'Cookie: '.$GLOBALS['COOKIE'],
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

/*返回一个302地址*/
function  curl_302($url) {
    $ch = curl_init();
    
    $headers = array(
        'Connection: keep-alive',
        'Pragma: no-cache',
        'Cache-Control: no-cache',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Linux; U; Android 4.0; en-us; GT-I9300 Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'Sec-Fetch-Site: none',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Dest: document',
        'Accept-Language: zh-CN,zh;q=0.9',
        'Cookie: did=web_46ed8fb010564af086bf3109224266d1; didv=1602100840000; sid=5c373d9347cc606e2ae349b8; Hm_lvt_86a27b7db2c5c0ae37fee4a8a35033ee=1602100843; clientid=3; client_key=65890b29; Hm_lpvt_86a27b7db2c5c0ae37fee4a8a35033ee=1602100936',
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    curl_setopt($ch,  CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL,  $url);
    curl_setopt($ch, CURLOPT_GET, 1);
    curl_setopt($ch,  CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
    
    $data = curl_exec($ch);
    $Headers =  curl_getinfo($ch);
    curl_close($ch);
    if ($data != $Headers)
        return  $Headers["url"];
    else
        return false;

}

//无水印解析
function crawl_video($vid) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $GLOBALS['WORK_URL'].$vid);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    
    $headers = array(
        'Connection: keep-alive',
        'Pragma: no-cache',
        'Cache-Control: no-cache',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Linux; U; Android 4.0; en-us; GT-I9300 Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'Sec-Fetch-Site: none',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-User: ?1',
        'Sec-Fetch-Dest: document',
        'Accept-Language: zh-CN,zh;q=0.9',
        'Cookie:'.$GLOBALS['COOKIE_MOBILE'],
    );
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    curl_close($ch);
    
    return $result;
}

//有水印解析
function crawl_video2($uid, $vid) {
    $data = '{"operationName":"SharePageQuery","variables":{"photoId":"'.$vid.'","principalId":"'.$uid.'"},"query":"query SharePageQuery($principalId: String, $photoId: String) {\n  feedById(principalId: $principalId, photoId: $photoId) {\n    currentWork {\n      playUrl\n      __typename\n    }\n    __typename\n  }\n}\n"}';

    $headers_web = array(
        'accept: */*',
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7',
        'Connection: keep-alive',
        'Content-Type: application/json',
        'Host: live.kuaishou.com',
        'Origin: https://live.kuaishou.com',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36',
        'Cookie: '.$GLOBALS['COOKIE'],
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['DATA_URL']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_web);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $result;
}

function zz_video_url($content){
    $str_r = '/"srcNoMark":"(.*?\.mp4.*?)"/';
    preg_match_all($str_r, $content, $arr);
    return $arr[1][0];
}
function zz_video_url2($content){
    $json = json_decode($content, true);
    return $json['data']['feedById']['currentWork']['playUrl'];
}

$url = $_GET['url'];
//$url='https://live.kuaishou.com/u/Xiaobaiyang31/3xuqi3z6wwbzryw';
$uid;
$vid;
if(strpos($url,'/u/') == false){
    $url = curl_302($url);
    $url = explode('?', $url);
    //无水印解析
    $str_r = '/\/fw\/photo\/(.*)/';
    preg_match_all($str_r, $url[0], $arr);
    $vid = $arr[1][0];
}else{
    $url = explode('?', $url);
    $str_r = '/\/u\/(.*?)\/(.*)/';
    preg_match_all($str_r, $url[0], $arr);
    $uid = $arr[1][0];
    $vid = $arr[2][0];
}
//解析视频直连
//$content = crawl_video2($uid, $vid);
$content = crawl_video($vid);
// print($content);
$v_url = zz_video_url($content);
print($v_url);
header("Location: ".$v_url);
exit;
?>

<?php
error_reporting(0);

#接受所有请求源
header('Access-Control-Allow-Origin:*');

/*返回一个302地址*/
function get_302($url){
  $header = get_headers($url,1);
  if (strpos($header[0],'301') || strpos($header[0],'302')) {
     if(is_array($header['Location'])) {
         return $header['Location'][count($header['Location'])-1];
     }else{
         return $header['Location'];
     }
  }else {
     return $url;
  }
}

$url = $_GET['url'];
$type = $_GET['type'];
$v_url = get_302($url);
if($type && $type="302"){
  header("Location: ".$v_url);
  //header("Content-Type: video/mp4");
  return;
}
echo $v_url;
exit;
?>

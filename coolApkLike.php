<?php
header("content-type:application/json");
require "../include/functions.php";
// if(!isset($_GET['test']))
// exit;
$cookie = "uid=3718528; username=%E8%90%BD%E9%9B%A8%E9%85%B1; token=fa010fd9McEEDUhCQsZZM9OabuTy-6IIMMs1N4uppaxCkW_2YAJK2Etx70yRg_L0Rplf3zulkYIbKfEXPxVsVaS60s8JC8LPV4InQFWhbBj_k7nzTCLgoYL2F2zwJSEKnPxCYGlX1Shw0VeyqWfItDc5dGlIY_6HhbPhEzttNDqVRglDMKVmRLeeFS-ZUno2frRDMwyoD4Z259JJPlkpfPRQWHt05w";
// $cookie[] = "uid=925239; username=%E7%A5%AD%E5%A4%9C; token=f7188c7aHNPNiTfe4ioowrRIYP1kXXE5s7j02wjz8hRUr9cTmcpNSg7oUAwW-ttA5tPQvuV2MJ0h0lghoLurVw2Fj1_5qfHTjUDENsPcvCFJvVIX-rGckRsu1J_Z2lwZyfXjSVWtR9LDKNmmF28rgdAymnmKfjsHeFF9EAW_X5J11VxJOpEHok_ctzyIpae9Vv4fifQtAE5QSJ71wiTqJwEH-zUMPQ";
CoolAPK_like($cookie);

function CoolAPK_like($cookie)
{
    $list = CoolAPK_getTList($cookie);
    exit;
    $token = CoolAPK_getAppToken();
    $http = new EasyHttp();
    foreach ($list as $value) {
        sleep(1);
        $response = $http->request("https://api.coolapk.com/v6/feed/like?id={$value}&detail=0", array(
            'method' => 'POST',        //	GET/POST
            'timeout' => 5,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => null,
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $cookie,
                "X-Requested-With" => "XMLHttpRequest",
                "X-App-Id" => "com.coolapk.market",
                "X-App-Token" => $token
                ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            'body' => null,
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        if(is_object($response))
            continue;
        echo "\r\n--->" . $response['body'] . "{$value['id']}\r\n";
    }
}

function CoolAPK_getTList($cookie)
{
    // 最新的 https://api.coolapk.com:443/v6/page/dataList?url=%23%2Ffeed%2FdigestList%3Ftype%3D0%252C5%252C12%252C10%252C11%252C13%252C8%252C9%26message_status%3D98%252C99%252C100&title=%E6%9C%80%E6%96%B0%E5%8A%A8%E6%80%81&page=1&firstItem=18608452
    // 关注的 https://api.coolapk.com/v6/page/dataList?url=%2Fpage%3Furl%3DV9_HOME_TAB_FOLLOW&title=%E5%85%A8%E9%83%A8%E5%85%B3%E6%B3%A8&page=1&firstItem=8599
    $url = "https://api.coolapk.com/v6/page/dataList?url=%2Fpage%3Furl%3DV9_HOME_TAB_FOLLOW&title=%E5%85%A8%E9%83%A8%E5%85%B3%E6%B3%A8&page=";
    
    $token = CoolAPK_getAppToken();
    $list = array();
    $http = new EasyHttp();
    for($i = 1; $i <= 2; $i++){
        $response = $http->request($url . $i, array(
            'method' => 'GET',        //	GET/POST
            'timeout' => 5,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5.2-beta1-2008191",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $cookie,
                "X-Requested-With" => "XMLHttpRequest",
                "X-App-Id" => "com.coolapk.market",
                "X-App-Token" => $token,
                "X-App-Device" => "wIjNYBSZMByOvNWRlxEI7UGbpJ2bNVGTgsDMCpTN5oDR3ojRzozMCpDMEByO0gTMwETOwcTO3EDMwYDNgsDbsVnbgsDM4MjY1QWY5YTYwMjM1MzY",
                "X-App-Code" => "2008191",
                "X-Api-Version" => "10",
                "X-App-Version" => "10.5.2-beta1",
                "X-Sdk-Int" => "23",
                "X-Sdk-Locale" => "zh-CN",
                "X-forward-by" => "127.0.0.1"
                ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            'body' => null,
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        if(is_object($response))
        {    
            continue;
        }
        print_r($response['body']);
        exit;
        $body = json_decode($response['body'], true)['data'];
        unset($body[0]);
        foreach($body as $value)
        {
            if($value['entityType'] == "configCard" || strpos($value["recent_like_list"], "落雨酱") !== false)
                continue;
            $list[] = $value['id'];
        }
    }
    // print_r($list);
    // exit;
    return $list;
}

function CoolAPK_getAppToken($device_id = "d4924003-1650-3566-91bb-9e7502351c7f", $time = null)
{
    if(!$time)
        $time = time();
     # 时间戳加密
    $md5_t = md5(strval($time));
    
    // # 不知道什么鬼字符串拼接
    $a = "token://com.coolapk.market/c67ef5943784d09750dcfbb31020f0ab?{$md5_t}\${$device_id}&com.coolapk.market";
    
    // # 不知道什么鬼字符串拼接 后的字符串再次加密
    $md5_a = md5(base64_encode($a));
    $token = $md5_a . $device_id . '0x' . dechex($time);
    return $token;
}


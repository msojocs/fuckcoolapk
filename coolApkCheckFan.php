<?php
header("content-type:application/json");
require "../include/functions.php";
$cookie = "uid=3718528; username=%E8%90%BD%E9%9B%A8%E9%85%B1; token=fa010fd9McEEDUhCQsZZM9OabuTy-6IIMMs1N4uppaxCkW_2YAJK2Etx70yRg_L0Rplf3zulkYIbKfEXPxVsVaS60s8JC8LPV4InQFWhbBj_k7nzTCLgoYL2F2zwJSEKnPxCYGlX1Shw0VeyqWfItDc5dGlIY_6HhbPhEzttNDqVRglDMKVmRLeeFS-ZUno2frRDMwyoD4Z259JJPlkpfPRQWHt05w";

CoolAPK_f($cookie);
// CoolAPK_uf($cookie);

// 关注粉丝
function CoolAPK_f($cookie)
{
    $http = new EasyHttp();
    $body = array();
    $token = CoolAPK_getAppToken();
    $response = $http->request("https://api.coolapk.com/v6/user/fansList?uid=3718528&page=1", array(
        'method' => 'GET',        //	GET/POST
        'timeout' => 10,            //	超时的秒数
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
        return;
    $body = json_decode($response['body'], true);
    // var_dump($body);
    if(empty($body['data']))
        return;
    foreach ($body['data'] as $value) {
        if($value['isfriend'] === 0)
        {
            // var_dump($value);
            CoolAPK_follow($cookie, $value['uid']);
            echo $value['username'];
        }
    }
}

// 取消关注非粉丝人员
function CoolAPK_uf($cookie)
{
    $http = new EasyHttp();
    $body = array();
    $i = 1;
    while(true)
    {
        $token = CoolAPK_getAppToken();
        $response = $http->request("https://api.coolapk.com/v6/user/followList?uid=3718528&page={$i}", array(
            'method' => 'GET',        //	GET/POST
            'timeout' => 10,            //	超时的秒数
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
        $i++;
        if(is_object($response))
            continue;
        $body = json_decode($response['body'], true);
        if(empty($body['data']))
            break;
        foreach ($body['data'] as $value) {
            // var_dump($value);
            if($value['isfriend'] === 0)
            {
                CoolAPK_unfollow($cookie, $value['fuid']);
                echo $value['fusername'];
            }
        }
        // var_dump($body);
        $i++;
    }
}

function CoolAPK_unfollow($cookie, $uid)
{
    $token = CoolAPK_getAppToken();
    $http = new EasyHttp();
    $response = $http->request("https://api.coolapk.com/v6/user/unfollow?uid=" . $uid, array(
        'method' => 'POST',        //	GET/POST
        'timeout' => 10,            //	超时的秒数
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
    // $body = json_decode($response['body'], true);
    // var_dump($response);
}

function CoolAPK_follow($cookie, $uid)
{
    $token = CoolAPK_getAppToken();
    $http = new EasyHttp();
    $response = $http->request("https://api.coolapk.com/v6/user/follow?uid=" . $uid, array(
        'method' => 'POST',        //	GET/POST
        'timeout' => 10,            //	超时的秒数
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
    // $body = json_decode($response['body'], true);
    // var_dump($response);
}

function CoolAPK_getAppToken($device_id = "d4964063-1660-35c6-91bb-9e7502351c7f", $time = null)
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
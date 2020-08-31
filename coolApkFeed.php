<?php
header("content-type:application/json");
require "../include/functions.php";
$cookie = "uid=3514543; username=%E5%8D%83%E4%BB%A3%E9%85%B1; token=06806e9cAopmmyudxVLWiLr3lgVHWxPGsqHgKEvEXlZAvyF1tQbfqPKo9eHDqm9sC9SNj7tiQY97_vtCxrpqW6T7NQNG6UwkIkQz56aB0pP8PorGh8fuP3nXHKE3XZYZZqwXhUBuDjxzJO8Py1akHMIjMQxuK1lxxmObFHVkkq-vf2I_0rnlQtpweVYqAyx4YN5OXzcAp-01yL9KeW30gjjxVHkF6w";
if (isset($_GET['test'])) {
    // $msg = array(
    //     "msg" => "#二次元# #东方project# \r\n[doge呵斥] 来源：https://wall.alphacoders.com/",
    //     "img" => array(
    //         "http://fq.fh.jysafe.cn/getImg/f052607f93c22f52cd1390b40c19a6ec.png",
    //         "http://fq.fh.jysafe.cn/getImg/73ef8a568a7f0f13a2c70f5da253eb32.png",
    //         "http://fq.fh.jysafe.cn/getImg/8b7fa519012fa8519f3ea1e6a4f2640f.jpeg",
    //         "http://fq.fh.jysafe.cn/getImg/349b9ded273ba97416fe8555e35c4ffe.jpeg"
    //         )
    //     );
    $reply = array(
        "id" => "20855004",
        "type" => "feed"
        );
    $coolapk = new CoolApk($cookie);
    $msg = array(
        "msg" => "[cos滑稽]",
        "img" => "http://coolapk.jysafe.cn/getImg/3d28da5abe3ba02da6d441aaf75fd293.jpeg"
        );
    $coolapk->feedReply($reply, $msg);
    // $coolapk->createPicFeed($msg);
    // $coolapk->createFeed($msg);
}

function deviceFeed($cookie) {
    $device = strrev(base64_encode("6302c1f48d4c38a6; null; 460017970910184; D0:B3:3F:7D:95:B0; LeMobile; LeEco; Le X620"));
    $http = new EasyHttp();
    $body = array();
    $token = CoolAPK_getAppToken();
    $response = $http->request("https://m.coolapk.com/mp/do?c=userDevice&m=deviceFeedback", array(
        'method' => 'POST',        //	GET/POST
        'timeout' => 10,            //	超时的秒数
        'redirection' => 5,        //	最大重定向次数
        'httpversion' => '1.1',    //	1.0/1.1
        'user-agent' => "Mozilla/5.0 (Linux; Android 6.0; Le X620 Build/HEXCNFN5902012151S; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/49.0.2623.91 Mobile Safari/537.36 (#Build; 酷安服务器; 酷安服务器; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5",
        'blocking' => true,        //	是否阻塞
        'headers' => array(
            "cookie" => $cookie . ";SESSID=b4d0-5f2f8e0619e04-1596952070-1024",
            "X-Requested-With" => "com.coolapk.market",
            // "X-App-Id" => "com.coolapk.market",
            // "X-App-Token" => $token,
            // "X-App-Device" => $device,
            // "X-App-Version" => "10.5",
            // "X-App-Code" => "2008061",
            // "X-Api-Version" => "10"
        ),    //	header信息
        'cookies' => null,    //	关联数组形式的cookie信息
        // 'cookies' => $cookies,
        'body' => "requestHash=dfeca605qes83q&submit=1&device_title=酷安服务器",
        'compress' => false,    //	是否压缩
        'decompress' => true,    //	是否自动解压缩结果
        'sslverify' => true,
        'stream' => false,
        'filename' => null        //	如果stream = true，则必须设定一个临时文件名
    ));
    if (is_object($response))
        return;
    // $body = json_decode($response['body'], true);
    print_r($response['body']);
}
function deviceFeedP($cookie) {
    $device = strrev(base64_encode("6302c1f48d4c38a6; null; 460017970910184; D0:B3:3F:7D:95:B0; LeMobile; LeEco; Le X620"));
    $http = new EasyHttp();
    $body = array();
    $token = CoolAPK_getAppToken();
    $response = $http->request("https://m.coolapk.com/mp/do?c=userDevice&m=deviceFeedback", array(
        'method' => 'GET',        //	GET/POST
        'timeout' => 10,            //	超时的秒数
        'redirection' => 5,        //	最大重定向次数
        'httpversion' => '1.1',    //	1.0/1.1
        'user-agent' => "Mozilla/5.0 (Linux; Android 6.0; Le X620 Build/HEXCNFN5902012151S; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/49.0.2623.91 Mobile Safari/537.36 (#Build; 酷安服务器; 酷安服务器; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5",
        'blocking' => true,        //	是否阻塞
        'headers' => array(
            "cookie" => $cookie,
            "X-Requested-With" => "com.coolapk.market",
            // "X-App-Id" => "com.coolapk.market",
            // "X-App-Token" => $token,
            // "X-App-Device" => $device,
            // "X-App-Version" => "10.5",
            // "X-App-Code" => "2008061",
            // "X-Api-Version" => "10"
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
    if (is_object($response))
        return;
    var_dump($response);
    // print_r($response['body']);
}
// 机型
function phone($cookie) {
    $device = strrev(base64_encode("6302c1f48d4c38a6; null; 460017970910184; D0:B3:3F:7D:95:B0; LeMobile; LeEco; Le X620"));
    $http = new EasyHttp();
    $body = array();
    $token = CoolAPK_getAppToken();
    $response = $http->request("https://m.coolapk.com/mp/do?c=userDevice&m=myDevice", array(
        'method' => 'GET',        //	GET/POST
        'timeout' => 10,            //	超时的秒数
        'redirection' => 5,        //	最大重定向次数
        'httpversion' => '1.1',    //	1.0/1.1
        'user-agent' => "Mozilla/5.0 (Linux; Android 6.0; Le X620 Build/HEXCNFN5902012151S; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/49.0.2623.91 Mobile Safari/537.36 (#Build; 酷安服务器; 酷安服务器; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5",
        'blocking' => true,        //	是否阻塞
        'headers' => array(
            "cookie" => $cookie,
            "X-Requested-With" => "com.coolapk.market",
            // "X-App-Id" => "com.coolapk.market",
            // "X-App-Token" => $token,
            // "X-App-Device" => $device,
            // "X-App-Version" => "10.5",
            // "X-App-Code" => "2008061",
            // "X-Api-Version" => "10"
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
    if (is_object($response))
        return;
    // $body = json_decode($response['body'], true);
    print_r($response['body']);
}

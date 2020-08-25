<?php
class CoolApk{
    private $cookie;
    private $user;
    private $pass;
    
    // 初始化
    function __construct()
    {
        $num = func_num_args();
        if($num == 1)
            $this->cookie = func_get_arg(0);
        else if($num == 2){
            $this->user = func_get_arg(0);
            $this->pass = func_get_arg(1);
        }else
            exit("参数个数非法！");
    }
    /**
     * 发表回复
     * 
     * @param $feedId string 要回复的[动态/回复]的id
     * @param $msg array(
     *          "msg" 要回复的内容
     *          "img" 要回复的图片链接
     *          ) 
     * 
    */
    public function feedReply($reply, $msg) {
        $maker = isset($_POST['maker'])?$_POST['maker']:"LeMobile";
        $brand = isset($_POST['brand'])?$_POST['brand']:"LeEco";
        $model = isset($_POST['model'])?$_POST['model']:"Le X620";
        $device = strrev(base64_encode(mt_rand(0, 6666) . "; null;" . mt_rand(0, 999999) . "; 00:B3:3F:7D:95:B0; {$maker}; {$brand}; {$model}"));
        $http = new EasyHttp();
        $body = array();
        $token = self::getAppToken();
        $response = $http->request("https://api.coolapk.com/v6/feed/reply?id={$reply['id']}&type={$reply['type']}", array(
            'method' => 'POST',        //	GET/POST
            'timeout' => 10,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5-2008061",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $this->cookie,
                "X-Requested-With" => "XMLHttpRequest",
                "X-App-Id" => "com.coolapk.market",
                "X-App-Token" => $token,
                "X-App-Device" => $device,
                "X-App-Version" => "10.5",
                "X-App-Code" => "2008061",
                "X-Api-Version" => "10",
                "Content-Type" => "multipart/form-data; boundary=123-123-123-123"
            ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            'body' => self::createFormData("123-123-123-123", $msg),
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        if (is_object($response))
            return;
        // $body = json_decode($response['body'], true);
        echo $response['body'];
    }
    
    /**
     * 创建请求数据
     * 
     * @param $boundary
     * @param $msg array(
     *          "msg" 要回复的内容
     *          "img" 要回复的图片链接
     *          ) 
    */
    private function createFormData($boundary, $msg) {
        /*--ee696895-4f41-4a30-9c5d-78fe8737b9f5
    Content-Disposition: form-data; name="message"
    Content-Length: 12
    
    厕所宿舍
    --ee696895-4f41-4a30-9c5d-78fe8737b9f5
    Content-Disposition: form-data; name="pic"
    Content-Length: 82
    
    http://image.coolapk.com/feed/2020/0810/06/3514543_e46bb43c_1015_4231@120x120.jpeg
    --ee696895-4f41-4a30-9c5d-78fe8737b9f5--*/
        $reply = "";
        if (isset($msg['msg'])) {
            $reply .= "--{$boundary}\n";
            $reply .= "Content-Disposition: form-data; name=\"message\"\nContent-Length: " . strlen($msg['msg']) . "\n\n{$msg['msg']}\n";
        }
        if (isset($msg['img'])) {
            $reply .= "--{$boundary}\n";
            $reply .= "Content-Disposition: form-data; name=\"pic\"\nContent-Length: " . strlen($msg['img']) . "\n\n{$msg['img']}\n";
        }
        $reply .= "--{$boundary}--";
        return $reply;
    }
    private function createFormData2($con, $boundary) {
        $formData = "";
        $formData .= "--{$boundary}\n";
        if($con['type'] == "text")
            $formData .= "Content-Disposition: form-data; name=\"{$con['name']}\"\nContent-Length: " . strlen($con['text']) . "\n\n{$con['text']}\n";
        elseif($con['type'] == "file")
            $formData .= "Content-Disposition: form-data; name=\"{$con['name']}\"; filename=\"{$con['file']['name']}\"\nContent-Type: {$con['file']['type']}\nContent-Length: " . filesize($con['file']['path']) . "\n\n" . file_get_contents($con['file']['path']) . "\n";
        $formData .= "--{$boundary}--";
        return $formData;
    }
    
    // 发布动态
    public function createFeed($msg) {
        $maker = isset($_POST['maker'])?$_POST['maker']:"LeMobile";
        $brand = isset($_POST['brand'])?$_POST['brand']:"LeEco";
        $model = isset($_POST['model'])?$_POST['model']:"Le X620";
        $device = strrev(base64_encode(mt_rand(0, 6666) . "; null;" . mt_rand(0, 999999) . "; 00:B3:3F:7D:95:B0; {$maker}; {$brand}; {$model}"));
        // print_r($_POST);
        // exit("测试中。。。");
        $token = self::getAppToken();
        $http = new EasyHttp();
        $body = array();
        $response = $http->request("https://api.coolapk.com/v6/feed/createFeed", array(
            'method' => 'POST',        //	GET/POST
            'timeout' => 10,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5-2008061",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $this->cookie,
                "X-Requested-With" => "XMLHttpRequest",
                "X-App-Id" => "com.coolapk.market",
                "X-App-Token" => $token,
                "X-App-Device" => $device,
                "X-App-Version" => "10.5",
                "X-App-Code" => "2008061",
                "X-Api-Version" => "10"
            ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            'body' => "message={$msg['msg']}&type=feed&is_html_article=0&pic={$msg['img']}&status=1&location=酷安服务器&long_location=酷安服务器&latitude=0.0&longitude=0.0&media_url=&media_type=0&media_pic=&message_title=&message_brief=&extra_title=&extra_url=&extra_key=&extra_pic=&extra_info=&message_cover=&disallow_repost=0&is_anonymous=0&is_editInDyh=0&forwardid=&fid=&dyhId=&targetType=&productId=&province=酷安服务器&city_code=&province=&city_code=&targetId=&location_city=酷安服务器&location_country=酷安服务器&disallow_reply=0&vote_score=0&replyWithForward=0&media_info=&insert_product_media=0",
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        if (is_object($response))
            return;
        $body = json_decode($response['body'], true);
        if(isset($body['data'])){
            $arr = array(
                "status" => 200,
                "msg" => "postType{$body['data']['entityType']}"
                );
            echo json_encode($arr);
        }else{
            $arr = array(
                "status" => 201,
                "msg" => $body
                );
            echo json_encode($arr);;
        }
        // logInfo($response['body']);
        exit;
    }

    // 发布酷图
    public function createPicFeed($msg) {
        $pic = "";
        $i = 0;
        foreach ($msg['img'] as $value) {
            $pic .= "{$value},";
            $i++;
            if($i >= 9)break;
        }
        // 设备处理
        $maker = isset($_POST['maker'])?$_POST['maker']:"LeMobile";
        $brand = isset($_POST['brand'])?$_POST['brand']:"LeEco";
        $model = isset($_POST['model'])?$_POST['model']:"Le X620";
        $device = strrev(base64_encode(mt_rand(0, 6666) . "; null;" . mt_rand(0, 999999) . "; 00:B3:3F:7D:95:B0; {$maker}; {$brand}; {$model}"));
        $http = new EasyHttp();
        $body = array();
        $token = self::getAppToken();
        $response = $http->request("https://api.coolapk.com/v6/feed/createFeed", array(
            'method' => 'POST',        //	GET/POST
            'timeout' => 10,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5-2008061",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $this->cookie,
                "X-Requested-With" => "XMLHttpRequest",
                "X-App-Id" => "com.coolapk.market",
                "X-App-Token" => $token,
                "X-App-Device" => $device,
                "X-App-Version" => "10.5",
                "X-App-Code" => "2008061",
                "X-Api-Version" => "10"
            ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            'body' => "message={$msg['msg']}&type=picture&is_html_article=0&pic={$pic}&status=1&location=酷安服务器&long_location=酷安服务器&latitude=0.0&longitude=0.0&media_url=&media_type=0&media_pic=&message_title=&message_brief=&extra_title=&extra_url=&extra_key=&extra_pic=&extra_info=&message_cover=&disallow_repost=0&is_anonymous=0&is_editInDyh=0&forwardid=&fid=&dyhId=&targetType=&productId=&province=&city_code=&province=&city_code=&targetId=&location_city=&location_country=&disallow_reply=0&vote_score=0&replyWithForward=0&media_info=&insert_product_media=0",
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        if (is_object($response))
            return;
        $body = json_decode($response['body'], true);
        if(isset($body['data'])){
            $arr = array(
                "status" => 200,
                "msg" => "postType{$body['data']['entityType']}"
                );
            echo json_encode($arr);
        }else{
            $arr = array(
                "status" => 201,
                "msg" => $body
                );
            echo json_encode($arr);;
        }
        logInfo($response['body']);
        exit;
    }

    public function login()
    {
        $http = new EasyHttp();
        $captcha = "";
        $login_cookie = "";
        $requstHash = "";
        if(func_num_args() == 0)
        {
             // 登录页面
            $loginPage = $http->request("https://account.coolapk.com/auth/loginByCoolapk", array(
                'method' => 'GET',        //	GET/POST
                'timeout' => 10,            //	超时的秒数
                'redirection' => 5,        //	最大重定向次数
                'httpversion' => '1.1',    //	1.0/1.1
                'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902012161S release-keys; 6.0) +CoolMarket/10.5-2008061",
                'blocking' => true,        //	是否阻塞
                'headers' => array(
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
            // 获取参数
            $body = $loginPage['body'];
            preg_match("/requestHash : '(.*)'/U", $body, $requstHash);
            $requstHash = $requstHash[1];
            $login_cookie = explode(";", $loginPage['headers']['set-cookie'][0])[0];
            // exit($login_cookie);
            if(strpos($body, '<div id="captchaGroup" class="weui-cell weui-cell_vcode" style="display: flex;">') !== false)
            {
                // 有验证码
                $ret = array(
                    "status" => 405,
                    "ret" => array(
                        "sessid" => $login_cookie,
                        "hash" => $requstHash,
                        "captcha" => self::getCaptcha($login_cookie)
                        )
                    );
                echo json_encode($ret);
                exit;
            }
        }else{
            // 存在参数，是需要验证码情形
            $login_cookie = func_get_arg(0);
            $requstHash = func_get_arg(1);
            $captcha = func_get_arg(2);
        }
        // 登录请求
        $loginReq = $http->request("https://account.coolapk.com/auth/loginByCoolapk", array(
            'method' => 'POST',        //	GET/POST
            'timeout' => 10,            //	超时的秒数
            'redirection' => 0,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902012161S release-keys; 6.0) +CoolMarket/10.5-2008061",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $login_cookie . "; forward=https%3A%2F%2Fwww.coolapk.com",
                "Content-Type" => "application/x-www-form-urlencoded",
                "Referer" => "https://account.coolapk.com/auth/loginByCoolapk",
                "x-requested-with" => "XMLHttpRequest"
            ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            'body' => "submit=1&requestHash={$requstHash}&login={$this->user}&password={$this->pass}&captcha={$captcha}&randomNumber=0undefined597506476848674",
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        $ret = json_decode($loginReq['body']);
        if($ret->message === "图形验证码错误"){
            $ret = array(
                    "status" => 406,
                    "msg" => "验证码错误"
                    );
            echo json_encode($ret);
            exit;
        }
        $cookie = "";
        $cookies = $loginReq['headers']['set-cookie'];
        $len = count($cookies);
        
        for($i=0; $i<$len; $i++)
        {
            if(strpos($cookies[$i], "uid=") === 0)
            {
                preg_match("/uid=(.*); /U", $cookies[$i], $matches);
                $cookie .= $matches[0];
            }else if(strpos($cookies[$i], "username=") === 0)
            {
                preg_match("/username=(.*); /U", $cookies[$i], $matches);
                $cookie .= $matches[0];
            }else if(strpos($cookies[$i], "token=") === 0 && strpos($cookies[$i], "token=deleted") === false)
            {
                preg_match("/token=(.*); /U", $cookies[$i], $matches);
                $cookie .= $matches[0];
            }
        }
        
        return $cookie;
    }
    
    public static function getCaptcha($sessid){
        $http = new EasyHttp();
        $res = $http->request("https://account.coolapk.com/auth/showCaptchaImage?" . time(), array(
            'method' => 'GET',        //	GET/POST
            'timeout' => 10,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902013151S release-keys; 6.0) +CoolMarket/10.5-2008061",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $sessid
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
        return "data:image/jpg/png/gif;base64," . base64_encode($res['body']);
    }
    public function uploadBackPic($file = "")
    {
        if(empty($file))
        {
            $log = $file . "--|--" . json_encode($_FILES);
            file_put_contents(DIR."/log.log", $log);
            $file = $_FILES['image']['tmp_name'];
        }
        $info = getimagesize($file);
        $prepare = self::uploadPrepare();
        if(is_object($prepare))return $prepare;
        $host = "http://{$prepare['data']['uploadPrepareInfo']['bucket']}." . parse_url($prepare['data']['uploadPrepareInfo']['endPoint'], PHP_URL_HOST) . "/";
        $url = $host . $prepare['data']['fileInfo'][0]['uploadFileName'];
        $callback = array(
            "callbackBodyType" => "application/json",
            "callbackHost" => "developer.coolapk.com",
            "callbackUrl" => $prepare['data']['uploadPrepareInfo']['callbackUrl'],
            "callbackBody" => '{"bucket":${bucket},"object":${object},"hasProcess":${x:var1}}'
            );
        $callback = base64_encode(json_encode($callback));
        $callbackVar = base64_encode('{"x:var1":"false"}');
        $contentMd5 = base64_encode(md5_file($file, true));
        $mime = $info['mime'];
        $date = gmdate("D, d M Y H:i:s T", time());
        $string_to_sign = "PUT\n{$contentMd5}\n{$mime}\n{$date}\nx-oss-callback:{$callback}\nx-oss-callback-var:{$callbackVar}\nx-oss-security-token:{$prepare['data']['uploadPrepareInfo']['securityToken']}\n/{$prepare['data']['uploadPrepareInfo']['bucket']}/{$prepare['data']['fileInfo'][0]['uploadFileName']}";
        $sign = base64_encode(hash_hmac('sha1', $string_to_sign, $prepare['data']['uploadPrepareInfo']['accessKeySecret'], true));
        $http = new EasyHttp();
        // $http->DEBUG = true;
        $res = $http->request($url, array(
            'method' => 'PUT',        //	GET/POST
            'timeout' => 120,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "aliyun-sdk-android/2.9.2(Linux/Android 6.0/Le%20X620;HEXCNFN5902012151S)",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "Content-Type" => $mime,
                "Content-MD5" => $contentMd5,
                // "Content-Length" => filesize($file),
                "Date" => $date,
                "x-oss-callback" => $callback,
                "x-oss-callback-var" => $callbackVar,
                "x-oss-security-token" => $prepare['data']['uploadPrepareInfo']['securityToken'],
                "Authorization" => "OSS {$prepare['data']['uploadPrepareInfo']['accessKeyId']}:{$sign}"
            ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            'body' => file_get_contents($file),
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        if(is_object($res))return $res;
        $ret = json_decode($res['body'], true);
        if($ret["status"] !== 0)
            return $ret;
        else return"{$prepare['data']['uploadPrepareInfo']['uploadImagePrefix']}/{$prepare['data']['fileInfo'][0]['uploadFileName']}";
    }
    public function changeBackPic($pic)
    {
        $device = strrev(base64_encode(mt_rand(0, 6666) . "; null;" . mt_rand(0, 999999) . "; 00:B3:3F:7D:95:B0; {$maker}; {$brand}; {$model}"));
        $token = self::getAppToken();
        $http = new EasyHttp();
        $res = $http->request("https://api.coolapk.com/v6/account/changeAvatarCover", array(
            'method' => 'POST',        //	GET/POST
            'timeout' => 10,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5-2008061",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $this->cookie,
                "X-Requested-With" => "XMLHttpRequest",
                "X-App-Id" => "com.coolapk.market",
                "X-App-Token" => $token,
                "X-App-Device" => $device,
                "X-App-Version" => "10.5",
                "X-App-Code" => "2008061",
                "X-Api-Version" => "10",
                "X-Sdk-Locale" => "zh-CN",
                "Content-Type" => "application/x-www-form-urlencoded"
            ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            'body' => array("url" => $pic),
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        if(is_object($res))return false;
        return $res['body'];
    }
    public function uploadAvatar($path)
    {
        $info = getimagesize($path);
        $device = strrev(base64_encode(mt_rand(0, 6666) . "; null;" . mt_rand(0, 999999) . "; 00:B3:3F:7D:95:B0; {$maker}; {$brand}; {$model}"));
        $token = self::getAppToken();
        $body = self::createFormData2(array(
                'type' => 'file',
                'name' => 'imgFile',
                'file' => array(
                    'path' => $path,
                    'name' => "cbfbf11f3a292df5d7ff7379ab315c6034a873b7",
                    'type' => $info['mime']
                    )
                ), "123-ebb9-4d08-a5ae-da6080cd26dc");
        // print_r($body);
        // exit;
        $http = new EasyHttp();
        $res = $http->request("https://api.coolapk.com/v6/account/changeAvatar", array(
            'method' => 'POST',        //	GET/POST
            'timeout' => 10,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5-2008061",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $this->cookie,
                "X-Requested-With" => "XMLHttpRequest",
                "X-App-Id" => "com.coolapk.market",
                "X-App-Token" => $token,
                "X-App-Device" => $device,
                "X-App-Version" => "10.5",
                "X-App-Code" => "2008061",
                "X-Api-Version" => "10",
                "X-Sdk-Locale" => "zh-CN",
                "Content-Type" => "multipart/form-data; boundary=123-ebb9-4d08-a5ae-da6080cd26dc",
                "Accept-Encoding" => "gzip"
            ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            'body' => $body,
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        print_r($res['body']);
    }
    private function uploadPrepare(){
        $device = strrev(base64_encode(mt_rand(0, 6666) . "; null;" . mt_rand(0, 999999) . "; 00:B3:3F:7D:95:B0; {$maker}; {$brand}; {$model}"));
        $token = self::getAppToken();
        $http = new EasyHttp();
        $body = array(
            "uploadBucket" => "avatar",
            "uploadDir" => "cover",
            "is_anonymous" => 0,
            "uploadFileList" => ""
            );
        $res = $http->request("https://api.coolapk.com/v6/upload/ossUploadPrepare", array(
            'method' => 'POST',        //	GET/POST
            'timeout' => 10,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Dalvik/2.1.0 (Linux; U; Android 6.0; Le X620 Build/HEXCNFN5902012151S) (#Build; LeEco; Le X620; HEXCNFN5902012151S release-keys; 6.0) +CoolMarket/10.5-2008061",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "cookie" => $this->cookie,
                "X-Requested-With" => "XMLHttpRequest",
                "X-App-Id" => "com.coolapk.market",
                "X-App-Token" => $token,
                "X-App-Device" => $device,
                "X-App-Version" => "10.5",
                "X-App-Code" => "2008061",
                "X-Api-Version" => "10",
                "Content-Type" => "application/x-www-form-urlencoded",
                "X-Sdk-Int" => "23",
                "X-Sdk-Locale" => "zh-CN"
            ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            // [{"name":"f3999a677196875233e09513db453403.jpeg","resolution":"1080x1080","md5":"ad52265e01ea0bf5eb7c06e2b240e558"}]
            'body' => "uploadBucket=avatar&uploadDir=cover&is_anonymous=0&uploadFileList=%5B%7B%22name%22%3A%22f3999a677197875233e09513db453403.jpeg%22%2C%22resolution%22%3A%221080x1080%22%2C%22md5%22%3A%22ad52265e01ea0bf5ec7c06e2b240e558%22%7D%5D",
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => false,
            'filename' => null        //	如果stream = true，则必须设定一个临时文件名
        ));
        if(is_object($res))return $res;
        return json_decode($res['body'], true);
    }
    /**
     * 请求Token生成
    */
    private static function getAppToken($device_id = "d4921063-1650-35c6-91bb-9e7555351c7f", $time = null) {
        if (!$time)
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
}
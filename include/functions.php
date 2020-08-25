<?php
include "class/pic.class.php";
include "class/coolapk.class.php";
include "class/shell/shell.class.php";

function imgRedict($url = null){
    $ext = null;    // 缩略图拓展名
    if(preg_match("/(.*)\.pic(\.[a-z]{1,2}\.jpg){0,1}/", $url, $matches))
    {
        // base64编码内容
        $base64 = $matches[1];
        if(func_is_base64($base64))
        {
            $url = base64_decode($base64);
        }else{
            $img = DIR . "/upload/notice.png";
            $pic = new PIC($img);
            $pic->outPic();
            exit;
        }
        // pic之后的拓展名
        $ext = isset($matches[2])?$matches[2]:null;
    }
        
    // header("x-ori: $url");
    // 链接合法性检测
    if(strpos($url, "http") === false)
    {
        $img = DIR . "/upload/notice.png";
        $pic = new PIC($img);
        $pic->outPic();
        exit;
    }
    // pixiv反代域名替换
    if(false !== strpos($url, "pximg.net"))
    {
        $url = str_replace("i.pximg.net", "i.pixiv.cat", $url);
    }
    
    if(null !== $ext)
    {
        // 缩略图处理"123.jpeg.x.jpg"
        // alphacoders.com
        if(false !== (strpos($url, "alphacoders.com")))
        {
            $pos = strrpos($url, "/");
            $url = substr($url, 0, $pos + 1) . "thumb-" . substr($url, $pos + 1);
        }else if(false !== strpos($url, "pixiv.cat"))
        {
            // pixiv
            switch($ext)
            {
                case ".xs.jpg":
                    $url = str_replace("img-original", "c/360x360_70/img-master", $url);
                    $pos = strrpos($url, ".");
                    $url = substr($url, 0, $pos) . "_master1200.jpg";
                    // header("X-log:{$ur}");
                    break;
                case ".m.jpg":
                    $url = str_replace("img-original", "c/540x540_70/img-master", $url);
                    $pos = strrpos($url, ".");
                    $url = substr($url, 0, $pos) . "_master1200.jpg";
                    break;
                case ".s.jpg":
                    $url = str_replace("img-original", "img-master", $url);
                    $pos = strrpos($url, ".");
                    $url = substr($url, 0, $pos) . "_master1200.jpg";
                    break;
                default:
                    $img = DIR . "/upload/notice.png";
                    $pic = new PIC($img);
                    $pic->outPic();
                    exit;
                    break;
            }
        }
    }
    // $img = DIR . "/upload/notice.png";
    // $pic = new PIC($img);
    // $pic->outPic();
    // header("HTTP/1.1 301 Moved Permanently");
    // header("location: {$url}");
    // exit;
    header("Content-Type:image");
    if (!($fp = fopen($url, "rb"))) {
        $img = DIR . "/upload/notice.png";
        $pic = new PIC($img);
        $pic->outPic();
        exit;
    }
    while (!feof($fp)) {
        echo fgets($fp, 4096);
    }
    fclose($fp);
}
function savePic() {
    $url = isset($_POST['url'])?$_POST['url']:null;
    $ref = isset($_POST['ref'])?$_POST['ref']:null;
    preg_match("/(http|https)+:\/\/[^\s]*/i", $url) or exit("error");
    $ret = PIC::onlineSave($url, DIR . "/upload/images", $ref);
    if(200 === $ret['status'])
    {
        $t = explode("/upload/images/", $ret['path'])[1];
        $new_url = "/getImg/{$t}";
        $arr = array(
            "status" => 200,
            "detail" => array(
                "ori" => $url,
                "url" => $new_url,
                "msg" => $ret['msg']
            )
        );
    }else{
        $arr = array(
            "status" => 222,
            "detail" => array(
                "ori" => $url,
                "msg" => $ret['msg']
            )
        );
    }
    echo json_encode($arr);
}

function postFeed()
{
    header("Content-Type:application/json");
    // $cookie = "uid=3514543; username=%E5%8D%83%E4%BB%A3%E9%85%B1; token=06806e9cAopmmyudxVLWiLr3lgVHWxPGsqHgKEvEXlZAvyF1tQbfqPKo9eHDqm9sC9SNj7tiQY97_vtCxrpqW6T7NQNG6UwkIkQz56aB0pP8PorGh8fuP3nXHKE3XZYZZqwXhUBuDjxzJO8Py1akHMIjMQxuK1lxxmObFHVkkq-vf2I_0rnlQtpweVYqAyx4YN5OXzcAp-01yL9KeW30gjjxVHkF6w";
    $cookie = isset($_POST['cookie'])?$_POST['cookie']:"";
    $pics = $_POST['pics'];
    if(!empty($pics))
    {
        // 处理图片链接
        $pics = explode("\r\n", $pics);
        
        foreach ($pics as $key => $value) {
            if(false === strpos($value, "http"))continue;
            if(strpos($value, 'coolapk.jysafe.cn') === false)
            {
                $pics[$key] = "http://coolapk.jysafe.cn/imgRedict/" . base64_encode($value) . ".pic";
                if(false !== strpos($value, "pximg.net"))
                {
                    // pixiv
                    if(false === strpos($value, "/img-original/"))
                    {
                        // 格式不正确
                        $arr = array(
                            "status" => -5,
                            "msg" => "pixiv链接格式不正确"
                            );
                        echo json_encode($arr);
                        exit;
                    }
                }
            }
        }
    }
    $coolapk = new CoolApk($cookie);
    switch($_POST['type'])
    {
        case "feed":
            $msg = array(
                "msg" => $_POST['text'],
                "img" => $pics
                );
            $coolapk->createFeed($msg);
            break;
        case "picture":
            $msg = array(
                "msg" => $_POST['text'],
                "img" => $pics
                );
            $coolapk->createPicFeed($msg);
            break;
        case "feedReply":
            $reply = array(
                "id" => $_POST['targetId'],
                "type" => "feed"
                );
            $msg = array(
                "msg" => $_POST['text'],
                "img" => $pics[0]
                );
            $coolapk->feedReply($reply, $msg);
            break;
        case "replyReply":
            $reply = array(
                "id" => $_POST['targetId'],
                "type" => "reply"
                );
            $msg = array(
                "msg" => $_POST['text'],
                "img" => $pics[0]
                );
            $coolapk->feedReply($reply, $msg);
            break;
        default:
            break;
    }
    return;
}

function uploadPic() {
    header("content-type:application/json");
    if (isset($_POST['url'])) {
        savePic();
        exit;
    }
    $file_name = $_FILES['image']['name'];
    //获取缓存区图片,格式不能变
    $type = array("jpg", "jpeg", "gif", 'png', 'bmp');
    //允许选择的图片类型
    $ext = explode(".", $file_name);
    //拆分获取图片名
    $ext = $ext[count($ext) - 1];
    //取图片的后缀名
    // print_r($_FILES);
    if (in_array($ext, $type)) {
        $new_name = md5_file($_FILES['image']['tmp_name']) . '.' . $ext;
        $path = DIR . '/upload/images/' . $new_name;
        $temp_file = $_FILES['image']['tmp_name'];
        $arr['detail'] = array(
            "url" => "/getImg/{$new_name}"
        );
        if (file_exists($path)) {
            // 已存在
            $arr['flag'] = 2;
        } else {
            move_uploaded_file($temp_file, $path);
            // 移动临时文件到目标路径
            $arr['flag'] = 1;
        }
    } else {
        // 后缀非法
        $arr['flag'] = 3;
    }
    echo json_encode($arr);
}

function getImg($file_name) {
    if(empty($file_name))exit("error");
    $md5 = substr($file_name, 0, strpos($file_name, "."));
    //检测有没改变
    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])){
        $etag = $_SERVER['HTTP_IF_NONE_MATCH'];
        if ($md5 === $etag){
            header("HTTP/1.1 304 Not Modified");
            exit;
        }
    }
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", strtotime('2011-1-1'))." GMT");
    //输出etag头
    header('etag:' . $md5);
    header('Cache-Control:max-age=2592000');
    
    $img = DIR . "/upload/images/{$file_name}";
    $originalImg = $img;
    if(preg_match("/\.[a-z]{1,2}\.jpg/", $img, $matches))
    {
        // "123.jpeg.x.jpg"
        $originalImg = substr($img, 0, strlen($img) - strlen($matches[0]));
    }
    $block = array(
        // "3d28da5abe3ba02da6d441aaf75fd293.jpeg"
        );
    if(!file_exists($originalImg)){
        // 原图不存在
        $img = DIR . "/upload/notice.png";
        $pic = new PIC($img);
        $pic->outPic();
        exit;
    }else{
        // 原图存在
        $pic = new PIC($originalImg);
        if(!empty($matches)){
            // 请求的不是原图
            switch($matches[0])
            {
                case ".m.jpg":
                    $pic->setQuality(60);
                    $pic->scaleImg(720, 720);
                    break;
                case ".xs.jpg":
                    $pic->setQuality(60);
                    $pic->scaleImg(360, 360);
                    break;
                case ".s.jpg":
                    $pic->setQuality(30);
                    break;
                default:
                    $pic->setQuality(50);
                    logInfo(json_encode($_SERVER));
                    break;
            }
            // $ret = $pic->setWordsWatermark("酷安@点赞姬", 50, "255,100,10", 5, DIR . '/src/fonts/msyh.ttf', 30);
        }else if(isset($_GET['wm']))
        {
            // 请求原图带水印
            if(in_array($file_name, $block) && strlen($_GET['wm']) === 1)
            {
                $pic->destroy();
                $img = DIR . "/upload/notice.png";
                $pic = new PIC($img);
                $pic->outPic();
                exit;
            }
            $ret = $pic->setWordsWatermark("酷安@点赞姬|  水印测试", 140, "255,100,10", 5, DIR . '/src/fonts/msyh.ttf', 30);
            $pic->setQuality(30);
        }else if(in_array($file_name, $block))
        {
            // 请求原图被封禁
            $pic->destroy();
            $img = DIR . "/upload/notice.png";
            $pic = new PIC($img);
            $pic->outPic();
            exit;
        }else
        {
            // 请求原图
            header('Content-Md5:' . base64_encode(md5_file($originalImg, true)));
        }
        $pic->outPic();
        exit;
    }
}

function logInfo($msg) {
    file_put_contents(DIR . "/log/log.log", $msg . PHP_EOL, FILE_APPEND);
}

function changeBackPic(){
    header("content-type:application/json");
    if($_SERVER['REQUEST_METHOD'] === "POST"){
        if(strpos($_POST["cookie"], "uid") === false || strpos($_POST["cookie"], "username") === false || strpos($_POST["cookie"], "token") === false)
        {
            $arr = array(
                "status" => -1,
                "msg" => "未登录"
                );
            echo json_encode($arr);
            exit;
        }
        $file_name = $_FILES['image']['name'];
        switch ($_FILES["image"]["error"])
        {
            case 0:
                $msg = null;
                break;
            case 1:
                $msg = "The file is bigger than this PHP installation allows";
                break;
            case 2:
                $msg = "The file is bigger than this form allows";
                break;
            case 3:
                $msg = "Only part of the file was uploaded";
                break;
            case 4:
                $msg = "No file was uploaded";
                break;
            case 6:
                $msg = "Missing a temporary folder";
                break;
            case 7:
                 $msg = "Failed to write file to disk";
                break;
            case 8:
                $msg = "File upload stopped by extension";
                break;
            default:
                $msg = "unknown error ".$_FILES['Filedata']['error'];
                break;
        }
        if($msg !== null){
            $arr = array(
                "status" => -1,
                "msg" => $msg
                );
            echo json_encode($arr);
            exit;
        }
        if(empty($file_name))
        {
            $arr = array(
                "status" => -1,
                "msg" => "空文件"
                );
            echo json_encode($arr);
            exit;
        }
        //获取缓存区图片,格式不能变
        $type = array("jpg", "jpeg", "gif", 'png');
        //允许选择的图片类型
        $ext = explode(".", $file_name);
        //拆分获取图片名
        $ext = $ext[count($ext) - 1];
        //取图片的后缀名
        if (in_array($ext, $type)) {
            $cp = new CoolApk($_POST["cookie"]);
            $imgUrl = $cp->uploadBackPic($_FILES["image"]["tmp_name"]);
            $ret = $cp->changeBackPic($imgUrl);
            // logInfo($ret);
            $ret = json_decode($re, true);
            if(isset($ret['status']))
            {
                echo $ret;
                exit;
            }
            if($ret === false)
            {
                $arr = array(
                    "status" => -1,
                    "msg" => "未知错误2"
                    );
                echo json_encode($arr);
                exit;
            }
            $arr = array(
                    "status" => 200,
                    "msg" => $ret
                    );
            echo json_encode($arr);
            exit;
        }else{
            $arr = array(
                    "status" => -1,
                    "msg" => "格式错误"
                    );
            echo json_encode($arr);
            exit;
        }
    }
}
function coolLogin(){
    if($_SERVER['REQUEST_METHOD'] === "POST"){
        switch($_POST['type'])
        {
            case "login":
                login();
                break;
            case "getCaptcha":
                $cap = CoolApk::getCaptcha($_POST['sessid']);
                echo json_encode(array($cap));
                break;
        }
    }else{
        $arr = array(
            "status" => 201,
            "msg" => "非法请求"
            );
        echo json_encode($arr);
    }
    exit;
}

function login(){
    header("Content-Type:application/json");
    $user = isset($_POST['cool_user'])?$_POST['cool_user']:"";
    $pass = isset($_POST['cool_pass'])?$_POST['cool_pass']:"";
    if(empty($user) || empty($pass))
    {
        $arr = array(
            "status" => 201,
            "msg" => "缺少参数"
            );
        echo json_encode($arr);
        exit;
    }
    $ca = new CoolApk($user, $pass);
    if(!empty($_POST['hash']) && !empty($_POST['captcha']) && !empty($_POST['sessid']))
    {
        $cookie = $ca->login($_POST['sessid'], $_POST['hash'], $_POST['captcha']);
    }else{
        $cookie = $ca->login();
    }
    if(empty($cookie))
        $arr = array(
            "status" => 401,
            "msg" => "错误"
            );
    else
        $arr = array(
            "status" => 200,
            "cookie" => $cookie
            );
    echo json_encode($arr);
    exit;
}
//第一个是原串,第二个是 部份串
function endWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

// curl -isX PURGE  http://coolapk.jysafe.cn/getImg/666.jpg
function purgeCache()
{
    $url = isset($_POST['urls'])?$_POST['urls']:"";
    $url = explode("\r\n", $url);
    $ret = array();
    for($i=0,$len=count($url); $i<$len; $i++) {
        if(preg_match("/(http|https)+:\/\/[^\s]*/i", $url[$i])){
            $cmd = "curl -isX PURGE {$url[$i]}";
            $t = shell::command($cmd, "echo pwd", true);
            $ret[] = array(
                "status" => 200,
                "url" => $url[$i],
                "msg" => strtok($t, "\n")
                );
        }else{
            $ret[] = array(
                "status" => 201,
                "url" => $url[$i],
                "msg" => "syntax error"
                );
        }
    }
    echo json_encode($ret);
    return;
}
/**
 * 判断字符串是否base64编码
 */
function func_is_base64($str)
{  
    return $str == base64_encode(base64_decode($str)) ? true : false;  
}
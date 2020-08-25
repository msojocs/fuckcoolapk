<?php
if(1){
    ini_set('display_errors','On');
    error_reporting(E_ALL & ~E_NOTICE);
}
include "load.php";
// header("content-type:application/json");
// print_r($_GET);
// print_r($_SERVER);
// exit;
// header("content-type:image/jpeg");
// print_r($_SERVER);
// exit;
// print_r(is_dir(__DIR__ . "/upload"));


$red = isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:$_SERVER['REQUEST_URI'];
if(strpos($red, "?"))
{
    $red = explode("?", $red)[0];
}
$t = explode("/", $red);
$path = isset($t[1])?$t[1]:"";
if(isset($t[2]))$name = $t[2];

switch ($path) {
    // 远程上传
    case 'save':
        savePic();
        break;
    // 本地图片
    case "up":
        uploadPic();
        break;
    case "doFeed":
        postFeed();
        break;
    case "getImg":
        getImg($name);
        break;
    
    // LiteSpeed Server 清理缓存
    case "purgeCache":
        purgeCache();
        break;
    case "doLogin":
        coolLogin();
    case "changeBackPic":
        changeBackPic();
        break;
    case "getDevices":
        header("content-type:application/json");
        echo file_get_contents('devices.json');
        break;
    case "imgRedict":
        if($p = strpos($red, "imgRedict/"))imgRedict(substr($red, $p + strlen("imgRedict/")));
        else imgRedict();
        break;
    case "upPage":
        echo file_get_contents("upPage.html");
        break;
    case "feedPage":
        echo file_get_contents("feedPage.html");
        break;
        
    // LiteSpeed Server 清理缓存
    case "purgePage":
        echo file_get_contents("purgePage.html");
        break;
    default:
        echo file_get_contents("announcement.html");
        break;
}
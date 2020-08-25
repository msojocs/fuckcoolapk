<?php
include "../load.php";
header("content-type:application/json");
$cookie = "uid=3514543; username=%E5%8D%83%E4%BB%A3%E9%85%B1; token=06806e9cAopmmyudxVLWiLr3lgVHWxPGsqHgKEvEXlZAvyF1tQbfqPKo9eHDqm9sC9SNj7tiQY97_vtCxrpqW6T7NQNG6UwkIkQz56aB0pP8PorGh8fuP3nXHKE3XZYZZqwXhUBuDjxzJO8Py1akHMIjMQxuK1lxxmObFHVkkq-vf2I_0rnlQtpweVYqAyx4YN5OXzcAp-01yL9KeW30gjjxVHkF6w";
$cp = new CoolApk($cookie);
$path = "/home/jysafecn/public_html/coolapk/test/avatar1.jpg";
$imgUrl = $cp->uploadBackPic($path);
print_r($imgUrl);
// $cp->changeBackPic("http://avatar.coolapk.com/cover/15/97/62/3514543_1597622117.jpg");
// $cp->uploadAvatar($path);
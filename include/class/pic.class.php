<?php
// 图片处理类
class PIC {
    private $imgPath;
    private $imgInfo;
    private $imgRes;
    private $quality;
    
    function __construct($imgPath)
    {
        $this->imgPath = $imgPath;
        // 获取图像信息
        $this->imgInfo = getimagesize($imgPath);
        // 获取图像后缀
        $this->imgInfo['ext'] = image_type_to_extension($this->imgInfo[2], false);
        // 创建图像资源
        $fun = "imagecreatefrom{$this->imgInfo['ext']}";
        $this->imgRes = $fun($this->imgPath);
        //输出质量,JPEG格式(0-100),PNG格式(0-9)
        if($this->imgInfo['ext'] == "png")
            $this->quality = 9;
        else
            $this->quality = 100;
    }
    public function setQuality($quality)
    {
        if($this->imgInfo['ext'] == "png"){
            $this->quality = $quality / 10;
            if($this->quality > 9)$this->quality = 9;
        }else
            $this->quality = $quality;
    }
    
    public function outPic(){
        $info = $this->imgInfo;
        $imgExt = $this->imgInfo['ext'];
        $im = $this->imgRes;
        $mime = $info['mime'];
        header("Accept-Ranges: bytes");
        header('Content-Type:' . $mime);
        
        $fun = "image{$imgExt}";
        // if($this->quality !== 100 && $this->quality !== 9)
        // {
        //     // 非原图
        //     $path = __DIR__ . md5(uniqid(microtime(true),true));
        //     // 存储图片
        //     $fun($im, $path, $this->quality);
        // }else
        // {
        //     // 原图
        //     $path = $this->imgPath;
        // }
        
        // header('Content-Md5:' . base64_encode(md5_file($path, true)));
        // header("Content-Length: " . filesize($path));
        // // 输出
        // $fp = fopen($path, "rb");
        // while (!feof($fp)) {
        //     echo fread($fp, 4096);
        // }
        // fclose($fp);
        
        // // 删除临时图片
        // if($this->quality !== 100 && $this->quality !== 9)
        //     unlink($path);
        
        //2.将图像输出到浏览器或文件。如: imagepng ( resource $image )
        $fun($im, null, $this->quality);
        imagedestroy($im);
    }
    
    /**
     * 主函数： 获取图片信息，准备参数
     * 
     * @param $quality           输出图片质量
     * @param $target_width      处理后的图片宽度
     * @param $target_height     处理后的图片高度
     */
    public function scaleImg($target_width = null, $target_height = null) {
        // 获取原图尺寸
        $img_info = $this->imgInfo;
        //原图片宽度
        $original_width = $img_info[0];
        //原图片高度
        $original_height = $img_info[1];
        $original_mime = $img_info['mime'];

        header('Content-Type:' . $original_mime);

        // 原图片长宽比
        $original_scale = $original_height / $original_width;

        if (null == $target_height)
            $target_height = $target_width * $original_scale;
        //目标图像长宽比
        $target_scale = $target_height / $target_width;

        if ($original_scale >= $target_scale) {
            // 过高
            $w = intval($original_width);
            $h = intval($target_scale * $w);

            $x = 0;
            $y = ($original_height - $h) / 3;
        } else {
            // 过宽
            $h = intval($original_height);
            $w = intval($h / $target_scale);

            $x = ($original_width - $w) / 2;
            $y = 0;
        }

        self::deal($w, $h,$x, $y, $original_width, $original_height, $target_width, $target_height);

    }
    /**
     * 进一步处理函数
     */
    public function deal($w, $h,$x, $y, $original_width, $original_height, $target_width, $target_height) {
        // 剪裁
        // 图像资源
        $source = $this->imgRes;
        // 新建一个真彩色图像
        $croped = imagecreatetruecolor($w, $h);
        // 拷贝图像的一部分
        imagecopy($croped, $source, 0, 0, $x, $y, $original_width, $original_height);
        // 缩放
        $scale = $target_width / $w;
        $target = imagecreatetruecolor($target_width, $target_height);
        //新建一个真彩色图像
        $final_w = intval($w * $scale);
        $final_h = intval($h * $scale);
        //重采样拷贝部分图像并调整大小
        imagecopyresampled($target, $croped, 0, 0, 0, 0, $final_w, $final_h, $w, $h);
        imagedestroy($this->imgRes);
        $this->imgRes = $target;
        $this->imgInfo[0] = $final_w;
        $this->imgInfo[1] = $final_h;
    }
    
    /**
     * Text Watermark Point:
     *   #1      #2    #3
     *   #4   #5    #6
     *   #7      #8    #9
     */

    /**
     * 给图片添加文字水印 可控制位置，旋转，多行文字    **有效字体未验证**
     * @param string $imgurl  图片地址
     * @param array $text   水印文字（多行以'|'分割）
     * @param int $fontSize 字体大小
     * @param type $color 字体颜色  如： 255,255,255
     * @param int $point 水印位置
     * @param type $font 字体
     * @param int $angle 旋转角度  允许值：  0-90   270-360 不含
     * @param string $newimgurl  新图片地址 默认使用后缀命名图片
     * @return boolean 
     */
    public function setWordsWatermark($text = "WaterMark", $fontSize = '14', $color = '0,0,0', $point = '1', $fontPath = 'msyh.ttf', $angle = 0, $newimgurl = '') {
        
        $imgWidth = $this->imgInfo[0];
        $imgHeight = $this->imgInfo[1];
        $imgMime = $this->imgInfo['mime'];
        $im = $this->imgRes;
        
        /*
         * 参数判断
         */
        $color = explode(',', $color);
        $text_color = imagecolorallocate($im, intval($color[0]), intval($color[1]), intval($color[2])); //文字水印颜色
        $point = intval($point) > 0 && intval($point) < 10 ? intval($point) : 1; //文字水印所在的位置
        $fontSize = intval($fontSize) > 0 ? intval($fontSize) : 14;
        $angle = ($angle >= 0 && $angle < 90 || $angle > 270 && $angle < 360) ? $angle : 0; //判断输入的angle值有效性
        $text = explode('|', $text);
    
        /**
         *  根据文字所在图片的位置方向，计算文字的坐标
         * 首先获取文字的宽，高， 写一行文字，超出图片后是不显示的
         */
        $textLength = count($text) - 1;
        $maxtext = 0;
        foreach ($text as $val) {
            $maxtext = strlen($val) > strlen($maxtext) ? $val : $maxtext;
        }
        $textSize = imagettfbbox($fontSize, 0, $fontPath, $maxtext);
        $textWidth = $textSize[2] - $textSize[1]; //文字的最大宽度
        $textHeight = $textSize[1] - $textSize[7]; //文字的高度
        $lineHeight = $textHeight + 3; //文字的行高
        
        //是否可以添加文字水印 只有图片的可以容纳文字水印时才添加
        // if ($textWidth + 40 > $imgWidth || $lineHeight * $textLength + 40 > $imgHeight) {
        //     return false; //图片太小了，无法添加文字水印
        // }
    
        if ($point == 1) { //左上角
            $porintLeft = 20;
            $pointTop = 20;
        } elseif ($point == 2) { //上中部
            $porintLeft = floor(($imgWidth - $textWidth) / 2);
            $pointTop = 20;
        } elseif ($point == 3) { //右上部
            $porintLeft = $imgWidth - $textWidth - 20;
            $pointTop = 20;
        } elseif ($point == 4) { //左中部
            $porintLeft = 20;
            $pointTop = floor(($imgHeight - $textLength * $lineHeight) / 2);
        } elseif ($point == 5) { //正中部
            $porintLeft = floor(($imgWidth - $textWidth) / 2);
            $pointTop = floor(($imgHeight - $textLength * $lineHeight) / 2);
        } elseif ($point == 6) { //右中部
            $porintLeft = $imgWidth - $textWidth - 20;
            $pointTop = floor(($imgHeight - $textLength * $lineHeight) / 2);
        } elseif ($point == 7) { //左下部
            $porintLeft = 20;
            $pointTop = $imgHeight - $textLength * $lineHeight - 20;
        } elseif ($point == 8) { //中下部
            $porintLeft = floor(($imgWidth - $textWidth) / 2);
            $pointTop = $imgHeight - $textLength * $lineHeight - 20;
        } elseif ($point == 9) { //右下部
            $porintLeft = $imgWidth - $textWidth - 20;
            $pointTop = $imgHeight - $textLength * $lineHeight - 20;
        }
    
        //如果有angle旋转角度，则重新设置 top ,left 坐标值
        if ($angle != 0) {
            if ($angle < 90) {
                $diffTop = ceil(sin($angle * M_PI / 180) * $textWidth);
    
                if (in_array($point, array(1, 2, 3))) {// 上部 top 值增加
                    $pointTop += $diffTop;
                } elseif (in_array($point, array(4, 5, 6))) {// 中部 top 值根据图片总高判断
                    if ($textWidth > ceil($imgHeight / 2)) {
                        $pointTop += ceil(($textWidth - $imgHeight / 2) / 2);
                    }
                }
            } elseif ($angle > 270) {
                $diffTop = ceil(sin((360 - $angle) * M_PI / 180) * $textWidth);
    
                if (in_array($point, array(7, 8, 9))) {// 上部 top 值增加
                    $pointTop -= $diffTop;
                } elseif (in_array($point, array(4, 5, 6))) {// 中部 top 值根据图片总高判断
                    if ($textWidth > ceil($imgHeight / 2)) {
                        $pointTop = ceil(($imgHeight - $diffTop) / 2);
                    }
                }
            }
        }
    
        foreach ($text as $key => $val) {
            imagettftext($im, $fontSize, $angle, $porintLeft, $pointTop + $key * $lineHeight, $text_color, $fontPath, $val);
        }
    }
    public function destroy()
    {
        imagedestroy($this->imgRes);
    }
    
    private static function getRepHeaders($sUrl){
        $oCurl = curl_init();
        // 设置请求头, 有时候需要,有时候不用,看请求网址是否有对应的要求
        // $header[] = "Content-type: application/x-www-form-urlencoded";
        $user_agent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36";
        curl_setopt($oCurl, CURLOPT_URL, $sUrl);
        // curl_setopt($oCurl, CURLOPT_HTTPHEADER,$header);
        // 返回 response_header, 该选项非常重要,如果不为 true, 只会获得响应的正文
        curl_setopt($oCurl, CURLOPT_HEADER, true);
        // 是否不需要响应的正文,为了节省带宽及时间,在只需要响应头的情况下可以不要正文
        curl_setopt($oCurl, CURLOPT_NOBODY, true);
        // 使用上面定义的 ua
        curl_setopt($oCurl, CURLOPT_USERAGENT,$user_agent);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        
        // 不用 POST 方式请求, 意思就是通过 GET 请求
        curl_setopt($oCurl, CURLOPT_POST, false);
        
        $sContent = curl_exec($oCurl);
        // 获得响应结果里的：头大小
        $headerSize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
        // 根据头大小去获取头信息内容
        $header = substr($sContent, 0, $headerSize);
            
        curl_close($oCurl);
        
        print_r($sContent);
        print_r($header);
    }
    
    /**
     * @param $url 远程图片链接
     * @param $dir 存储本地路径(结尾无"/")
     *
     * @return string 文件路径
     */
    public static function onlineSave($url, $dir, $ref) {
        // $url = "https://i0.hdslb.com/bfs/album/a86968bf004eb542d18a45c58a1eeb53a87a80c0.jpg";
        if($ref === null)
        {
            $p = parse_url($url);
            $ref = "{$p['scheme']}://{$p['host']}/";
        }
        $temp_path = "/tmp/" . md5(uniqid(microtime(true),true));
        $http = new EasyHttp();
        $response = $http->request($url, array(
            'method' => 'GET',        //	GET/POST
            'timeout' => 20,            //	超时的秒数
            'redirection' => 5,        //	最大重定向次数
            'httpversion' => '1.1',    //	1.0/1.1
            'user-agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4217.0 Safari/537.36 Edg/86.0.601.0",
            'blocking' => true,        //	是否阻塞
            'headers' => array(
                "referer" => $ref
                ),    //	header信息
            'cookies' => null,    //	关联数组形式的cookie信息
            // 'cookies' => $cookies,
            'body' => null,
            'compress' => false,    //	是否压缩
            'decompress' => true,    //	是否自动解压缩结果
            'sslverify' => true,
            'stream' => true,
            'filename' => $temp_path        //	如果stream = true，则必须设定一个临时文件名
        ));
        if (is_object($response)) return false;
        $originalSize = intval($response['headers']['content-length']);
        $new_path = false;
        $ret = array();
        if(intval(filesize($temp_path)) === $originalSize){
            // 大小相同
            $md5 = md5_file($temp_path);
            $suffix = self::getImgSuffix($temp_path);
            if(strlen($suffix) === 0)
            {
                $ret['status'] = 28;
                $ret['msg'] = "下载出错";
            }else{
                $new_path = $dir . "/{$md5}.{$suffix}";
                if(file_exists($new_path))
                {
                    $ret['status'] = 200;
                    $ret['msg'] = "文件已存在";
                    $ret['path'] = $new_path;
                }else{
                    copy($temp_path, $new_path);
                    $ret['status'] = 200;
                    $ret['msg'] = "成功";
                    $ret['path'] = $new_path;
                }
            }
        }else{
            // EasyHttp下载失败，采用备用方案
            preg_match("/[^\/\?&\s\:\\\]+$/i", $url, $fname);
            if (!($fp = fopen(trim($url), "rb"))) {
                $ret['status'] = 26;
                $ret['msg'] = "远程文件读取失败";
            } elseif (!($fp2 = fopen($fpath = "{$dir}/{$fname}", "wb"))) {
                $ret['status'] = 27;
                $ret['msg'] = "本地文件打开失败";
            } else {
                while (!feof($fp)) {
                    fwrite($fp2, fread($fp, 4096));
                }
                fclose($fp);
                fclose($fp2);
                if (intval(filesize($fpath)) === $originalSize) {
                    $md5 = md5_file($fpath);
                    $suffix = self::getImgSuffix($fpath);
                    $new_path = "{$dir}/{$md5}.{$suffix}";
                    rename($fpath, $new_path);
                    $ret['status'] = 200;
                    $ret['msg'] = $new_path;
                } else {
                    unlink($fpath);
                    $ret['status'] = 28;
                    $ret['msg'] = "文件校验失败";
                }
            }
        }
        unlink($temp_path);
        return $ret;
    }
    
    /*
    @desc：获取图片真实后缀
    @param   name    文件名
    @return  suffix  文件后缀
    */
    public static function getImgSuffix($name) {
        $info = getimagesize($name);
        $suffix = false;
        if ($mime = $info['mime']) {
            $suffix = explode('/',$mime)[1];
        }
        return $suffix;
    }

}
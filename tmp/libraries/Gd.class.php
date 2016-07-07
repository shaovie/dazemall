<?php
/*
 * GD图像类
 * 
 * */
class Gd{
    /**
     * 图像资源对象
     * @var resource
     */
    private $img;

    /**
     * 图像信息
     * @var array
     */
    private $info;

    /**
     * @param string $imgname 图像路径
     */
    public function __construct($imgname = null) {
        $imgname && $this->open($imgname);
    }

    /**
     * 打开一张图像
     * @param  string $imgname 图像路径
     */
    public function open($imgname){
        //检测图像文件
        if(!is_file($imgname)) {
            die('图像文件不存在');
        }

        //获取图像信息
        $info = getimagesize($imgname);

        //检测图像合法性
        if(false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))){
            die('图像文件非法');
        }

        //设置图像信息
        $this->info = array(
            'width'  => $info[0],
            'height' => $info[1],
            'type'   => image_type_to_extension($info[2], false),
            'mime'   => $info['mime'],
        );

        //销毁已存在的图像
        empty($this->img) || imagedestroy($this->img);

        //打开图像
        if('gif' == $this->info['type']){
           die('gif非法图像');
        } else {
            $fun = "imagecreatefrom{$this->info['type']}";
            $this->img = $fun($imgname);
        }
    }

    /**
     * 保存图像
     * @param  string  $imgname   图像保存名称
     * @param  string  $type      图像类型
     * @param  integer $quality   图像质量     
     * @param  boolean $interlace 是否对JPEG类型图像设置隔行扫描
     */
    public function save($imgname, $type = null, $quality=80,$interlace = true){
        if(empty($this->img)) {
            die('没有可以被保存的图像资源');
        }

        //自动获取图像类型
        if(is_null($type)){
            $type = $this->info['type'];
        } else {
            $type = strtolower($type);
        }
        //保存图像
        if('jpeg' == $type || 'jpg' == $type){
            //JPEG图像设置隔行扫描
            imageinterlace($this->img, $interlace);
            imagejpeg($this->img, $imgname,$quality);
        }else{
            $fun = 'image'.$type;
            $fun($this->img, $imgname);
        }
    }

    /**
     * 返回图像宽度
     * @return integer 图像宽度
     */
    public function width(){
        if(empty($this->img)) {
            die('没有指定图像资源');
        }
        return $this->info['width'];
    }

    /**
     * 返回图像高度
     * @return integer 图像高度
     */
    public function height(){
        if(empty($this->img)) {
            die('没有指定图像资源');
        }
        return $this->info['height'];
    }

    /**
     * 返回图像类型
     * @return string 图像类型
     */
    public function type(){
        if(empty($this->img)) {
            die('没有指定图像资源');
        }
        return $this->info['type'];
    }

    /**
     * 返回图像MIME类型
     * @return string 图像MIME类型
     */
    public function mime(){
        if(empty($this->img)) {
            die('没有指定图像资源');
        }
        return $this->info['mime'];
    }

    /**
     * 返回图像尺寸数组 0 - 图像宽度，1 - 图像高度
     * @return array 图像尺寸
     */
    public function size(){
        if(empty($this->img)) {
            die('没有指定图像资源');
        }
        return array($this->info['width'], $this->info['height']);
    }

    /**
     * 裁剪图像
     * @param  integer $w      裁剪区域宽度
     * @param  integer $h      裁剪区域高度
     * @param  integer $x      裁剪区域x坐标
     * @param  integer $y      裁剪区域y坐标
     * @param  integer $width  图像保存宽度
     * @param  integer $height 图像保存高度
     */
    public function crop($w, $h, $x = 0, $y = 0, $width = null, $height = null){
        if(empty($this->img)) {
            die('没有可以被裁剪的图像资源');
        }

        //设置保存尺寸
        empty($width)  && $width  = $w;
        empty($height) && $height = $h;
        //创建新图像
        $img = imagecreatetruecolor($width, $height);
        // 调整默认颜色
        $color = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $color);
        
        //裁剪
        imagecopyresampled($img, $this->img, 0, 0, $x, $y, $width, $height, $w, $h);
        imagedestroy($this->img); //销毁原图
        
        //设置新图像
        $this->img = $img;
        $this->info['width']  = $width;
        $this->info['height'] = $height;
    }

    /**
     * 生成缩略图
     * @param  integer $width  缩略图最大宽度
     * @param  integer $height 缩略图最大高度
     * @param  integer $type   缩略图裁剪类型
     */
    public function thumb($width, $height, $type = image::IMAGE_THUMB_SCALE){
        if(empty($this->img)) {
           die('没有可以被缩略的图像资源');
        }

        //原图宽度和高度
        $w = $this->info['width'];
        $h = $this->info['height'];

        /* 计算缩略图生成的必要参数 */
        switch ($type) {
            /* 等比例缩放 */
            case image::IMAGE_THUMB_SCALE:
                //原图尺寸小于缩略图尺寸则不进行缩略
                if($w < $width && $h < $height) return;

                //计算缩放比例
                $scale = min($width/$w, $height/$h);
                
                //设置缩略图的坐标及宽度和高度
                $x = $y = 0;
                $width  = $w * $scale;
                $height = $h * $scale;
                break;

            /* 居中裁剪 */
            case image::IMAGE_THUMB_CENTER:
                //计算缩放比例
                $scale = max($width/$w, $height/$h);

                //设置缩略图的坐标及宽度和高度
                $w = $width/$scale;
                $h = $height/$scale;
                $x = ($this->info['width'] - $w)/2;
                $y = ($this->info['height'] - $h)/2;
                break;

            /* 左上角裁剪 */
            case image::IMAGE_THUMB_NORTHWEST:
                //计算缩放比例
                $scale = max($width/$w, $height/$h);

                //设置缩略图的坐标及宽度和高度
                $x = $y = 0;
                $w = $width/$scale;
                $h = $height/$scale;
                break;

            /* 右下角裁剪 */
            case image::IMAGE_THUMB_SOUTHEAST:
                //计算缩放比例
                $scale = max($width/$w, $height/$h);

                //设置缩略图的坐标及宽度和高度
                $w = $width/$scale;
                $h = $height/$scale;
                $x = $this->info['width'] - $w;
                $y = $this->info['height'] - $h;
                break;

            /* 填充 */
            case image::IMAGE_THUMB_FILLED:
                //计算缩放比例
                if($w < $width && $h < $height){
                    $scale = 1;
                } else {
                    $scale = min($width/$w, $height/$h);
                }

                //设置缩略图的坐标及宽度和高度
                $neww = $w * $scale;
                $newh = $h * $scale;
                $posx = ($width  - $w * $scale)/2;
                $posy = ($height - $h * $scale)/2;
                
                //创建新图像
                $img = imagecreatetruecolor($width, $height);
                // 调整默认颜色
                $color = imagecolorallocate($img, 255, 255, 255);
                imagefill($img, 0, 0, $color);
                
                //裁剪
                imagecopyresampled($img, $this->img, $posx, $posy, $x, $y, $neww, $newh, $w, $h);
                imagedestroy($this->img); //销毁原图
                $this->img = $img;
                
                $this->info['width']  = $width;
                $this->info['height'] = $height;
                return;

            /* 固定 */
            case image::IMAGE_THUMB_FIXED:
                $x = $y = 0;
                break;

            default:
                die('不支持的缩略图裁剪类型');
        }

        /* 裁剪图像 */
        $this->crop($w, $h, $x, $y, $width, $height);
    }

    /**
     * 添加水印
     * @param  string  $source 水印图片路径
     * @param  integer $locate 水印位置
     * @param  integer $alpha  水印透明度
     */
    public function water($source, $locate = image::IMAGE_WATER_SOUTHEAST,$alpha=80){
        //资源检测
        if(empty($this->img)) die('没有可以被添加水印的图像资源');
        if(!is_file($source)) die('水印图像不存在');

        //获取水印图像信息
        $info = getimagesize($source);
        if(false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))){
            die('非法水印文件');
        }

        //创建水印图像资源
        $fun   = 'imagecreatefrom' . image_type_to_extension($info[2], false);
        $water = $fun($source);

        //设定水印图像的混色模式
        imagealphablending($water, true);

        /* 设定水印位置 */
        switch ($locate) {
            /* 右下角水印 */
            case image::IMAGE_WATER_SOUTHEAST:
                $x = $this->info['width'] - $info[0];
                $y = $this->info['height'] - $info[1];
                break;

            /* 左下角水印 */
            case image::IMAGE_WATER_SOUTHWEST:
                $x = 0;
                $y = $this->info['height'] - $info[1];
                break;

            /* 左上角水印 */
            case image::IMAGE_WATER_NORTHWEST:
                $x = $y = 0;
                break;

            /* 右上角水印 */
            case image::IMAGE_WATER_NORTHEAST:
                $x = $this->info['width'] - $info[0];
                $y = 0;
                break;

            /* 居中水印 */
            case image::IMAGE_WATER_CENTER:
                $x = ($this->info['width'] - $info[0])/2;
                $y = ($this->info['height'] - $info[1])/2;
                break;

            /* 下居中水印 */
            case image::IMAGE_WATER_SOUTH:
                $x = ($this->info['width'] - $info[0])/2;
                $y = $this->info['height'] - $info[1];
                break;

            /* 右居中水印 */
            case image::IMAGE_WATER_EAST:
                $x = $this->info['width'] - $info[0];
                $y = ($this->info['height'] - $info[1])/2;
                break;

            /* 上居中水印 */
            case image::IMAGE_WATER_NORTH:
                $x = ($this->info['width'] - $info[0])/2;
                $y = 0;
                break;

            /* 左居中水印 */
            case image::IMAGE_WATER_WEST:
                $x = 0;
                $y = ($this->info['height'] - $info[1])/2;
                break;

            default:
                /* 自定义水印坐标 */
                if(is_array($locate)){
                    list($x, $y) = $locate;
                } else {
                    die('不支持的水印位置类型');
                }
        }
        
        //添加水印
        $src = imagecreatetruecolor($info[0], $info[1]);
        // 调整默认颜色
        $color = imagecolorallocate($src, 255, 255, 255);
        imagefill($src, 0, 0, $color);
        
        imagecopy($src, $this->img, 0, 0, $x, $y, $info[0], $info[1]);
        imagecopy($src, $water, 0, 0, 0, 0, $info[0], $info[1]);
        imagecopymerge($this->img, $src, $x, $y, 0, 0, $info[0], $info[1], $alpha);
        
        //销毁零时图片资源
        imagedestroy($src);

        //销毁水印资源
        imagedestroy($water);
    }

    /**
     * 图像添加文字
     * @param  string  $text   添加的文字
     * @param  string  $font   字体路径
     * @param  integer $size   字号
     * @param  string  $color  文字颜色
     * @param  integer $locate 文字写入位置
     * @param  integer $offset 文字相对当前位置的偏移量
     * @param  integer $angle  文字倾斜角度
     */
    public function text($text, $font, $size, $color = '#00000000', 
        $locate = image::IMAGE_WATER_SOUTHEAST, $offset = 0, $angle = 0){
        //资源检测
        if(empty($this->img)) {
            die('没有可以被写入文字的图像资源');
        }
        if(!is_file($font)) {
            die("不存在的字体文件：{$font}");
        }

        //获取文字信息
        $info = imagettfbbox($size, $angle, $font, $text);
        $minx = min($info[0], $info[2], $info[4], $info[6]); 
        $maxx = max($info[0], $info[2], $info[4], $info[6]); 
        $miny = min($info[1], $info[3], $info[5], $info[7]); 
        $maxy = max($info[1], $info[3], $info[5], $info[7]); 

        /* 计算文字初始坐标和尺寸 */
        $x = $minx;
        $y = abs($miny);
        $w = $maxx - $minx;
        $h = $maxy - $miny;

        /* 设定文字位置 */
        switch ($locate) {
            /* 右下角文字 */
            case image::IMAGE_WATER_SOUTHEAST:
                $x += $this->info['width']  - $w;
                $y += $this->info['height'] - $h;
                break;

            /* 左下角文字 */
            case image::IMAGE_WATER_SOUTHWEST:
                $y += $this->info['height'] - $h;
                break;

            /* 左上角文字 */
            case image::IMAGE_WATER_NORTHWEST:
                // 起始坐标即为左上角坐标，无需调整
                break;

            /* 右上角文字 */
            case image::IMAGE_WATER_NORTHEAST:
                $x += $this->info['width'] - $w;
                break;

            /* 居中文字 */
            case image::IMAGE_WATER_CENTER:
                $x += ($this->info['width']  - $w)/2;
                $y += ($this->info['height'] - $h)/2;
                break;

            /* 下居中文字 */
            case image::IMAGE_WATER_SOUTH:
                $x += ($this->info['width'] - $w)/2;
                $y += $this->info['height'] - $h;
                break;

            /* 右居中文字 */
            case image::IMAGE_WATER_EAST:
                $x += $this->info['width'] - $w;
                $y += ($this->info['height'] - $h)/2;
                break;

            /* 上居中文字 */
            case image::IMAGE_WATER_NORTH:
                $x += ($this->info['width'] - $w)/2;
                break;

            /* 左居中文字 */
            case image::IMAGE_WATER_WEST:
                $y += ($this->info['height'] - $h)/2;
                break;

            default:
                /* 自定义文字坐标 */
                if(is_array($locate)){
                    list($posx, $posy) = $locate;
                    $x += $posx;
                    $y += $posy;
                } else {
                    die('不支持的文字位置类型');
                }
        }

        /* 设置偏移量 */
        if(is_array($offset)){
            $offset = array_map('intval', $offset);
            list($ox, $oy) = $offset;
        } else{
            $offset = intval($offset);
            $ox = $oy = $offset;
        }

        /* 设置颜色 */
        if(is_string($color) && 0 === strpos($color, '#')){
            $color = str_split(substr($color, 1), 2);
            $color = array_map('hexdec', $color);
            if(empty($color[3]) || $color[3] > 127){
                $color[3] = 0;
            }
        } elseif (!is_array($color)) {
            die('错误的颜色值');
        }
        
        /* 写入文字 */
        $col = imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], $color[3]);
        imagettftext($this->img, $size, $angle, $x + $ox, $y + $oy, $col, $font, $text);
    }
    
    //生成圆形图方画
    private function _get_lt_rounder_corner($radius) {
        $img = imagecreatetruecolor($radius, $radius);
        $bgcolor = imagecolorallocate($img, 255, 255, 255);
        $fgcolor = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $bgcolor);
        imagefilledarc($img, $radius, $radius, $radius*2, $radius*2, 180, 270, $fgcolor, IMG_ARC_PIE);
        imagecolortransparent($img, $fgcolor);
        return $img;
    }
    
    /**
     * 生成圆形图
     */
    public function round() {
        // load the source image
        $src_image = $this->img;
        if ($src_image === false) {
            die('Sorry, can/t load the image');
        }
        $image_width = imagesx($src_image);
        $image_height = imagesy($src_image);
        if($image_width < $image_height){
            $imageSize = $image_width;
        }else{
            $imageSize = $image_height;
        }
        $radius=(int)$imageSize/2;
        // create a new image, with src_width, src_height, and fill it with transparent color
        $image = imagecreatetruecolor($imageSize, $imageSize);
        $trans_color = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $trans_color);
         
        // then overwirte the source image to the new created image
        imagecopymerge($image, $src_image, 0, 0, 0, 0, $imageSize, $imageSize, 100);
         
        // then just copy all the rounded corner images to the 4 corners
        // lt
        $lt_corner = $this->_get_lt_rounder_corner($radius);
        imagecopymerge($image, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);
        // lb
        $lb_corner = imagerotate($lt_corner, 90, $trans_color);
        imagecopymerge($image, $lb_corner, 0, $imageSize - $radius, 0, 0, $radius, $radius, 100);
        // rb
        $rb_corner = imagerotate($lt_corner, 180, $trans_color);
        imagecopymerge($image, $rb_corner, $imageSize - $radius, $imageSize - $radius, 0, 0, $radius, $radius, 100);
        // rt
        $rt_corner = imagerotate($lt_corner, 270, $trans_color);
        imagecopymerge($image, $rt_corner, $imageSize - $radius, 0, 0, 0, $radius, $radius, 100);
         
        // set the transparency
        imagecolortransparent($image,$trans_color);
        $this->img = $image;
    }
    
    /**
     * 图像合并
     * @param unknown $template_img_path
     */
    public function merge($template_img_path,$x,$y) {
        $type = $this->info['type'];
        $template_img = $template_img_path.'.'.$type;
        if(!file_exists($template_img)) {
            die('template img not exists.');
        }
        list($max_width, $max_height) = getimagesize($template_img);
        $dests = imagecreatetruecolor($max_width, $max_height);
        
        $fun = "imagecreatefrom{$this->info['type']}";
        $dst_im = $fun($template_img);
        
        imagecopy($dests,$dst_im,0,0,0,0,$max_width,$max_height);
        imagedestroy($dst_im);
        
        $src_im = $this->img;
        //imagecopy($dests,$src_im,0,$max_height/2,0,0,$this->info['width'],$this->info['height']);
        imagecopy($dests,$src_im,$x,$y,0,0,$this->info['width'],$this->info['height']);
        imagedestroy($src_im);
        
        $this->img = $dests;        
    }

    public function __destruct() {
        empty($this->img) || imagedestroy($this->img);
    }
}
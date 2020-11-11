<?php
/**
 * 图片常用操作类
 *
 * 用于生成缩略图、图片水印生成等Web开发常用操作
 *
 * @author tommy <tommy@doitphp.com>
 * @copyright Copyright (c) 2010 Tommy Software Studio
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Image.php 2.0 2012-12-23 10:40:01Z tommy $
 * @package library
 * @since 1.0
 */
namespace doitphp\library;

use doitphp\core\Response;

if (!defined('IN_DOIT')) {
    exit();
}

class Image {

    /**
     * 原图片路径,该图片在验证码时指背景图片,在水印图片时指水印图片.
     *
     * @var string
     */
    protected $_imagePath;

    /**
     * 字体名称
     *
     * @var string
     */
    protected $_fontName;

    /**
     * 字体大小
     *
     * @var integer
     */
    protected $_fontSize;

    /**
     * 图片实例化名称
     *
     * @var object
     */
    protected $_image;

    /**
     * 图象宽度
     *
     * @var integer
     */
    protected $_width;

    /**
     * 图象高度
     *
     * @var integer
     */
    protected $_height;

    /**
     * 图片格式, 如:jpeg, gif, png
     *
     * @var string
     */
    protected $_type;

    /**
     * 文字的横坐标
     *
     * @var integer
     */
    protected $_fontX;

    /**
     * 文字的纵坐标
     *
     * @var integer
     */
    protected $_fontY;

    /**
     * 字体颜色
     *
     * @var string
     */
    protected $_fontColor;

    /**
     * 生成水印图片的原始图片的宽度
     *
     * @var integer
     */
    protected $_imageWidth;

    /**
     * 生成水印图片的原始图片的高度
     *
     * @var integer
     */
    protected $_imageHeight;

    /**
     * 生成缩略图的实际宽度
     *
     * @var integer
     */
    protected $_widthNew;

    /**
     * 生成缩略图的实际高度
     *
     * @var integer
     */
    protected $_heightNew;

    /**
     * 水印图片的实例化对象
     *
     * @var object
     */
    protected $_waterImage;

    /**
     * 生成水印区域的横坐标
     *
     * @var integer
     */
    protected $_waterX;

    /**
     * 生成水印区域的纵坐标
     *
     * @var integer
     */
    protected $_waterY;

    /**
     * 图片剪切的起始横坐标
     *
     * @var integer
     */
    protected $_clipAreaX = 0;

    /**
     * 图片剪切的起始纵坐标
     *
     * @var integer
     */
    protected $_clipAreaY = 0;

    /**
     * 图片剪切区域的自适应性状态，默认：自适应性
     *
     * @var boolean
     */
    protected $_isClipFlex = true;

    /**
     * 生成水印图片的水印区域的透明度
     *
     * @var integer
     */
    protected $_alpha;

    /**
     * 文字水印字符内容
     *
     * @var string
     */
    protected $_textContent;

    /**
     * 水印图片的宽度
     *
     * @var integer
     */
    protected $_waterWidth;

    /**
     * 水印图片的高度
     *
     * @var integer
     */
    protected $_waterHeight;


    /**
     * 构造方法
     *
     * @access public
     * @return boolean
     */
    public function __construct() {

        $this->_fontSize = 16;
        $this->_fontName = DOIT_ROOT . '/views/source/aispec.ttf';

        return true;
    }

    /**
     * 初始化运行环境,获取图片格式并实例化.
     *
     * @access protected
     *
     * @param string $url 图片路径
     *
     * @return boolean
     */
    protected function _parseImageInfo($url) {

        list($this->_imageWidth, $this->_imageHeight, $type) = getimagesize($url);

        switch ($type) {

            case 1:
                $this->_image = imagecreatefromgif ($url);
                $this->_type  = 'gif';
                break;

            case 2:
                $this->_image = imagecreatefromjpeg($url);
                $this->_type  = 'jpg';
                break;

            case 3:
                $this->_image = imagecreatefrompng($url);
                imagesavealpha($this->_image, true);
                $this->_type  = 'png';
                break;

            case 4:
                $this->_image = imagecreatefromwbmp($url);
                $this->_type  = 'bmp';
                break;
        }

        return true;
    }

    /**
     * 设置图片文字的字体名称.
     *
     * @access public
     *
     * @param string $name 字体名称(字体的路径)
     * @param integer $size 字体大小
     *
     * @return object
     */
    public function setFontName($name, $size = null) {

        if ($name) {
            $this->_fontName = $name;
        }
        if ($size) {
            $this->_fontSize = (int)$size;
        }

        return $this;
    }

    /**
     * 设置图片文字的字体大小.
     *
     * @access public
     *
     * @param integer $size 字体大小
     *
     * @return object
     */
    public function setFontSize($size) {

        if ($size) {
            $this->_fontSize = (int)$size;
        }

        return $this;
    }

    /**
     * 设置图片文字的颜色参数.
     *
     * @access public
     *
     * @param integer $x    RGB色彩中的R的数值
     * @param integer $y    RGB色彩中的G的数值
     * @param integer $z    RGB色彩中的B的数值
     *
     * @return object
     */
    public function setFontColor($x = null, $y = null, $z = null) {

        $this->_fontColor = (is_int($x) && is_int($y) && is_int($z)) ? array($x, $y, $z) : array(255, 255, 255);

        return $this;
    }

    /**
     * 水印图片的路径.
     *
     * @access public
     *
     * @param string $url    图片的路径(图片的实际地址)
     *
     * @return object
     */
    public function setWatermarkImage($imagePath) {

        if ($imagePath) {
            $this->_imagePath = $imagePath;
        }

        return $this;
    }

    /**
     * 设置生成图片的大小.
     *
     * @access public
     *
     * @param integer $width    图片的宽度
     * @param integer $height    图片的高度
     *
     * @return object
     */
    public function setImageSize($width, $height) {

        if ($width) {
            $this->_width = (int)$width;
        }
        if ($height) {
            $this->_height = (int)$height;
        }

        return $this;
    }

    /**
     * 设置文字水印字符串内容.
     *
     * @access public
     *
     * @param string $content 文字内容
     *
     * @return object
     */
    public function setTextContent($content) {

        if ($content) {
            $this->_textContent = $content;
        }

        return $this;
    }

    /**
     * 设置文字水印图片文字的坐标位置.
     *
     * @access public
     *
     * @param integer $x    水印区域的横坐标
     * @param integer $y    水印区域的纵坐标
     *
     * @return object
     */
    public function setTextPosition($x, $y) {

        if ($x) {
            $this->_fontX = (int)$x;
        }
        if ($y) {
            $this->_fontY = (int)$y;
        }

        return $this;
    }

    /**
     * 设置水印图片水印的坐标位置.
     *
     * @access public
     *
     * @param integer $x    水印区域的横坐标
     * @param integer $y    水印区域的纵坐标
     *
     * @return object
     */
    public function setWatermarkPosition($x, $y) {

        if ($x) {
            $this->_waterX = (int)$x;
        }
        if ($y) {
            $this->_waterY = (int)$y;
        }

        return $this;
    }

    /**
     * 设置水印图片水印区域的透明度.
     *
     * @access public
     *
     * @param integer $param    水印区域的透明度
     *
     * @return object
     */
    public function setWatermarkAlpha($param) {

        if ($param) {
            $this->_alpha = intval($param);
        }

        return $this;
    }

    /**
     * 设置图片剪切区域的开始坐标位置.
     *
     * @access public
     *
     * @param integer $x    剪切区域的横坐标
     * @param integer $y    剪切区域的纵坐标
     *
     * @return object
     */
    public function setClipPosition($x, $y) {

        $this->_isClipFlex = false;

        if ($x) {
            $this->_clipAreaX = (int)$x;
        }
        if ($y) {
            $this->_clipAreaY = (int)$y;
        }

        return $this;
    }

    /**
     * 调整文字水印区域的位置
     *
     * @access protected
     *
     * @param boolean $limitOption 开关(true/false)
     *
     * @return boolean
     */
    protected function _handleWatermarkFontPlace($limitOption = false) {

        if (!$this->_fontX || !$this->_fontY) {
            if (!$this->_textContent) {
                Response::halt('You do not set the watermark text on image!');
            }

            $bbox = imagettfbbox($this->_fontSize, 0, $this->_fontName, $this->_textContent);

            //文字margin_right为5px,特此加5
            $fontW = $bbox[2] - $bbox[0] + 5;
            $fontH = abs($bbox[7] - $bbox[1]);

            if ($limitOption === true && $this->_heightNew && $this->_heightNew) {

                $this->_fontX = ($this->_widthNew > $fontW) ? $this->_widthNew - $fontW : 0;
                $this->_fontY = ($this->_heightNew > $fontH) ? $this->_heightNew - $fontH : 0;

            } else {

                $this->_fontX = ($this->_imageWidth > $fontW) ? $this->_imageWidth - $fontW : 0;
                $this->_fontY = ($this->_imageHeight > $fontH) ? $this->_imageHeight - $fontH : 0;
            }
        }

        return true;
    }

    /**
     * 常设置的文字颜色转换为图片信息.
     *
     * @access protected
     * @return boolean
     */
    protected function _handleFontColor() {

        if (!$this->_fontColor) {
            $this->_fontColor = array(255, 255, 255);
        }

        return imagecolorallocate($this->_image, $this->_fontColor[0], $this->_fontColor[1], $this->_fontColor[2]);
    }

    /**
     * 根据图片原来的宽和高的比例,自适应性处理缩略图的宽度和高度
     *
     * @access protected
     * @return boolean
     */
    protected function _handleImageSize() {

        //当没有所生成的图片的宽度和高度设置时.
        if (!$this->_width || !$this->_height) {
            Response::halt('You do not set the image height size or width size!');
        }

        $perW = $this->_width/$this->_imageWidth;
        $perH = $this->_height/$this->_imageHeight;

        if (ceil($this->_imageHeight*$perW)>$this->_height) {
            $this->_widthNew  = ceil($this->_imageWidth*$perH);
            $this->_heightNew = $this->_height;
        } else {
            $this->_widthNew  = $this->_width;
            $this->_heightNew = ceil($this->_imageHeight*$perW);
        }

        return true;
    }

    /**
     * 生成图片的缩略图.
     *
     * @access public
     *
     * @param string $url 原始图片路径
     * @param string $destName 生成图片的路径(注:无须后缀名)
     *
     * @return boolean
     */
    public function makeThumbnail($url, $destName = null) {

        //参数分析
        if (!$url) {
            return false;
        }

        //原图片分析.
        $this->_parseImageInfo($url);
        $this->_handleImageSize();

        //新图片分析.
        $destImage = imagecreatetruecolor($this->_widthNew, $this->_heightNew);

        //生成新图片.
        imagecopyresampled($destImage, $this->_image, 0, 0, 0, 0, $this->_widthNew, $this->_heightNew, $this->_imageWidth, $this->_imageHeight);

        $this->_createImage($destImage, $destName, $this->_type);
        imagedestroy($destImage);
        imagedestroy($this->_image);

        return true;
    }

    /**
     * 裁剪图片并生成缩略图.
     *
     * @access public
     *
     * @param string $url 原始图片路径
     * @param string $destName 生成图片的路径(注:无须后缀名)
     *
     * @return boolean
     */
    public function makeClipImage($url, $destName = null) {

        //参数分析
        if (!$url) {
            return false;
        }

        //当没有所生成的图片的宽度和高度设置时.
        if (!$this->_width || !$this->_height) {
            Response::halt('You do not set the image height size or width size!');
        }

        //原图片分析.
        $this->_parseImageInfo($url);

        //分析所要生成的裁剪图片的内容(计算需从原图片哪一部分开始裁剪)
        $perTargetHeight = $this->_height/$this->_width;
        $perTargetWidth  = $this->_width/$this->_height;

        //当没有设置剪切区域起始点时,根据原图片尺寸和目标尺寸进行自动分析居中剪切
        if($this->_isClipFlex) {
            if($this->_width > $this->_imageWidth) {
                $this->_width  = $this->_imageWidth;
                $this->_height = ceil($this->_imageWidth * $perTargetHeight);
            }
            if($this->_height > $this->_imageHeight) {
                $this->_height = $this->_imageHeight;
                $this->_width  = ceil($this->_imageHeight * $perTargetWidth);
            }

            $perWidth  = $this->_imageWidth/$this->_width;
            $perHeight = $this->_imageHeight/$this->_height;

            //当原图片尺寸宽和高均大小目标尺寸的宽和高时
            if(ceil($perWidth * $this->_height) > $this->_imageHeight) {
                $srcWidth  = ceil($this->_width * $perHeight);
                $srcHeight = $this->_imageHeight;

                $this->_clipAreaX = ceil(abs($this->_imageWidth - $srcWidth)/2);
                $this->_clipAreaY = 0;
            } else {
                $srcWidth  = $this->_imageWidth;
                $srcHeight = ceil($this->_height * $perWidth);

                $this->_clipAreaX = 0;
                $this->_clipAreaY = ceil(abs($this->_imageHeight - $srcHeight)/2);
            }
        } else {
            //当设置了图片的剪切区域起始位置时，则没有自适应性图片尺寸。如若尺寸不正确，则进行报错
            $imageWidth  =  $this->_imageWidth - $this->_clipAreaX;
            $imageHeight = $this->_imageHeight - $this->_clipAreaY;

            if($this->_width > $imageWidth) {
                $this->_width  = $imageWidth;
                $this->_height = ceil($imageWidth * $perTargetHeight);
            }
            if($this->_height > $imageHeight) {
                $this->_height = $imageHeight;
                $this->_width  = ceil($imageHeight * $perTargetWidth);
            }

            $srcWidth  = $this->_width;
            $srcHeight = $this->_height;
        }

        //新图片分析.
        $destImage = imagecreatetruecolor($this->_width, $this->_height);

        //生成新图片.
        imagecopyresampled($destImage, $this->_image, 0, 0, $this->_clipAreaX, $this->_clipAreaY, $this->_width, $this->_height, $srcWidth, $srcHeight);

        $this->_createImage($destImage, $destName, $this->_type);
        imagedestroy($destImage);
        imagedestroy($this->_image);

        return true;
    }

    /**
     * 生成目标图片.
     *
     * @access protected
     *
     * @param string $destImage 原始图片的路径
     * @param string $destName 生成图片的路径
     * @param string $imageType 图片格式
     *
     * @return boolean
     */
    protected function _createImage($destImage, $destName = null, $imageType) {

        //参数分析
        if (!$destImage || !$imageType) {
            return false;
        }

        if ($destName) {
            switch ($imageType) {

                case 'gif':
                    imagegif ($destImage, $destName.'.gif');
                    break;

                case 'jpg':
                    imagejpeg($destImage, $destName.'.jpg');
                    break;

                case 'png':
                    imagepng($destImage, $destName.'.png');
                    break;

                case 'bmp':
                    imagewbmp($destImage, $destName.'.bmp');
                    break;
            }
        } else {
            switch ($imageType) {

                case 'gif':
                    header('Content-type:image/gif');
                    imagegif ($destImage);
                    break;

                case 'jpg':
                    header('Content-type:image/jpeg');
                    imagejpeg($destImage);
                    break;

                case 'png':
                    header('Content-type:image/png');
                    imagepng($destImage);
                    break;

                case 'bmp':
                    header('Content-type:image/png');
                    imagewbmp($destImage);
                    break;
            }
        }


        return true;
    }

    /**
     * 生成文字水印图片.
     *
     * @access public
     *
     * @param string $imageUrl    背景图片的路径
     * @param string $destName    目标图片的路径(注:无须后缀名)
     *
     * @return boolean
     */
    public function makeTextWatermark($imageUrl, $destName = null) {

        //参数判断
        if (!$imageUrl) {
            return false;
        }

        //分析原图片.
        $this->_parseImageInfo($imageUrl);

        //当所要生成的文字水印图片有大小尺寸限制时(缩略图功能)
        if($this->_width && $this->_height) {

            $this->_handleImageSize();
            //新图片分析.
            $destImage = imagecreatetruecolor($this->_widthNew, $this->_heightNew);

            //生成新图片.
            imagecopyresampled($destImage, $this->_image, 0, 0, 0, 0, $this->_widthNew, $this->_heightNew, $this->_imageWidth, $this->_imageHeight);

            //所生成的图片进行分析.
            $this->_handleWatermarkFontPlace(true);

            $fontColor = $this->_handleFontColor();

            //生成新图片.
            imagettftext($destImage, $this->_fontSize, 0, $this->_fontX, $this->_fontY, $fontColor, $this->_fontName, $this->_textContent);
            $this->_createImage($destImage, $destName, $this->_type);
            imagedestroy($destImage);

        } else {

            //所生成的图片进行分析.
            $this->_handleWatermarkFontPlace();

            $fontColor = $this->_handleFontColor();

            //生成新图片.
            imagettftext($this->_image, $this->_fontSize, 0, $this->_fontX, $this->_fontY, $fontColor, $this->_fontName, $this->_textContent);
            $this->_createImage($this->_image, $destName, $this->_type);
        }

        imagedestroy($this->_image);

        return true;
    }

    /**
     * 获取水印图片信息
     *
     * @access protected
     * @return boolean
     */
    protected function _handleWatermarkImage() {

        if ($this->_image && !$this->_waterImage) {

            $imagePath = (!$this->_imagePath) ? DOIT_ROOT . '/views/source/watermark' . '.' . $this->_type : $this->_imagePath;

            list($this->_waterWidth, $this->_waterHeight, $type) = getimagesize($imagePath);

            switch ($type) {

                case 1:
                    $this->_waterImage = imagecreatefromgif ($imagePath);
                    break;

                case 2:
                    $this->_waterImage = imagecreatefromjpeg($imagePath);
                    break;

                case 3:
                    $this->_waterImage = imagecreatefrompng($imagePath);
                    break;

                case 4:
                    $this->_waterImage = imagecreatefromwbmp($imagePath);
                    break;
            }
        }

        return true;
    }

    /**
     * 调整水印区域的位置,默认位置距图片右下角边沿5像素.
     *
     * @access protected
     * @return boolean
     */
    protected function _handleWatermarkImagePlace($limitOption = false) {

        if (!$this->_waterX || !$this->_waterY) {

            if ($limitOption === true && $this->_widthNew && $this->_heightNew) {

                $this->_waterX = ($this->_widthNew - 5 > $this->_waterWidth) ? $this->_widthNew - $this->_waterWidth - 5 : 0;
                $this->_waterY = ($this->_heightNew - 5 > $this->_waterHeight) ? $this->_heightNew - $this->_waterHeight - 5 : 0;

            } else {

                $this->_waterX = ($this->_imageWidth-5 > $this->_waterWidth) ? $this->_imageWidth - $this->_waterWidth - 5 : 0;
                $this->_waterY = ($this->_imageHeight-5 > $this->_waterHeight) ? $this->_imageHeight - $this->_waterHeight - 5 : 0;
            }
        }

        return true;
    }

    /**
     * 生成图片水印.
     *
     * @access public
     *
     * @param string $imageUrl 原始图片的路径
     * @param string $destName 生成图片的路径(注:不含图片后缀名)
     *
     * @return boolean
     */
    public function makeImageWatermark($imageUrl, $destName = null) {

        //参数分析
        if (!$imageUrl) {
            return false;
        }

        //分析图片信息.
        $this->_parseImageInfo($imageUrl);

        //水印图片的透明度参数
        $this->_alpha = (!$this->_alpha) ? 85 : $this->_alpha;

        //对水印图片进行信息分析.
        $this->_handleWatermarkImage();

        if ($this->_width && $this->_height) {

            $this->_handleImageSize();
            //新图片分析.
            $destImage = imagecreatetruecolor($this->_widthNew, $this->_heightNew);

            //生成新图片.
            imagecopyresampled($destImage, $this->_image, 0, 0, 0, 0, $this->_widthNew, $this->_heightNew, $this->_imageWidth, $this->_imageHeight);

            //分析新图片的水印位置.
            $this->_handleWatermarkImagePlace(true);

            //生成新图片.
            imagecopymerge($destImage, $this->_waterImage, $this->_waterX, $this->_waterY, 0, 0, $this->_waterWidth, $this->_waterHeight, $this->_alpha);
            $this->_createImage($destImage, $destName, $this->_type);
            imagedestroy($destImage);

        } else {

            //分析新图片的水印位置.
            $this->_handleWatermarkImagePlace();

            //生成新图片.
            imagecopymerge($this->_image, $this->_waterImage, $this->_waterX, $this->_waterY, 0, 0, $this->_waterWidth, $this->_waterHeight, $this->_alpha);
            $this->_createImage($this->_image, $destName, $this->_type);
        }

        imagedestroy($this->_image);
        imagedestroy($this->_waterImage);

        return true;
    }
}

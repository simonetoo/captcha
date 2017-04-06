<?php
/**
 * @desc 图片类
 * @author vicens<vicens@linjianxiaoxi.com>
 */

namespace Vicens\Captcha;

use Symfony\Component\HttpFoundation\Response;


/**
 * Class Builder
 * @method width ($width)
 * @method height ($height)
 * @method textColor ($color)
 * @method backgroundColor ($color)
 * @method maxFrontLines ($maxFrontLines)
 * @method maxBehindLines ($maxBehindLines)
 * @method distortion ($distortion)
 * @method maxAngle ($maxAngle)
 * @method maxOffset ($maxOffset)
 * @method quality ($quality)
 * @package Vicens\Captcha
 */
class Image
{

    /**
     * 已生成的图片
     * @var resource
     */
    protected $image;

    /**
     * 验证码
     * @var string
     */
    protected $code;

    /**
     * 验证码设置
     * @var array
     */
    protected $config = array(
        /**
         * 默认验证码宽度
         * @var int
         */
        'width' => 150,
        /**
         * 默认验证码高度
         * @var int
         */
        'height' => 40,
        /**
         * 指定文字颜色
         * @var string
         */
        'textColor' => null,
        /**
         * 文字字体文件
         * @var string
         */
        'textFont' => null,
        /**
         * 指定图片背景色
         * @var string
         */
        'backgroundColor' => null,
        /**
         * 开启失真模式
         * @var bool
         */
        'distortion' => true,
        /**
         * 最大前景线条数
         * @var int
         */
        'maxFrontLines' => null,
        /**
         * 最大背景线条数
         * @val int
         */
        'maxBehindLines' => null,
        /**
         * 文字最大角度
         * @var int
         */
        'maxAngle' => 8,
        /**
         * 文字最大偏移量
         * @var int
         */
        'maxOffset' => 5,
        /**
         * 图片质量
         * @var int
         */
        'quality' => 90
    );


    public function __construct($code, array $config = array())
    {
        $this->code = $code;

        $this->setConfig($config);
    }

    /**
     * 设置验证码配置
     * @param $config
     * @param null $value
     * @return $this
     */
    public function setConfig($config, $value = null)
    {
        if (!is_array($config)) {
            $config = [$config => $value];
        }

        foreach ($config as $key => $value) {
            if (array_key_exists($key, $this->config)) {
                $this->config[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * 获取配置
     * @param  $key
     * @return array|string|number
     */
    public function getConfig($key)
    {
        if ($key) {
            return $this->config[$key];
        }
        return $this->config;
    }

    /**
     * 获取图片
     * @return resource
     */
    public function getImage()
    {
        if (!$this->image) {

            $this->reset();
        }

        return $this->image;
    }

    /**
     * 重置图片
     * @return $this
     */
    public function reset()
    {

        $this->image = $this->build($this->code);

        return $this;
    }

    /**
     * 获取 base64的数据,用做image标签的src
     * @return string
     */
    public function getDataUrl()
    {
        return 'data:image/jpeg;base64,' . base64_encode($this->getContent());
    }

    /**
     * 输出为html
     * @param int $width
     * @param int $height
     * @return string
     */
    public function html($width = null, $height = null)
    {
        $html = '<img src="' . $this->getDataUrl() . '"" alt="captcha"';
        if (is_null($width)) {
            $html .= ' width="' . $width . '"';
        }
        if (is_null($height)) {
            $html .= ' height="' . $height . '"';
        }
        $html .= '>';
        return $html;
    }

    /**
     * 返回Response响应
     * @return Response
     */
    public function response()
    {

        return Response::create($this->getContent(), 200, array('Content-type' => 'image/jpeg'));

    }


    /**
     * 获取图片资源
     * @return mixed
     */
    public function getContext()
    {
        return $this->getImage();
    }

    /**
     * 保存为图片
     * @param $filename
     * @return $this
     */
    public function save($filename)
    {
        $this->output($filename);

        return $this;
    }

    /**
     * 直接输出
     * @param null $filename
     * @return $this
     */
    public function output($filename = null)
    {
        imagejpeg($this->getContext(), $filename, $this->getConfig('quality'));

        return $this;
    }

    /**
     * 获取输出内容
     * @return string
     */
    public function getContent()
    {
        ob_start();
        $this->output();
        return ob_get_clean();
    }

    /**
     * 创建验证码图片
     * @param $code
     * @return resource
     */
    protected function build($code)
    {

        // 图片宽
        $width = $this->getConfig('width');
        // 图片高
        $height = $this->getConfig('height');
        // 背景颜色
        $backgroundColor = $this->getConfig('backgroundColor');


        //随机取一个字体
        $font = $this->getTextFont();

        //根据宽高创建一个背景画布
        $image = imagecreatetruecolor($width, $height);

        if ($backgroundColor == null) {
            $backgroundColor = imagecolorallocate($image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
        } else {
            $color = $backgroundColor;
            $backgroundColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        }
        //填充背景色
        imagefill($image, 0, 0, $backgroundColor);

        //绘制背景干扰线
        $this->drawLines($image, $this->getConfig('maxBehindLines'));

        //写入验证码文字
        $color = $this->renderText($image, $code, $font);

        //绘制前景干扰线
        $this->drawLines($image, $this->getConfig('maxFrontLines'), $color);


        if ($this->getConfig('distortion')) {
            //创建失真
            $image = $this->distort($image, $width, $height, $backgroundColor);
        }

        //如果不指定字体颜色和背景颜色,则使用图像过滤器修饰
        if (function_exists('imagefilter') && is_null($backgroundColor) && is_null($this->getConfig('textColor'))) {
            //颜色翻转 - 1/2几率
            if (mt_rand(0, 1) == 0) {
                imagefilter($image, IMG_FILTER_NEGATE);
            }
            //用边缘检测来突出图像的边缘 - 1/11几率
            if (mt_rand(0, 10) == 0) {
                imagefilter($image, IMG_FILTER_EDGEDETECT);
            }
            //改变图像的对比度
            imagefilter($image, IMG_FILTER_CONTRAST, mt_rand(-50, 10));

            if (mt_rand(0, 5) == 0) {
                //用高斯算法和指定颜色模糊图像
                imagefilter($image, IMG_FILTER_COLORIZE, mt_rand(-80, 50), mt_rand(-80, 50), mt_rand(-80, 50));
            }
        }
        return $image;
    }

    /**
     * 获取一个字体
     * @return string
     */
    protected function getTextFont()
    {
        //指定字体
        if ($this->config['textFont'] && file_exists($this->config['textFont'])) {
            return $this->config['textFont'];
        }
        //随机字体
        return __DIR__ . '/../fonts/' . mt_rand(0, 5) . '.ttf';
    }

    /**
     * 写入验证码到图片中
     * @param $image
     * @param $phrase
     * @param $font
     * @return int
     */
    protected function renderText($image, $phrase, $font)
    {
        $length = strlen($phrase);
        if ($length === 0) {
            return imagecolorallocate($image, 0, 0, 0);
        }

        // 计算文字尺寸
        $size = $this->config['width'] / $length - mt_rand(0, 3) - 1;
        $box = imagettfbbox($size, 0, $font, $phrase);
        $textWidth = $box[2] - $box[0];
        $textHeight = $box[1] - $box[7];
        $x = ($this->config['width'] - $textWidth) / 2;
        $y = ($this->config['height'] - $textHeight) / 2 + $size;

        if (!count($this->config['textColor'])) {
            $textColor = array(mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
        } else {
            $textColor = $this->config['textColor'];
        }
        $color = imagecolorallocate($image, $textColor[0], $textColor[1], $textColor[2]);

        // 循环写入字符,随机角度
        for ($i = 0; $i < $length; $i++) {
            $box = imagettfbbox($size, 0, $font, $phrase[$i]);
            $w = $box[2] - $box[0];
            $angle = mt_rand(-$this->config['maxAngle'], $this->config['maxAngle']);
            $offset = mt_rand(-$this->config['maxOffset'], $this->config['maxOffset']);
            imagettftext($image, $size, $angle, $x, $y + $offset, $color, $font, $phrase[$i]);
            $x += $w;
        }

        return $color;
    }

    /**
     * 画线
     * @param $image
     * @param $width
     * @param $height
     * @param null $color
     */
    protected function renderLine($image, $width, $height, $color = null)
    {
        if ($color === null) {
            $color = imagecolorallocate($image, mt_rand(100, 255), mt_rand(100, 255), mt_rand(100, 255));
        }

        if (mt_rand(0, 1)) { // 横向
            $Xa = mt_rand(0, $width / 2);
            $Ya = mt_rand(0, $height);
            $Xb = mt_rand($width / 2, $width);
            $Yb = mt_rand(0, $height);
        } else { // 纵向
            $Xa = mt_rand(0, $width);
            $Ya = mt_rand(0, $height / 2);
            $Xb = mt_rand(0, $width);
            $Yb = mt_rand($height / 2, $height);
        }
        imagesetthickness($image, mt_rand(1, 3));
        imageline($image, $Xa, $Ya, $Xb, $Yb, $color);
    }

    /**
     * 画线
     * @param $image
     * @param $max
     * @param $color
     */
    protected function drawLines($image, $max, $color = null)
    {
        $square = $this->config['width'] * $this->config['height'];
        $effects = mt_rand($square / 3000, $square / 2000);

        // 计算线条数
        if ($max != null && $max > 0) {
            $effects = min($max, $effects);
        }

        if ($max !== 0) {
            for ($e = 0; $e < $effects; $e++) {

                if ($color) {
                    $this->renderLine($image, $this->config['width'], $this->config['height'], $color);
                } else {
                    $this->renderLine($image, $this->config['width'], $this->config['height']);
                }

            }
        }
    }

    /**
     * 创建失真
     * @param $image
     * @param $width
     * @param $height
     * @param $bg
     * @return resource
     */
    protected function distort($image, $width, $height, $bg)
    {
        $contents = imagecreatetruecolor($width, $height);
        $X = mt_rand(0, $width);
        $Y = mt_rand(0, $height);
        $phase = mt_rand(0, 10);
        $scale = 1.1 + mt_rand(0, 10000) / 30000;
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $Vx = $x - $X;
                $Vy = $y - $Y;
                $Vn = sqrt($Vx * $Vx + $Vy * $Vy);

                if ($Vn != 0) {
                    $Vn2 = $Vn + 4 * sin($Vn / 30);
                    $nX = $X + ($Vx * $Vn2 / $Vn);
                    $nY = $Y + ($Vy * $Vn2 / $Vn);
                } else {
                    $nX = $X;
                    $nY = $Y;
                }
                $nY = $nY + $scale * sin($phase + $nX * 0.2);

                $p = $this->getColor($image, round($nX), round($nY), $bg);

                if ($p == 0) {
                    $p = $bg;
                }

                imagesetpixel($contents, $x, $y, $p);
            }
        }

        return $contents;
    }

    /**
     * 获取颜色
     * @param $image
     * @param $x
     * @param $y
     * @param $background
     * @return int
     */
    protected function getColor($image, $x, $y, $background)
    {
        $L = imagesx($image);
        $H = imagesy($image);
        if ($x < 0 || $x >= $L || $y < 0 || $y >= $H) {
            return $background;
        }

        return imagecolorat($image, $x, $y);
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->config)) {
            $this->config[$name] = $arguments[0];
        }

        return $this;
    }

}
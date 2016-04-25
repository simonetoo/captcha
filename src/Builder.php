<?php
/**
 * 验证码生成类
 * @Author vicens<vicens@linjianxiaoxi.com>
 * @Date  2016-03-10 13:47
 */

namespace Vicens\Captcha;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class Builder implements BuilderInterface
{

    /**
     * 已生成的图片
     * @var Image
     */
    protected $image;

    /**
     * 验证码存储驱动
     * @var Session|SessionInterface
     */
    protected $store;

    /**
     * 当前使用的验证码生成器驱动
     * @var string
     */
    protected $driver;

    /**
     * 当前验证码的命名空间
     * @var string
     */
    protected $name = 'default';


    /**
     * 验证码设置
     * @var array
     */
    protected $config = array(
        /**
         * 默认验证码长度
         * @var int
         */
        'length' => 4,
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
         * 验证码内容
         * @var string
         */
        'charset' => 'abcdefghijklmnpqrstuvwxyz123456789'
    );


    public function __construct(array $config = array())
    {
        $this->store = new Session();

        $this->setConfig($config);
    }

    /**
     * 设置验证码配置
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        foreach ($config as $key => $value) {
            if (array_key_exists($key, $this->config)) {
                $this->config[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 设置验证码高度
     * @param $height
     * @return $this
     */
    public function height($height)
    {
        $this->config['height'] = (int)$height;
        return $this;
    }

    /**
     * 设置验证码宽度
     * @param $width
     * @return $this
     */
    public function width($width)
    {
        $this->config['width'] = (int)$width;
        return $this;
    }

    /**
     * 设置验证码长度
     * @param $length
     * @return $this
     */
    public function length($length)
    {
        $this->config['length'] = (int)$length;
        return $this;
    }

    /**
     * 设置命名空间
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = (string)$name;
        return $this;
    }


    /**
     * 生成验证码
     * @param null $code
     * @return Image
     */
    public function make($code = null)
    {
        if (is_null($code)) {
            $code = $this->generate();
        }

        unset($this->image);

        //生成图片
        $image = $this->build($code);

        //存储验证码
        $this->store->set($this->getFullName(), $this->hash(strtolower($code)));

        $this->image = new Image($image);;

        return $this->image;
    }


    /**
     * 获取命名空间
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 验证并删除验证码
     * @param $input
     * @return bool
     */
    public function check($input)
    {
        $result = $this->test($input);

        //从Session中删除code
        $this->store->remove($this->getFullName());

        return $result;
    }

    /**
     * 仅验证验证码正确性
     * @param $input
     * @return bool
     */
    protected function test($input)
    {

        if (!($this->store->has($this->getFullName()) && $input)) {
            return false;
        }
        //从Session中取回code
        $code = $this->store->get($this->getFullName());

        //返回验证结果
        return password_verify(strtolower($input), $code);

    }

    /**
     * 获取存在session中的key名
     * @return string
     */
    protected function getFullName()
    {
        return 'captcha.' . $this->getName();
    }

    /**
     * 生成验证码
     *
     * @return string
     */
    protected function generate()
    {
        $characters = str_split($this->config['charset']);

        $code = '';
        for ($i = 0; $i < $this->config['length']; $i++) {
            $code .= $characters[rand(0, count($characters) - 1)];
        }

        return $code;
    }

    /**
     * 加密字符串
     * @param $value
     * @return bool|string
     */
    private function hash($value)
    {
        $hash = password_hash($value, PASSWORD_BCRYPT, array('cost' => 10));

        if ($hash === false) {
            throw new \RuntimeException('Bcrypt hashing not supported.');
        }

        return $hash;
    }

    /**
     * 创建验证码图片
     * @param $code
     * @return resource
     */
    protected function build($code)
    {
        //随机取一个字体
        $font = $this->getTextFont();

        //根据宽高创建一个背景画布
        $image = imagecreatetruecolor($this->config['width'], $this->config['height']);

        if ($this->config['backgroundColor'] == null) {
            $backgroundColor = imagecolorallocate($image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
        } else {
            $color = $this->config['backgroundColor'];
            $backgroundColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        }
        //填充背景色
        imagefill($image, 0, 0, $backgroundColor);

        //绘制背景干扰线
        $this->drawBehindLine($image);

        //写入验证码文字
        $color = $this->renderText($image, $code, $font);

        //绘制前景干扰线
        $this->drawFrontLine($image, $color);


        if ($this->config['distortion']) {
            //创建失真
            $image = $this->distort($image, $this->config['width'], $this->config['height'], $backgroundColor);
        }

        //如果不指定字体颜色和背景颜色,则使用图像过滤器修饰
        if (function_exists('imagefilter') && is_null($this->config['backgroundColor']) && is_null($this->config['textColor'])) {
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
     * 绘制背景线
     * @param $image
     */
    protected function drawBehindLine($image)
    {
        $square = $this->config['width'] * $this->config['height'];
        $effects = mt_rand($square / 3000, $square / 2000);
        // 计算线条数
        if ($this->config['maxBehindLines'] != null && $this->config['maxBehindLines'] > 0) {
            $effects = min($this->config['maxBehindLines'], $effects);
        }

        if ($this->config['maxBehindLines'] !== 0) {
            for ($e = 0; $e < $effects; $e++) {
                $this->renderLine($image, $this->config['width'], $this->config['height']);
            }
        }
    }

    /**
     * 绘制前景线
     * @param $image
     * @param $color
     */
    protected function drawFrontLine($image, $color)
    {
        $square = $this->config['width'] * $this->config['height'];
        $effects = mt_rand($square / 3000, $square / 2000);

        // 计算线条数
        if ($this->config['maxFrontLines'] != null && $this->config['maxFrontLines'] > 0) {
            $effects = min($this->config['maxFrontLines'], $effects);
        }

        if ($this->config['maxFrontLines'] !== 0) {
            for ($e = 0; $e < $effects; $e++) {
                $this->renderLine($image, $this->config['width'], $this->config['height'], $color);
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

}
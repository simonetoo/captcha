<?php

namespace Vicens\Captcha;

use Illuminate\Support\Facades\Session;

class Captcha
{
    /**
     * 存储在session中的key
     */
    const SESSION_NAME = '_captcha';

    /**
     * 验证码配置
     * @var array
     */
    protected $config = [
        /**
         * 调试模型
         */
        'debug' => false,
        /**
         * 默认验证码长度
         * @var int
         */
        'length' => 4,
        /**
         * 验证码字符集
         * @var string
         */
        'charset' => 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789',
        /**
         * 是否开启严格模式(区分大小写)
         * @var bool
         */
        'strict' => false,
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
        'maxOffset' => 5
    ];

    /**
     * Captcha constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * 设置验证码配置
     *
     * @param array|string $config 配置数组或配置项key
     * @param mixed $value 配置项值
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
     *
     * @param string|null $key 配置项key
     * @return string|number|array
     */
    public function getConfig($key = null)
    {
        if ($key !== null) {
            return $this->config[$key];
        }

        return $this->config;
    }

    /**
     * 生成验证码
     *
     * @return Image
     */
    public function make()
    {
        $code = $this->generate();

        $hash = password_hash($code, PASSWORD_BCRYPT, array('cost' => 10));

        if ($hash === false) {
            throw new \RuntimeException('Bcrypt hashing not supported.');
        }

        Session::put(self::SESSION_NAME, $hash);

        return new Image($this->build($code));
    }

    /**
     * 仅测试正确性, 不删除验证码
     *
     * @param string $input
     * @return bool
     */
    public function test($input)
    {
        if ($this->config['debug']) {
            return true;
        } elseif (!(Session::has(self::SESSION_NAME) && $input)) {
            return false;
        }

        $code = Session::get(self::SESSION_NAME);

        if ($this->config['strict']) {
            // 开启严格模式
            password_verify($input, $code);
        }

        //返回验证结果
        return password_verify(strtoupper($input), $code);
    }

    /**
     * 检测正确性,并删除验证码
     *
     * @param string $input
     * @return bool
     */
    public function check($input)
    {
        $result = $this->test($input);
        Session::forget(self::SESSION_NAME);

        return $result;
    }

    /**
     * 生成验证码
     *
     * @return string
     */
    protected function generate()
    {
        $characters = str_split($this->getConfig('charset'));
        $length = $this->getConfig('length');

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, count($characters) - 1)];
        }

        if ($this->config['strict']) {
            return $code;
        }

        return strtoupper($code);
    }

    /**
     * 创建验证码图片
     *
     * @param string $code
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

        // 随机取一个字体
        $font = $this->getTextFont();

        // 根据宽高创建一个背景画布
        $image = imagecreatetruecolor($width, $height);

        if ($backgroundColor === null) {
            $backgroundColor = imagecolorallocate($image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
        } else {
            $color = $backgroundColor;
            $backgroundColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        }
        // 填充背景色
        imagefill($image, 0, 0, $backgroundColor);

        // 绘制背景干扰线
        $this->drawLines($image, $this->getConfig('maxBehindLines'));

        // 写入验证码文字
        $color = $this->renderText($image, $code, $font);

        // 绘制前景干扰线
        $this->drawLines($image, $this->getConfig('maxFrontLines'), $color);

        if ($this->getConfig('distortion')) {
            // 创建失真
            $image = $this->createDistortion($image, $width, $height, $backgroundColor);
        }

        //如果不指定字体颜色和背景颜色,则使用图像过滤器修饰
        if (function_exists('imagefilter') && is_null($backgroundColor) && is_null($this->getConfig('textColor'))) {
            // 颜色翻转 - 1/2几率
            if (mt_rand(0, 1) == 0) {
                imagefilter($image, IMG_FILTER_NEGATE);
            }
            // 用边缘检测来突出图像的边缘 - 1/11几率
            if (mt_rand(0, 10) == 0) {
                imagefilter($image, IMG_FILTER_EDGEDETECT);
            }
            // 改变图像的对比度
            imagefilter($image, IMG_FILTER_CONTRAST, mt_rand(-50, 10));

            if (mt_rand(0, 5) == 0) {
                // 用高斯算法和指定颜色模糊图像
                imagefilter($image, IMG_FILTER_COLORIZE, mt_rand(-80, 50), mt_rand(-80, 50), mt_rand(-80, 50));
            }
        }
        return $image;
    }

    /**
     * 创建失真
     *
     * @param resource $image
     * @param int $width
     * @param int $height
     * @param int $backgroundColor
     * @return resource
     */
    protected function createDistortion($image, $width, $height, $backgroundColor)
    {
        //创建失真
        $contents = imagecreatetruecolor($width, $height);
        $rWidth = mt_rand(0, $width);
        $rHeight = mt_rand(0, $height);
        $phase = mt_rand(0, 10);
        $scale = 1.1 + mt_rand(0, 10000) / 30000;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $vX = $x - $rWidth;
                $vY = $y - $rHeight;
                $vN = sqrt($vX * $vX + $vY * $vY);

                if ($vN != 0) {
                    $vN2 = $vN + 4 * sin($vN / 30);
                    $nX = $rWidth + ($vX * $vN2 / $vN);
                    $nY = $rHeight + ($vY * $vN2 / $vN);
                } else {
                    $nX = $rWidth;
                    $nY = $rHeight;
                }
                $nY = $nY + $scale * sin($phase + $nX * 0.2);

                $pixel = $this->getColor($image, round($nX), round($nY), $backgroundColor);

                if ($pixel == 0) {
                    $pixel = $backgroundColor;
                }

                imagesetpixel($contents, $x, $y, $pixel);
            }
        }

        return $contents;
    }

    /**
     * 获取一个字体
     *
     * @return string
     */
    protected function getTextFont()
    {
        // 指定字体
        if ($this->getConfig('textFont') && file_exists($this->getConfig('textFont'))) {
            return $this->getConfig('textFont');
        }
        // 随机字体
        return __DIR__ . '/../fonts/' . mt_rand(0, 5) . '.ttf';
    }

    /**
     * 写入验证码到图片中
     *
     * @param resource $image
     * @param string $phrase
     * @param string $font
     * @return int
     */
    protected function renderText($image, $phrase, $font)
    {
        $length = strlen($phrase);
        if ($length === 0) {
            return imagecolorallocate($image, 0, 0, 0);
        }

        // 计算文字尺寸
        $size = $this->getConfig('width') / $length - mt_rand(0, 3) - 1;
        $box = imagettfbbox($size, 0, $font, $phrase);
        $textWidth = $box[2] - $box[0];
        $textHeight = $box[1] - $box[7];
        $x = ($this->getConfig('width') - $textWidth) / 2;
        $y = ($this->getConfig('height') - $textHeight) / 2 + $size;

        if (!count($this->getConfig('textColor'))) {
            $textColor = array(mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
        } else {
            $textColor = $this->getConfig('textColor');
        }
        $color = imagecolorallocate($image, $textColor[0], $textColor[1], $textColor[2]);

        // 循环写入字符,随机角度
        for ($i = 0; $i < $length; $i++) {
            $box = imagettfbbox($size, 0, $font, $phrase[$i]);
            $w = $box[2] - $box[0];
            $angle = mt_rand(-$this->getConfig('maxAngle'), $this->getConfig('maxAngle'));
            $offset = mt_rand(-$this->getConfig('maxOffset'), $this->getConfig('maxOffset'));
            imagettftext($image, $size, $angle, $x, $y + $offset, $color, $font, $phrase[$i]);
            $x += $w;
        }

        return $color;
    }

    /**
     * 画线
     *
     * @param resource $image
     * @param int $width
     * @param int $height
     * @param int|null $color
     */
    protected function renderLine($image, $width, $height, $color = null)
    {
        $color = $color ?: imagecolorallocate($image, mt_rand(100, 255), mt_rand(100, 255), mt_rand(100, 255));

        if (mt_rand(0, 1)) {
            // 横向
            $xA = mt_rand(0, $width / 2);
            $yA = mt_rand(0, $height);
            $xB = mt_rand($width / 2, $width);
            $yB = mt_rand(0, $height);
        } else {
            // 纵向
            $xA = mt_rand(0, $width);
            $yA = mt_rand(0, $height / 2);
            $xB = mt_rand(0, $width);
            $yB = mt_rand($height / 2, $height);
        }
        imagesetthickness($image, mt_rand(1, 3));
        imageline($image, $xA, $yA, $xB, $yB, $color);
    }

    /**
     * 画线
     *
     * @param resource $image
     * @param int $max
     * @param int|null $color
     */
    protected function drawLines($image, $max, $color = null)
    {
        $square = $this->getConfig('width') * $this->getConfig('height');
        $effects = mt_rand($square / 3000, $square / 2000);

        // 计算线条数
        if ($max != null && $max > 0) {
            $effects = min($max, $effects);
        }

        if ($max !== 0) {
            for ($e = 0; $e < $effects; $e++) {

                if ($color !== null) {
                    $this->renderLine($image, $this->getConfig('width'), $this->getConfig('height'), $color);
                } else {
                    $this->renderLine($image, $this->getConfig('width'), $this->getConfig('height'));
                }

            }
        }
    }

    /**
     * 获取颜色
     *
     * @param resource $image
     * @param int $width
     * @param int $height
     * @param int $background
     * @return int
     */
    protected function getColor($image, $width, $height, $background)
    {
        $sWidth = imagesx($image);
        $sHeight = imagesy($image);
        if ($width < 0 || $width >= $sWidth || $height < 0 || $height >= $sHeight) {
            return $background;
        }

        return imagecolorat($image, $width, $height);
    }

    /**
     * @param string $name
     * @param array $arguments
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
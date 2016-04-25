<?php
/**
 * 验证码图片操作类
 * @Author vicens<vicens@linjianxiaoxi.com>
 * @Date  2016-03-10 13:48
 */

namespace Vicens\Captcha;

use \Symfony\Component\HttpFoundation\Response;

class Image
{
    /**
     * 验证码图片
     * @var resource
     */
    protected $image;

    /**
     * 图片质量
     * @var int
     */
    protected $quality = 90;

    /**
     * Image constructor.
     * @param $image
     */
    public function __construct($image)
    {
        $this->image = $image;
    }

    /**
     * 设置图片质量
     * @param $quality
     * @return $this
     */
    public function quality($quality)
    {
        $this->quality = (int)$quality;
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
        return $this->image;
    }

    /**
     * 保存为图片
     * @param $filename
     */
    public function save($filename)
    {
        imagejpeg($this->getContext(), $filename, $this->quality);
    }

    /**
     * 直接输出
     */
    public function output()
    {
        imagejpeg($this->getContext(), null, $this->quality);
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
}
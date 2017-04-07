<?php
/**
 * @description 验证码图片类
 * @author vicens <vicens.shi@qq.com>
 */

namespace Vicens\Captcha;

use Symfony\Component\HttpFoundation\Response;


class Image
{

    /**
     * 已生成的图片
     *
     * @var resource
     */
    protected $image;


    public function __construct($image)
    {
        $this->image = $image;
    }

    /**
     * 获取 base64的数据,用做image标签的src
     *
     * @return string
     */
    public function getDataUrl()
    {
        return 'data:image/jpeg;base64,' . $this->getBase64();
    }

    /**
     * 获取base64
     *
     * @return string
     */
    public function getBase64()
    {
        return base64_encode($this->getContent());
    }

    /**
     * 作为img标签输出
     *
     * @param int|null $width img宽
     * @param int|null $height img高
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
     *
     * @return Response
     */
    public function response()
    {

        return Response::create($this->getContent(), 200, array('Content-type' => 'image/jpeg'));

    }

    /**
     * 获取图片
     *
     * @return resource
     */
    public function getImage()
    {

        return $this->image;
    }

    /**
     * 获取图片
     *
     * @return resource
     */
    public function getContext()
    {
        return $this->getImage();
    }

    /**
     * 保存为图片
     *
     * @param string $filename 文件名
     * @return $this
     */
    public function save($filename)
    {
        $this->output($filename);

        return $this;
    }

    /**
     * 直接输出
     *
     * @param string|null $filename 文件名
     * @return $this
     */
    public function output($filename = null)
    {
        imagejpeg($this->getContext(), $filename);

        return $this;
    }

    /**
     * 获取输出内容
     *
     * @return string
     */
    public function getContent()
    {
        ob_start();
        $this->output();
        return ob_get_clean();
    }

}
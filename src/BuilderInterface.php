<?php
/**
 * 验证码生成器接口
 * @Author vicens<vicens@linjianxiaoxi.com>
 * @Date  2016-03-10 16:37
 */

namespace Vicens\Captcha;


interface BuilderInterface
{

    public function make($code = null);

    /**
     * 设置命名空间
     * @param $name
     * @return $this
     */
    public function name($name);

    /**
     * 设置验证码高度
     * @param $height
     * @return $this
     */
    public function height($height);

    /**
     * 设置验证码宽度
     * @param $width
     * @return $this
     */
    public function width($width);

    /**
     * 获取命名空间
     * @return string
     */
    public function getName();

    /**
     * 验证并删除验证码
     * @param $input
     * @return bool
     */
    public function check($input);
}
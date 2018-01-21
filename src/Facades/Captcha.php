<?php

namespace Vicens\Captcha\Facades;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade;

/**
 * @method \Vicens\Captcha\Image make()
 * @method \Vicens\Captcha\Captcha setConfig(array | string $config, mixed $value)
 * @method string|number|array getConfig(string | null $key)
 * @method bool test(string $input)
 * @method bool check(string $input)
 * @see \Vicens\Captcha\Captcha
 */
class Captcha extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'captcha';
    }

    /**
     * 生成验证码图片标签
     *
     * @return HtmlString
     */
    public static function image()
    {
        return new HtmlString('<img src="' . self::src() . '" alt="captcha"/>');
    }

    /**
     * 获取图片验证码的URL
     *
     * @return string
     */
    public static function src()
    {
        return self::url();
    }

    /**
     * 返回验证码的URL
     *
     * @return string
     */
    public static function url()
    {
        return URL::to(Config::get('captcha.route', '/captcha'));
    }
}
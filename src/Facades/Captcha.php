<?php

namespace Vicens\Captcha\Facades;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade;

class Captcha extends Facade
{


    protected static function getFacadeAccessor()
    {
        return 'captcha';
    }

    /**
     * 生成验证码图片标签
     *
     * @return string
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
        return Route::to(Config::get('captcha.routeName', 'captcha'));
    }
}
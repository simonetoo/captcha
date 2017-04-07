<?php
/**
 * @description laravel 门面
 * @author vicens <vicens.shi@qq.com>
 */

namespace Vicens\Captcha\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;


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
     * 生成验证码路由
     *
     * @param string $path
     */
    public static function routes($path = '/captcha')
    {

        app('router')->get($path, function () {

            return app('captcha')->make()->response();

        })->name('captcha');
    }

    /**
     * 返回验证码的URL
     *
     * @return string
     */
    public static function url()
    {
        return route('captcha');
    }

    /**
     * 注册表单验证器
     */
    public static function validations()
    {

        app('validator')->extend('captcha', function ($attribute, $value) {

            return app('captcha')->check($value);
        });

    }

}
<?php
/**
 * @desc laravel 门面
 * @author vicens<vicens@linjianxiaoxi.com>
 */


namespace Vicens\Captcha\Facades;

use Illuminate\Http\Request;
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
     * @param string $name
     * @return string
     */
    public static function image($name = null)
    {
        return new HtmlString('<img src="' . self::src($name) . '" alt="captcha"/>');
    }

    /**
     * 获取图片验证码的URL
     * @param null $name
     * @return string
     */
    public static function src($name = null)
    {
        return self::url($name);
    }

    /**
     * 生成验证码路由
     * @param string $path
     */
    public static function routes($path = null)
    {
        if (!$path) {
            $path = config('captcha.path');
        }

        app('router')->get($path, function (Request $request) {

            $name = $request->get('name');

            return app('captcha')->make($name)->response();

        })->name('captcha');
    }

    /**
     * 返回验证码的URL
     * @param string $name
     * @return string
     */
    public static function url($name = null)
    {
        $parameters = [];

        if ($name) {
            $parameters['name'] = $name;
        }

        return route('captcha', $parameters);
    }

    /**
     * 注册表单验证器
     */
    public static function validations()
    {
        // Validator extensions
        app('validator')->extend('captcha', function ($attribute, $value, array $parameters = []) {

            return app('captcha')->check($value, array_first($parameters));
        });

    }

}
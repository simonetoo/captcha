<?php
/**
 * laravel 工厂
 * @Author vicens<vicens@linjianxiaoxi.com>
 * @Date  2016-03-10 22:48
 */

namespace Vicens\Captcha\Facades;


use Illuminate\Support\Facades\Facade;

class Captcha extends Facade
{
    
    protected static function getFacadeAccessor()
    {
        return 'captcha';
    }
}
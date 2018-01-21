<?php

namespace Vicens\Captcha\Providers;

use Vicens\Captcha\Captcha;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Vicens\Captcha\Middleware\CaptchaMiddleware;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * 按需加载
     * @var bool
     */
    protected $defer = true;

    /**
     * boot
     */
    public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__ . '/../config.php' => config_path('captcha.php')
        ], 'config');
    }

    /**
     * register
     */
    public function register()
    {

        //合并配置项
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'captcha');

        $config = Config::get('captcha', array());

        if (Arr::get($config, 'debug') === null) {
            // 自动开启调试模型
            Arr::set($config, 'debug', Config::get('app.debug', false));
        }

        //注册服务
        $this->app->singleton('captcha', function () use ($config) {
            return new Captcha($config);
        });

        // 注册中间件
        Route::aliasMiddleware(
            Arr::get($config, 'middlewareName', 'captcha'),
            CaptchaMiddleware::class
        );

        // 注册验证器
        Validator::extend(Arr::get($config, 'validationName', 'captcha'), function ($attribute, $value) {

            return $this->app['captcha']->check($value);
        });

    }

    /**
     * provides
     *
     * @return array
     */
    public function provides()
    {
        return ['captcha'];
    }
}
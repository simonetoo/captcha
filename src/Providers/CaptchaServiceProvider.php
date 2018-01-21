<?php

namespace Vicens\Captcha\Providers;

use Vicens\Captcha\Captcha;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class CaptchaServiceProvider extends ServiceProvider
{

    /**
     * boot
     */
    public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__ . '/../config.php' => config_path('captcha.php')
        ], 'config');

        // 注册路由
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        // 注册验证器
        Validator::extend('captcha', function ($attribute, $value) {

            return $this->app['captcha']->check($value);
        });

    }

    /**
     * register
     */
    public function register()
    {

        // 合并配置项
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'captcha');

        // 注册服务
        $this->app->singleton('captcha', function () {

            $config = Config::get('captcha', array());

            if (Arr::get($config, 'debug') === null) {
                // 自动开启调试模型
                Arr::set($config, 'debug', Config::get('app.debug', false));
            }

            return new Captcha($config);
        });
    }

    /**
     * provides
     *
     * @return string[]
     */
    public function provides()
    {
        return ['captcha'];
    }
}
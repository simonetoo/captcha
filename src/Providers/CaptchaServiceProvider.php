<?php
/**
 * @description 服务提供者
 * @author vicens <vicens.shi@qq.com>
 */

namespace Vicens\Captcha\Providers;

use Vicens\Captcha\Captcha;

use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * 按需加载
     * @var bool
     */
    protected $defer = true;


    public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__ . '/../../config/captcha.php' => config_path('captcha.php')
        ], 'config');
    }

    /**
     * 注册服务
     */
    public function register()
    {

        //合并配置项
        $this->mergeConfigFrom(__DIR__ . '/../../config/captcha.php', 'captcha');

        //注册到容器中
        $this->app->singleton('captcha', function () {

            $config = config('captcha', array());

            return new Captcha($config);

        });
    }

    /**
     * 延迟加载的服务
     *
     * @return array
     */
    public function provides()
    {
        return ['captcha'];
    }
}
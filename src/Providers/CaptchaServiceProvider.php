<?php
/**
 * @desc laravel 服务提供者
 * @author vicens<vicens@linjianxiaoxi.com>
 */


namespace Vicens\Captcha\Providers;

use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Session\Session;
use Vicens\Captcha\Captcha;

use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * 按需加载
     * @var bool
     */
    protected $defer = true;

    /**
     *
     */
    public function boot()
    {
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

            return new Captcha(new Session(), $config);

        });
    }

    /**
     * 延迟加载的服务
     * @return array
     */
    public function provides()
    {
        return ['captcha'];
    }
}
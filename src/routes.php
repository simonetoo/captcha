<?php

use Vicens\Captcha\Facades\Captcha;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

// 注册路由
Route::get(Config::get('captcha.route', '/captcha'), function () {

    return Captcha::make()->response();
});
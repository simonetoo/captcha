<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Vicens\Captcha\Facades\Captcha;

// 注册路由
Route::get(Config::get('captcha.route', 'captcha'), function () {

    return Captcha::make()->response();

})->name(Config::get('captcha.routeName', 'captcha'));
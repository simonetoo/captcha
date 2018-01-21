<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

// 注册路由
Route::get(Config::get('captcha.route', '/captcha'), 'Vicens\\Captcha\Controller\CaptchaController@image');
<?php

namespace Vicens\Captcha\Controller;

use Illuminate\Routing\Controller;
use Vicens\Captcha\Facades\Captcha;

class CaptchaController extends Controller
{

    /**
     * 生成二维码图片
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function image()
    {
        return Captcha::make()->response();
    }
}
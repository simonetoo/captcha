<?php

namespace Vicens\Captcha\Controller;

use Vicens\Captcha\Facades\Captcha;

class CaptchaController
{

    /**
     * 生成二维码图片
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function image()
    {
        return Captcha::make()->response();
    }
}

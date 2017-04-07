<?php
/**
 * @description 验证码异常类
 * @author vicens <vicens.shi@qq.com>
 */


namespace Vicens\Captcha\Exceptions;

use \Exception;
use Throwable;

class InvalidCaptcha extends Exception
{

    public function __construct($message = "Invalid Captcha!", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
<?php

namespace Vicens\Captcha\Exceptions;

use Throwable;
use \Exception;

class InvalidCaptcha extends Exception
{

    public function __construct($message = "Invalid Captcha!", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
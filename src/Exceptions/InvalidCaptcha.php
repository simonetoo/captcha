<?php

namespace Vicens\Captcha\Exceptions;

use Throwable;
use \InvalidArgumentException;

class InvalidCaptcha extends InvalidArgumentException
{

    public function __construct($message = 'Invalid Captcha!', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
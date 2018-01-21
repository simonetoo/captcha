<?php

namespace Vicens\Captcha\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use Vicens\Captcha\Captcha;
use Illuminate\Http\Request;

class CaptchaMiddleware
{

    /**
     * @var Captcha
     */
    protected $captcha;


    public function __construct(Captcha $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle(Request $request, Closure $next)
    {

        Validator::validate($request->only('captcha'), [
            'captcha' => 'captcha'
        ]);

        return $next($request);
    }
}
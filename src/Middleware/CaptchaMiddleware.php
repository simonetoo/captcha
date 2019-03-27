<?php

namespace Vicens\Captcha\Middleware;

use Closure;
use Illuminate\Contracts\Validation\Factory;

class CaptchaMiddleware
{

    /**
     * @var Factory
     */
    protected $validator;

    /**
     * CaptchaMiddleware constructor.
     *
     * @param Factory $validator
     */
    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param $request
     * @param Closure $next
     * @param string $captchaKey
     * @return mixed
     */
    public function handle($request, Closure $next, $captchaKey = 'captcha')
    {
        $this->validate($request, $captchaKey);

        return $next($request);
    }

    /**
     * 验证
     *
     * @param $request
     * @param $captchaKey
     */
    protected function validate($request, $captchaKey)
    {
        $this->validator->make(
            $request->only($captchaKey),
            $this->rules($captchaKey),
            $this->message($captchaKey)
        )->validate();
    }

    /**
     * 验证规则
     *
     * @param string $captchaKey
     * @return array
     */
    protected function rules($captchaKey)
    {
        return [
            $captchaKey => 'required|captcha'
        ];
    }

    /**
     * 验证的错误消息
     *
     * @param string $captchaKey
     * @return array
     */
    protected function message($captchaKey)
    {
        return [];
    }
}

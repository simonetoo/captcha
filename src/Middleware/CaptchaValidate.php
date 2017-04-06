<?php
/**
 * @desc laravel 验证码验证的中间件
 * @author vicens<vicens@linjianxiaoxi.com>
 */



namespace Vicens\Captcha\Middleware;


use Illuminate\Http\Request;
use Vicens\Captcha\Captcha;
use Closure;
use Vicens\Captcha\Exceptions\InvalidCaptcha;

class CaptchaValidate
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
     * @param string $name
     * @return mixed
     * @throws InvalidCaptcha
     */
    public function handle(Request $request, Closure $next, $name = null)
    {

        // 验证
        if (!$this->captcha->check($this->getCaptcha($request), $name)) {

            // 验证码错误
            throw new InvalidCaptcha();
        }

        return $next($request);
    }

    /**
     * 返回用户输入的验证码
     * @param Request $request
     * @return string
     */
    protected function getCaptcha(Request $request)
    {
        return $request->get('captcha');
    }
}
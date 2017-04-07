<?php
/**
 * @description HTTP中间件
 * @author vicens <vicens.shi@qq.com>
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
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        // 验证
        if (!$this->check($this->getCaptcha($request))) {

            $this->throwInvalidException();

        }

        return $next($request);
    }

    /**
     * 抛出验证码错误的异常
     *
     * @throws InvalidCaptcha
     */
    protected function throwInvalidException()
    {
        // 验证码错误
        throw new InvalidCaptcha();
    }

    /**
     * 验证
     *
     * @param string $input
     * @return bool
     */
    protected function check($input)
    {
        return $this->captcha->check($input);
    }

    /**
     * 返回用户输入的验证码
     *
     * @param Request $request
     * @return string
     */
    protected function getCaptcha(Request $request)
    {
        return $request->get('captcha');
    }
}
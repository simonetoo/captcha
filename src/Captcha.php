<?php
/**
 * @desc 验证码类
 * @author vicens<vicens@linjianxiaoxi.com>
 */



namespace Vicens\Captcha;

use Symfony\Component\HttpFoundation\Session\Session;

class Captcha
{
    const DEFAULT_NAME = 'default';

    protected $config = [
        /**
         * 默认验证码长度
         * @var int
         */
        'length' => 4,
        /**
         * 验证码内容
         * @var string
         */
        'charset' => 'abcdefghijklmnpqrstuvwxyz123456789'
    ];

    /**
     * 存储驱动
     * @var Session
     */
    protected $store;


    public function __construct(Session $session, array $config = [])
    {
        $this->store = $session;

        $this->setConfig($config);
    }

    /**
     * 设置验证码配置
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        foreach ($config as $key => $value) {
            if (array_key_exists($key, $this->config)) {
                $this->config[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 生成验证码
     * @param string $name
     * @param array $config
     * @return Image
     */
    public function make($name = null, array $config = [])
    {

        $config = array_merge($config, $this->config);

        $code = $this->generate($config['charset'], $config['length']);

        $this->store($name, $code);

        return new Image($code, $config);
    }

    /**
     * 仅测试正确性, 不删除验证码
     * @param $input
     * @param string $name
     * @return bool
     */
    public function test($input, $name = null)
    {

        if (!($this->has($name) && $input)) {
            return false;
        }

        //返回验证结果
        return strtolower($input) == $this->get($name);//password_verify(strtolower($input), $this->get($name));
    }

    /**
     * 检测正确性,并删除验证码
     * @param $input
     * @param string $name
     * @return bool
     */
    public function check($input, $name = null)
    {
        $result = $this->test($input, $name);

        $this->remove($name);

        return $result;
    }

    /**
     * 生成验证码
     * @param array|string $charset
     * @param int $length
     * @return string
     */
    protected function generate($charset, $length = 4)
    {
        $characters = str_split($charset);

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, count($characters) - 1)];
        }

        return $code;
    }


    /**
     * 加密字符串
     * @param $value
     * @return bool|string
     */
    protected function hash($value)
    {
        $hash = password_hash($value, PASSWORD_BCRYPT, array('cost' => 10));

        if ($hash === false) {
            throw new \RuntimeException('Bcrypt hashing not supported.');
        }

        return $value;
    }

    /**
     * 返回存储到session中的键全名
     * @param string $name
     * @return string
     */
    protected function getFullName($name)
    {
        return 'captcha.' . $name ?: self::DEFAULT_NAME;
    }

    /**
     * @param $name
     * @return bool
     */
    protected function has($name)
    {
        return $this->store->has($this->getFullName($name));
    }

    /**
     * 存储验证码
     * @param $name
     * @param $code
     */
    protected function store($name, $code)
    {
        $this->store->set($this->getFullName($name), $this->hash(strtolower($code)));
    }

    /**
     * 从存储中获取验证码
     * @param $name
     * @return mixed
     */
    protected function get($name)
    {
        return $this->store->get($this->getFullName($name));
    }

    /**
     * 从存储中删除验证码
     * @param $name
     * @return mixed
     */
    protected function remove($name)
    {
        return $this->store->remove($this->getFullName($name));
    }
}
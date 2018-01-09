
[![Build Status](https://scrutinizer-ci.com/g/vicens/captcha/badges/build.png?b=master)](https://scrutinizer-ci.com/g/vicens/captcha/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/vicens/captcha/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/vicens/captcha/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/vicens/captcha/v/stable)](https://packagist.org/packages/vicens/captcha)
[![Total Downloads](https://poser.pugx.org/vicens/captcha/downloads)](https://packagist.org/packages/vicens/captcha)
[![License](https://poser.pugx.org/vicens/captcha/license)](https://packagist.org/packages/vicens/captcha)

## 简介

一个简单的laravel5图形验证码扩展包

## 环境要求
1. PHP >= 5.5
2. php-gd
3. [illuminate\support](https://github.com/illuminate/support) ^5.1
4. [symfony/http-foundation](https://github.com/symfony/http-foundation) >= 2.0

## 安装

### 使用composer安装扩展包

先通过`composer`安装扩展包到项目中

```php
composer require vicens/captcha
```

### 注册服务提供者和别名(Laravel5.5无需手动注册)

在`config/app.php` 配置文件的`providers`数组中，注册服务提供者：

```php
'providers' => [
    // Other service providers...
    \Vicens\Captcha\Providers\CaptchaServiceProvider::class
]
```

如果你使用`Captcha`别名的话，在`aliases`数组中注册别名：

```php
'aliases' => [
    // Other facades...
    'Captcha' => \Vicens\Captcha\Facades\Captcha::class
]
```

### 注册路由、验证器和路由中间件

如果你希望使用`Captcha`的默认路由，你需要在`routes/web.php`或者`AppServiceProvider`的`boot`方法中调用`Captcha::routes`方法：

```php
\Vicens\Captcha\Facades\Captcha::routes($path = '/captcha');
```
`Captcha`提供了一个有用的表单验证器，你可以在`AppServiceProvider`的`boot`方法中注册验证器：

```php
\Vicens\Captcha\Facades\Captcha::validations();
```

`Captcha`还提供了一个路由中间件，你可以在`app/Http/Kernel.php`的`$routeMiddleware`数组中注册它：
```php
protected $routeMiddleware = [
       // Other middlewares
       'captcha' => \Vicens\Captcha\Middleware\CaptchaValidate::class
    ];
```

## 配置

如果你想使用自己的配置,你可以执行以下命令发布配置文件`config/captcha.php`：

```php
php artisan vendor:publish --provider=\Vicens\Captcha\Providers\CaptchaServiceProvider
```

```php
return array(
    /**
     * 默认验证码长度
     * @var int
     */
    'length' => 4,
    /**
     * 验证码字符集
     * @var string
     */
    'charset' => 'abcdefghijklmnpqrstuvwxyz123456789',
    /**
     * 默认验证码宽度
     * @var int
     */
    'width' => 150,
    /**
     * 默认验证码高度
     * @var int
     */
    'height' => 40,
    /**
     * 指定文字颜色
     * @var string
     */
    // 'textColor' => null,
    /**
     * 文字字体文件
     * @var string
     */
    // 'textFont' => null,
    /**
     * 指定图片背景色
     * @var string
     */
    // 'backgroundColor' => null,
    /**
     * 开启失真模式
     * @var bool
     */
    'distortion' => true,
    /**
     * 最大前景线条数
     * @var int
     */
    // 'maxFrontLines' => null,
    /**
     * 最大背景线条数
     * @val int
     */
    // 'maxBehindLines' => null,
    /**
     * 文字最大角度
     * @var int
     */
    'maxAngle' => 8,
    /**
     * 文字最大偏移量
     * @var int
     */
    'maxOffset' => 5
);
```

## 基本用法

#### 生成验证码图片实例

```php
use \Vicens\Captcha\Facades\Captcha;

$image = Captcha::make();
$image = Captcha::setConfig($config)->make();
$image = Captcha::width(100)->height(40)->make();
```
#### 图片实例用法
直接返回`Response`对象：
```php
$image->response();
```
直接输出给浏览器：
```php
$image->output();
```
输出`img`标签：
```php
Captcha::html($width, $height);
```
返回`base64`编码：
```php
$image->getBase64();
```
返回`base64`Url地址：
```php
$image->getDataUrl();
```
返回图片二进制内容：
```php
$image->getContent();
```
保存图片到服务器：
```php
$image->save($filename);
```

#### 验证和测试
仅测试验证码的正确性：
```php
Captcha::test($input);
```
检测验证码的正确性，并且从缓存中删除验证码：
```php
Captcha::check($input);
```

#### 使用中间件

在路由上使用：

```php
Route::post('login','LoginController@login')->middleware('captcha');
```

在控制器中使用：
```php
public function __constructor(){
   $this->middleware('captcha');
}
```

#### 使用表单验证器

在控制器中使用：

```php
$this->validation([
   'code'=>'captcha'
]);
```
在`Request`中使用：
 ```php
public function rules()
{
    return [
        'code' => 'captcha'
    ];
}
```

#### 外观方法

返回URL：
```php
Captcha::url();
Captcha::src();
```
返回`img`标签：
```php
Captcha::image();
```
注册路由：
```php
Captcha::routes();
```
注册表单验证器：
```php
Captcha::validations();
```

## 开源协议

[MIT license](http://opensource.org/licenses/MIT).

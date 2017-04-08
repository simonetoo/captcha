
[![Build Status](https://scrutinizer-ci.com/g/vicens/captcha/badges/build.png?b=master)](https://scrutinizer-ci.com/g/vicens/captcha/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/vicens/captcha/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/vicens/captcha/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/vicens/captcha/v/stable)](https://packagist.org/packages/vicens/captcha)
[![Total Downloads](https://poser.pugx.org/vicens/captcha/downloads)](https://packagist.org/packages/vicens/captcha)
[![License](https://poser.pugx.org/vicens/captcha/license)](https://packagist.org/packages/vicens/captcha)

## Introduction

An captcha component for laravel5 applications.

## Requirements
1. PHP >= 5.5
2. php-gd
3. laravel/framework >= 5.1
3. symfony/http-foundation >= 2.0

## Installation

Install captcha via composer.

```php
composer require vicens/captcha
```

Next, register the service provider in the providers array of your `config/app.php` configuration file:

```php
'providers' => [
    // Other service providers...
    Vicens\Captcha\Providers\CaptchaServiceProvider::class
]
```

Also, add the `Captcha` facade to the aliases array:

```php
'aliases' => [
    // Other facades...
    'Captcha' => Vicens\Captcha\Facades\Captcha::class
]
```

If you are going to use Captcha's default routes, you should call the `Captcha::routes` method within the `routes/web.php` or `boot` method of your  `AppServiceProvider`:

```php
Captcha::routes($path = '/captcha');
```

Captcha provides a helpful validation rules, if you need it, you can register validation within the `boot` method of your `AppServiceProvider`:

```php
Captcha::validations();
```

You can also use the Captcha's middleware in your routes. So you need register middleware to the `$routeMiddleware` array in `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
       // Other middlewares
       'captcha' => Vicens\Captcha\Middleware\CaptchaValidate::class
    ];
```

## Configuration

If you would like to use your own settings, you may publish Captcha's config using the `vendor:publish` Artisan command. The published config will be placed in  `config/captcha.php`:

```php
php artisan vendor:publish
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

## Basic Usage

#### Make captcha's image:

```php
$image = Captcha::make();
$image = Captcha::setConfig($config)->make();
$image = Captcha::width(100)->height(40)->make();
```
#### Image:
Return Symfony\Component\HttpFoundation\Response:
```php
$image->response();
```
Output to browser:
```php
$image->output();
```
Return image tag:
```php
Captcha::html($width, $height);
```
Return base64 encode:
```php
$image->getBase64();
```
Return base64 image Url:
```php
$image->getDataUrl();
```
Return image content:
```php
$image->getContent();
```
Save the image:
```php
$image->save($filename);
```

#### Check and test:
Only test
```php
Captcha::test($input);
```
Check input and remove the code from store
```php
Captcha::check($input);
```

#### Using the middleware:

Using in routes:

```php
Route::post('login','LoginController@login')->middleware('captcha');
```

Using in controllers:
```php
public function __constructor(){
   $this->middleware('captcha');
}
```

#### Using the validation:

In controllers:

```php
$this->validation([
   'code'=>'captcha'
]);
```
In Requests:
 ```php
public function rules()
{
    return [
        'code' => 'captcha'
    ];
}
```

#### Facade methods:

Return url:
```php
Captcha::url();
Captcha::src();
```
Return html:
```php
Captcha::image();
```
Register routes:
```php
Captcha::routes();
```
Register validations:
```php
Captcha::validations();
```

## License

Captcha is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).


[![Build Status](https://scrutinizer-ci.com/g/vicens/captcha/badges/build.png?b=master)](https://scrutinizer-ci.com/g/vicens/captcha/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/vicens/captcha/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/vicens/captcha/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/vicens/captcha/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/vicens/captcha/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/vicens/captcha/v/stable)](https://packagist.org/packages/vicens/captcha)
[![Total Downloads](https://poser.pugx.org/vicens/captcha/downloads)](https://packagist.org/packages/vicens/captcha)
[![License](https://poser.pugx.org/vicens/captcha/license)](https://packagist.org/packages/vicens/captcha)

## Introduction

An captcha component for laravel5 applications.

## Requirements
1. PHP >= 5.5
2. php-gd
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

## Usage

## License

Captcha is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

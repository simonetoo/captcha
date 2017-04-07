
## Introduction

An captcha component for laravel5 applications.

## Requirements
1. PHP >= 5.5
2. php-gd
3. symfony/http-foundation >= 2.0

## Installation

install captcha via composer

```php
composer require vicens/captcha
```

Next,register the service provider in the providers array of your `config/app.php` configuration file:

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

If you are going to use Captcha's default routes,you should call the `Captcha::routes` method within the `routes/web.php` or `boot` method of your  `AppServiceProvider`:

```php
Captcha::routes($path = '/captcha');
```

Captcha provides a helpful validation rules,if you need it,you can register validation within the `boot` method of your `AppServiceProvider`:

```php
Captcha::validations();
```

You can also use the Captcha's middleware in your routes,so you need register middleware to the `$routeMiddleware` array in `app/Http/Kernel.php`:

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
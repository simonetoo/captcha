
## 简介
php 验证码类,支持laravel5.*

## 运行环境
1. PHP >= 5.3
2. php-gd
3. symfony/http-foundation >= 2.0

## 特点
1. 支持验证码生成,验证码图片生成
2. 支持设置验证码图片大小,字符内容,复杂化参数等
3. 允许自定义存储驱动
4. 支持验证码图片保存为文件,直接响应,输出img标签等
5. 验证码使用hash加密后存储,更安全
6. 支持验证码命名空间,用于区分不同场景的验证码
7. [支持在laravel5.*中使用](#在laravel5中使用),并提供 配置文件和ServiceProvider

## 安装于使用

### 安装
```php
composer require vicens/captcha
```

### 卸载
```php
composer remove vicens/captcha
```

### 配置项
```php
$config = array(
    /**
     * 默认验证码长度
     * @var int
     */
    'length' => 4,
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
    'textColor' => null,
    /**
     * 文字字体文件
     * @var string
     */
    'textFont' => null,
    /**
     * 指定图片背景色
     * @var string
     */
    'backgroundColor' => null,
    /**
     * 开启失真模式
     * @var bool
     */
    'distortion' => true,
    /**
     * 最大前景线条数
     * @var int
     */
    'maxFrontLines' => null,
    /**
     * 最大背景线条数
     * @val int
     */
    'maxBehindLines' => null,
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

### API

#### 实例化
```php
$builder = new Captcha(array $config = array(), StoreInterface $store = null);
```

#### 设置和获取当前配置
```php
//设置
$builder->setConfig(array $config);
//获取
$builder->getConfig();
```

#### 设置命名空间
```php
$bulder->name('login');
```

#### 设置验证码图片宽高
```php
$builder->width(150)->height(40);
```

#### 设置验证码文字长度
```php
$builder->length(4);
```

#### 生成验证码图片
```php
$image = $builder->make();
```

#### 设置图片质量
```php
$image->quality(90);
```

#### 保存为文件
```php
$image->save('./captcha.jpg');
```

#### 直接输出
```php
$image->output();
```

#### 返回base64数据
```php
$image->getDataUrl();
```

#### 返回<img>标签
```php
$image->html();
```

#### 直接响应给浏览器
```php
$image->response();
```

#### 验证用户输入的验证码是否正确
```php
$builder->attempt($input);
```
### 在laravel5中使用

#### 配置服务提供者
```php
//服务提供器
'providers' => [
    ...
    Vicens\Captcha\Providers\CaptchaServiceProvider::class
]

//别名
'aliases' => [
    ...
    'Captcha' => Vicens\Captcha\Facades\Captcha::class
]
```
#### 生成配置文件
在你的laravel根目录下执行:
```php
php artisan vendor:publish
```
执行完以上命令后,会在config目录中生成captcha.php配置文件
#### 静态用法
```php
    Captcha::make()->response();
    Captcha::check($input);
    ...
```

## Todo List
- [ ] 支持异步验证

## Change Log

## Encourage
欢迎各位PHPer光顾和支持,如有需要可以start,如有问题或Bug可以issue或提交pull request!

## LICENSE 
 MIT

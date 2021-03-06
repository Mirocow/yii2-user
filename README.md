Yii2 User
=========

Yii2 User - User authentication module

![alt text](http://images.mirocow.com/2014-02-19_03.24.42_rca639xskb.png)

![alt text](http://images.mirocow.com/2014-02-19_03.26.29_kufjlm2776.png)

* Registation
* Restore password
* Authorization
* Email Authorization
* Email Uniq Link Engine
* Session table
* ACL - List of Permissions
* Admin intarface

ACL
===

    $isCanUserView = User::find(44)->can('view');
    $isCurrentUserView = Yii::$app->user->can('view');

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

### Add repositor


```json
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/mirocow/yii2-user.git"
        }
    ]
```

and then

```
php composer.phar require --prefer-dist "mirocow/yii2-user" "*"
```

or add

```json
"mirocow/yii2-user" : "*"
```

to the require section of your application's `composer.json` file.

Use
========

For simple use

```php
    // ...
    'modules' => [
    
        // Пользователь, Роль, Разрешения
        'user' => [
        
          'class' => 'yii\user\Module',

        ],
        // ...
    ],
```   

If you want overide base setting pliase add in config file.

```php
    // ...
    'modules' => [
    
        // Пользователь, Роль, Разрешения
        'user' => [
        
          'class' => 'yii\user\Module',
          
          // Ищем Views по локальному пути
          'views' => '@app/modules/core/views',
          
          // Обязательно должы быть Controllers
          'controllers' => '@app/modules/core/controllers',
          
          // Обязательно должны быть реализованы Actions
          'controllerNamespace' => 'app\modules\core\controllers',
          
          // Путь до шаплонов отправляемых писем
          'emailViewPath' => '@app/modules/site/views/email',
          
          // Параметры
          'requireUsername' => true,
          
          'usedCaptcha' => array('register', 'login'),
          'pathCaptcha' => 'user/default/captcha',
        ],
        // ...
    ],
    // ...
```

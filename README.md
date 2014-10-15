Yii2 User
=========

Yii2 User - User authentication module

![alt text](http://images.mirocow.com/2014-02-19_03.24.42_rca639xskb.png)

![alt text](http://images.mirocow.com/2014-02-19_03.26.29_kufjlm2776.png)

* Registation -
* Authorization - 
* Email Authorization -
* Email Uniq Link Engine -
* Session table
* ACL - List of Permissions ()

ACL
===

    $isCanUserView = User::find(44)->can('view');
    $isCurrentUserView = Yii::$app->user->can('view');

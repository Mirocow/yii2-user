Yii2 User
=========

Yii2 User - User authentication module

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
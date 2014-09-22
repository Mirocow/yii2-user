Yii2 User
=========

![alt text](http://images.mirocow.com/2014-02-19_03.24.42_rca639xskb.png)

![alt text](http://images.mirocow.com/2014-02-19_03.26.29_kufjlm2776.png)

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
    
Route
=====

Actions in this module
Note: some actions may be unavailable depending on if you are logged in/out, or as an admin/regular user


    Link	Description
    User	 URL: /user
    Login	 URL: /user/login
    Logout	 URL: /user/logout
    Register	 URL: /user/register&type=xxxxx
        Регистрация пользователя с указанием типа
    Profile	 URL: /user/profile?type=xxxxxxxxx
    Profile by type
        Forgot password	 URL: /user/forgot
    Resend	 URL: /user/resend
        Resend email change confirmation (NOT FOR REGISTRATION / EMAIL ACTIVATION)
    Cancel	 URL: /user/cancel
        Cancel email change confirmation. 
        This and resend appear on the 'Account' page
    Confirm	 URL: /user/confirm?hash=xxxxxxxxx&sid=xxxxxxxxxx
    Confirm email address. Automatically generated with key
    Reset	 URL: /user/reset?hash=xxxxxxxxx&sid=xxxxxxxxxx
    Reset password. Automatically generated with key from 'Forgot password' page
    captcha	 URL: yii\captcha\CaptchaAction
        Path for captcha generate
    admin/user	 URL: /user/admin/user
        User Control Manager
    admin/role	 URL: /user/admin/role
        Role Based Access Control Manager
    admin/permission	 URL: /user/admin/permission
    
Permissions and access control interface


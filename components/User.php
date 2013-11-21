<?php

namespace amnah\yii2\user\components;

use Yii;

/**
 * User component
 *
 * @author amnah <amnah.dev@gmail.com>
 */
class User extends \yii\web\User {

    /**
     * @inheritdoc
     */
    public $identityClass = "amnah\yii2\user\models\User";

    /**
     * @inheritdoc
     */
    public $enableAutoLogin = true;

    /**
     * @inheritdoc
     */
    public $loginUrl = ["/user/login"];
}
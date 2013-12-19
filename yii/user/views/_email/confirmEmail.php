<?php

use yii\user\models\User;
use yii\user\models\Profile;
use yii\user\models\Userkey;

/**
 * @var string $subject
 * @var User $user
 * @var Profile $profile
 * @var Userkey $userkey
 */
?>

<h3><?= $subject ?></h3>

<p>Please confirm your email address by clicking the link below:</p>

<p><?= Yii::$app->urlManager->createAbsoluteUrl("user/confirm", ["key" => $userkey->key]); ?></p>
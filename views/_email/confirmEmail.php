<?php

use mirocow\user\models\User;
use mirocow\user\models\Profile;
use mirocow\user\models\Userkey;

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
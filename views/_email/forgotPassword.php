<?php

use mirocow\user\models\User;
use mirocow\user\models\Userkey;

/**
 * @var string $subject
 * @var User $user
 * @var Userkey $userkey
 */
?>

<h3><?= $subject ?></h3>

<p>Please use this link to reset your password:</p>

<p><?= Yii::$app->urlManager->createAbsoluteUrl("user/reset", ["key" => $userkey->key]); ?></p>
<?php

use yii\user\models\User;
use yii\user\models\Profile;
use yii\user\models\Session;

/**
 * @var string $subject
 * @var User $user
 * @var Profile $profile
 * @var Session $session
 */
?>

<h3><?= $subject ?></h3>

<p>Please confirm your email address by clicking the link below:</p>

<p><?= Yii::$app->urlManager->createAbsoluteUrl("user/confirm", ["hash" => $user->hash, "sid" => $session->sid]); ?></p>
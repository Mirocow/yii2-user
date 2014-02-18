<?php

namespace yii\user\modules\admin\controllers;

use Yii;
use yii\web\ForbiddenHttpException;

use yii\user\modules\admin\components\AdminController;

class DefaultController extends AdminController
{
	public function actionIndex()
	{
		throw new ForbiddenHttpException(Yii::t('backend', 'Your access to the requested page is forbidden.'));
	}
}

<?php

namespace yii\user\modules\admin\controllers;

use Yii;
use yii\web\ForbiddenHttpException;

use yii\user\modules\admin\components\AdminController;

class DefaultController extends AdminController
{
	/*public function actionIndex()
	{
		throw new ForbiddenHttpException(Yii::t('backend', 'Your access to the requested page is forbidden.'));
	}*/

		/**
		 * Display index
		 */
		public function actionIndex() {

				// display debug page if YII_DEBUG is set
				if (defined('YII_DEBUG')) {
						$actions = Yii::$app->getModule("user/admin")->getActions();
						return $this->render('index', ["actions" => $actions]);
				}
				// redirect to login page if user is guest
				elseif (Yii::$app->user->isGuest) {
						return $this->redirect([""]);
				}
				// redirect to account page if user is logged in
				else {
						return $this->redirect([""]);
				}
		}

}

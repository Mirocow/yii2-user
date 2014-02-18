<?php

namespace yii\user\modules\admin\components;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\Controller;

class AdminController extends Controller
{

		/**
		 * @inheritdoc
		 */
		public function beforeAction($action)
		{
				if(Yii::$app->user->can('admin') && Yii::$app->user->can($action->id)){
						return parent::beforeAction($action);
				} else {
						throw new ForbiddenHttpException(Yii::t('backend', 'Your access to the requested page ({action}) is forbidden.', ['action' => $action->id]));
				}
		}

}

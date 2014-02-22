<?php

namespace yii\user\modules\admin;


class Module extends \yii\base\Module
{
	public $controllerNamespace = 'yii\user\modules\admin\controllers';

	public function init()
	{
		parent::init();

		// custom initialization code goes here
	}

		/**
		 * Get a list of actions for this module. Used for debugging/initial installations
		 */
		public function getActions() {

				return [
						'user' => [
										'url' => ["/user/admin/user"],
										"description" => "User Control Manager",
						],

						'role' => [
										'url' => ["/user/admin/role"],
										"description" => "Role Based Access Control Manager",
						],

						'permission' => [
										'url' => ["/user/admin/permission"],
										"description" => "Permissions and access control interface",
						],
				];
		}

}

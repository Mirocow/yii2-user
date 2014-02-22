<?php

namespace yii\user;

use Yii;
use yii\base\InvalidConfigException;
use yii\user\models\Permission;

/**
 * User module
 *
 * @author amnah <amnah.dev@gmail.com>
 */
class Module extends \yii\base\Module {

		/**
		 * @inheritdoc
		 */
		public $controllerNamespace = "yii\user\controllers";

		/**
		 * @var string Alias for module
		 */
		public $alias = "@user";

		/**
		 * @var bool If true, users will have to confirm their email address after registering
		 *           This is the same as email activation
		 */
		public $emailConfirmation = true;

		/**
		 * @var bool If true, users will have to confirm their email address after updating email (account page)
		 */
		public $emailChangeConfirmation = true;

		/**
		 * @var bool If true, users are required to enter an email
		 */
		public $requireEmail = true;

		/**
		 * @var bool If true, users are required to enter a username
		 */
		public $requireUsername = false;

		/**
		 * @var bool If true, users are required to enter an password or it`s generate automaticly
		 */
		public $requirePassword = false;

		/**
		* @var interger Length for autogererate
		*/
		public $passwordLength = 8;

		/*
		 * @var bool If true, users can enter an email. This is automatically set to true if $requireEmail = true
		 */
		public $useEmail = true;

		/**
		 * @var bool If true, users can enter a username. This is automatically set to true if $requireUsername = true
		 */
		public $useUsername = true;

		/**
		 * @var bool If true, users can log in by entering their email
		 */
		public $loginEmail = true;

		/**
		 * @var bool If true, users can log in by entering their username
		 */
		public $loginUsername = true;

		/**
		 * @var int Login duration
		 */
		public $loginDuration = 2592000;

		/**
		* @var bool Action for show captcha
		*/
		public $usedCaptcha = array('register');

		/**
		* @var string Path for captcha generate
		*/
		public $pathCaptcha = 'site/captcha';

		/**
		 * @var string Email view path
		 */
		public $emailViewPath = "@user/views/_email";

		public $views = '@user/views';

		public $controllers = '@user/controllers';

		/**
		* @var bool If true, the user will be status active automaticly
		*/
		public $userReristerActive = false;

		/**
		 * @inheritdoc
		 */
		public function init() {
				parent::init();

				// set alias
				$this->setAliases([
						$this->alias => __DIR__,
				]);

				// set views path
				$this->setViewPath($this->views);

				// set controllers path
				$this->setControllerPath($this->controllers);

				// set use fields based on required fields
				if ($this->requireEmail) {
						$this->useEmail = true;
				}
				if ($this->requireUsername) {
						$this->useUsername = true;
				}

				// get class name for error messages
				$className = get_called_class();

				// check required fields
				if (!$this->requireEmail and !$this->requireUsername) {
						throw new InvalidConfigException("{$className}: \$requireEmail and/or \$requireUsername must be true");
				}
				// check login fields
				if (!$this->loginEmail and !$this->loginUsername) {
						throw new InvalidConfigException("{$className}: \$loginEmail and/or \$loginUsername must be true");
				}
				// check email fields with emailConfirmation/emailChangeConfirmation is true
				if (!$this->useEmail and $this->emailConfirmation) {
						throw new InvalidConfigException("{$className}: \$useEmail must be true if \$emailConfirmation is true");
				}
				if (!$this->useEmail and $this->emailChangeConfirmation) {
						throw new InvalidConfigException("{$className}: \$useEmail must be true if \$emailChangeConfirmation is true");
				}

				$this->setModule('admin', 'yii\user\modules\admin\Module');

				\Yii::$app->getI18n()->translations['frontend'] = [
						'class' => 'yii\i18n\PhpMessageSource',
						'sourceLanguage' => 'en',
						'basePath' => '@yii/user/messages',
				];

				\Yii::$app->getI18n()->translations['backend'] = [
						'class' => 'yii\i18n\PhpMessageSource',
						'sourceLanguage' => 'en',
						'basePath' => '@yii/user/messages',
				];

		}

		/**
		 * Get a list of actions for this module. Used for debugging/initial installations
		 */
		public function getActions() {

				return [
						"User" => [
								'url' => [""],
								"description" => "",
						],

						"Login" => [
								'url' => ["login"],
								'description' => '',
						],

						"Logout" => ["logout"],

						"Account" => ["account"],

						"Register" => [
								"url" => ["register", "type" => "xxxxxxxx"],
								"description" => "Регистрация пользователя с указанием типа",
						],

						"Profile" => [
								"url" => ["profile", "type" => "xxxxxxxxx"],
								"description" => "Profile by type",
						],

						"Forgot password" => ["forgot"],

						"Resend" => [
								"url" => ["resend"],
								"description" => "Resend email change confirmation (NOT FOR REGISTRATION / EMAIL ACTIVATION)",
						],
						"Cancel" => [
								"url" => ["cancel"],
								"description" => "Cancel email change confirmation. <br/>This and resend appear on the 'Account' page",
						],

						"Confirm" => [
								"url" => ["confirm", "hash" => "xxxxxxxxx", "sid" => "xxxxxxxxxx"],
								"description" => "Confirm email address. Automatically generated with key",
						],
						"Reset" => [
								"url" => ["reset", "hash" => "xxxxxxxxx", "sid" => "xxxxxxxxxx"],
								"description" => "Reset password. Automatically generated with key from 'Forgot password' page",
						],

						/*'captcha' => [
										'url' => ['yii\captcha\CaptchaAction'],
										"description" => "Path for captcha generate",
						],*/

						'user/admin' => [
										'url' => ["/{$this->id}/admin"],
										"description" => "Administrate interface",
						],
				];
		}
}

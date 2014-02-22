<?php

namespace yii\user\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\user\models\UserRole;
use yii\user\models\Permission;
use yii\user\models\PermissionRole;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Role model
 *
 * @property int $id
 * @property string $name
 * @property string $create_time
 * @property string $update_time
 * @property int $grant
 *
 * @property User[] $users
 */
class Role extends ActiveRecord {

		/**
		 * @var int Admin user role
		 */
		const ROLE_ADMIN = 1;

		/**
		 * @var int Default user role
		 */
		const ROLE_USER = 2;

		/**
		 * @var int Guest user role
		 */
		//const ROLE_GUEST = 3;

		/**
		 * @inheritdoc
		 */
		public static function tableName() {
				return '{{%role}}';
		}

		/**
		 * @inheritdoc
		 */
		public function rules() {
				return [
						[['name'], 'required'],
						[['machine_name'], 'required'],
						[['create_time', 'update_time'], 'safe'],
						[['grant'], 'boolean'],
						[['name'], 'string', 'max' => 255]
				];
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels() {
				return [
						'id' => 'ID',
						'name' => 'Name',
						'machine_name' => 'Machine name',
						'create_time' => 'Create Time',
						'update_time' => 'Update Time',
						'grant' => 'All access',
				];
		}

		/**
		 * @return \yii\db\ActiveRelation
		 */
		public function getPermissionRoles()
		{
				return $this->hasMany(PermissionRole::className(), ['role_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveRelation
		 */
		public function getUserRoles()
		{
				return $this->hasMany(UserRole::className(), ['role_id' => 'id']);
		}

		/**
		 * @inheritdoc
		 */
		public function behaviors() {
				return [
						'timestamp' => [
								'class' => TimestampBehavior::className(),
								'attributes' => [
										ActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
										ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
								],
								'value' => new Expression('NOW()'),
						],
				];
		}

		/**
		 * Get list of roles for creating dropdowns
		 *
		 * @return array
		 */
		public static function dropdown() {

				// get data if needed
				static $dropdown;
				if ($dropdown === null) {

						// get all records from database and generate
						$models = static::find()->all();
						foreach ($models as $model) {
								$dropdown[$model->id] = $model->name;
						}
				}

				return $dropdown;
		}

		/**
		* put your comment there...
		*
		* @param boolean $selected -
		* @return []
		*/
		public function getPermissions($selected = true){
				$return = [];

				$return = ArrayHelper::map($return, 'id', 'name');

				return $return;
		}

}

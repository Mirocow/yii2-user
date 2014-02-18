<?php

namespace yii\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Profile model
 *
 * @property int $id
 * @property int $user_id
 * @property string $create_time
 * @property string $update_time
 * @property string $username
 * @property string $phone
 *
 * @property User $user
 */
class Profile extends ActiveRecord {

		/**
		 * @inheritdoc
		 */
		public static function tableName() {
				return '{{%profile}}';
		}

		/**
		 * @inheritdoc
		 */
		public function rules() {
				$rules = [
						[['user_id'], 'required'],
						[['user_id'], 'integer'],
						[['username', 'phone'], 'string', 'max' => 255],
						[['username'], 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u', 'message' => "{attribute} can contain only letters, numbers, and '_'."],
						[['create_time', 'update_time'], 'safe'],
				];

				// add required rules for username depending on module properties
				$requireFields = ["requireUsername"];

				foreach ($requireFields as $requireField) {
						if (Yii::$app->getModule("user")->$requireField) {
								$attribute = strtolower(substr($requireField, 7));
								$rules[] = [$attribute, "required"];
						}
				}

				return $rules;
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels() {
				return [
						'id' => 'ID',
						'user_id' => 'User ID',
						'create_time' => 'Create Time',
						'update_time' => 'Update Time',
						'username' => 'User name',
						'phone' => 'Phone number',
				];
		}

		/**
		 * @return \yii\db\ActiveRelation
		 */
		public function getUser() {
				return $this->hasOne(User::className(), ['id' => 'user_id']);
		}

		/**
		 * @inheritdoc
		 */
		public function behaviors() {
				return [
						'timestamp' => [
								'class' => 'yii\behaviors\AutoTimestamp',
								'timestamp' => function() { return date("Y-m-d H:i:s"); },
								'attributes' => [
										ActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
										ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
								],
						],
				];
		}

		/**
		 * Register a new profile for user
		 *
		 * @param int $userId
		 * @return static
		 */
		public function register($userId) {

				$this->user_id = $userId;
				$this->save(false);
				return $this;

		}

		/**
		 * Set user id for profile
		 *
		 * @param int $userId
		 * @return static
		 */
		public function setUser($userId) {

				$this->user_id = $userId;
				return $this;
		}
}

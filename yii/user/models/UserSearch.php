<?php

namespace yii\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\user\models\User;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
	public $id;
	public $username;
	public $status;
	public $email;
	public $new_email;
	public $password;
	public $hash;
	public $ban_time;
	public $ban_reason;
	public $create_time;
	public $update_time;
	public $data;

	public function rules()
	{
		return [
			[['id'], 'integer'],
			[['username', 'status', 'email', 'new_email', 'password', 'hash', 'ban_time', 'ban_reason', 'create_time', 'update_time', 'data'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'username' => 'Контактное лицо',
			'status' => 'Статус',
			'email' => 'Контактный email',
			'new_email' => 'New Email',
			'password' => 'Пароль',
			'hash' => 'Hash (user key)',
			'ban_time' => 'Ban Time',
			'ban_reason' => 'Ban Reason',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'data' => 'Data',
		];
	}

	public function search($params)
	{
		$query = User::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$this->addCondition($query, 'id');
		$this->addCondition($query, 'username', true);
		$this->addCondition($query, 'status', true);
		$this->addCondition($query, 'email', true);
		$this->addCondition($query, 'new_email', true);
		$this->addCondition($query, 'password', true);
		$this->addCondition($query, 'hash', true);
		$this->addCondition($query, 'ban_time');
		$this->addCondition($query, 'ban_reason', true);
		$this->addCondition($query, 'create_time');
		$this->addCondition($query, 'update_time');
		$this->addCondition($query, 'data', true);
		return $dataProvider;
	}

	protected function addCondition($query, $attribute, $partialMatch = false)
	{
		$value = $this->$attribute;
		if (trim($value) === '') {
			return;
		}
		if ($partialMatch) {
			$query->andWhere(['like', $attribute, $value]);
		} else {
			$query->andWhere([$attribute => $value]);
		}
	}
}

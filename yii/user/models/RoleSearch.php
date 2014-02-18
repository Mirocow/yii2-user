<?php

namespace yii\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\user\models\Role;

/**
 * RoleSearch represents the model behind the search form about Role.
 */
class RoleSearch extends Model
{
	public $id;
	public $machine_name;
	public $name;
	public $description;
	public $grant;
	public $create_time;
	public $update_time;

	public function rules()
	{
		return [
			[['id'], 'integer'],
			[['machine_name', 'name', 'description', 'create_time', 'update_time'], 'safe'],
			[['grant'], 'boolean'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'machine_name' => 'Машинное имя',
			'name' => 'Наименование',
			'description' => 'Комментарий',
			'grant' => 'Grant',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
		];
	}

	public function search($params)
	{
		$query = Role::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$this->addCondition($query, 'id');
		$this->addCondition($query, 'machine_name', true);
		$this->addCondition($query, 'name', true);
		$this->addCondition($query, 'description', true);
		$this->addCondition($query, 'grant');
		$this->addCondition($query, 'create_time');
		$this->addCondition($query, 'update_time');
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

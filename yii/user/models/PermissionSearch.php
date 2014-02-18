<?php

namespace yii\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\user\models\Permission;

/**
 * PermissionSearch represents the model behind the search form about Permission.
 */
class PermissionSearch extends Model
{
	public $id;
	public $machine_name;
	public $name;
	public $type;
	public $description;

	public function rules()
	{
		return [
			[['id'], 'integer'],
			[['machine_name', 'name', 'type', 'description'], 'safe'],
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
			'type' => 'Тип разрешения',
			'description' => 'Описание',
		];
	}

	public function search($params)
	{
		$query = Permission::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$this->addCondition($query, 'id');
		$this->addCondition($query, 'machine_name', true);
		$this->addCondition($query, 'name', true);
		$this->addCondition($query, 'type', true);
		$this->addCondition($query, 'description', true);
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

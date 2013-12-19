<?php

namespace mirocow\user\models;

use Yii;
use yii\db\ActiveRecord;
use ReflectionClass;

/**
 * Role model
 *
 * @property int $id
 * @property string $name
 * @property string $create_time
 * @property string $update_time
 * @property int $can_admin
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
    const ROLE_GUEST = 3;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return Yii::$app->db->tablePrefix . 'role';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['can_admin'], 'boolean'],
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
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'can_admin' => 'Can Admin',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['role_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\AutoTimestamp',
                'timestamp' => function() { return date("Y-m-d H:i:s"); },
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
}

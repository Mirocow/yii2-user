<?php

namespace yii\user\models;

/**
 * This is the model class for table "tbl_permission".
 *
 * @property integer $id
 * @property string $machine_name
 * @property string $name
 * @property string $description
 *
 * @property PermissionRole[] $permissionRoles
 */
class Permission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_permission';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['machine_name', 'name', 'description'], 'required'],
            [['machine_name', 'name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'machine_name' => 'Machine Name',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getPermissionRoles()
    {
        return $this->hasMany(PermissionRole::className(), ['permission_id' => 'id']);
    }
}
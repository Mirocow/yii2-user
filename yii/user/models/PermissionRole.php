<?php

namespace yii\user\models;

/**
 * This is the model class for table "tbl_permission_role".
 *
 * @property integer $id
 * @property integer $permission_id
 * @property integer $role_id
 *
 * @property Permission $permission
 * @property UserRole $role
 */
class PermissionRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_permission_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['permission_id', 'role_id'], 'required'],
            [['permission_id', 'role_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'permission_id' => 'Permission ID',
            'role_id' => 'Role ID',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getPermission()
    {
        return $this->hasOne(Permission::className(), ['id' => 'permission_id']);
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getRole()
    {
        return $this->hasOne(UserRole::className(), ['role_id' => 'role_id']);
    }
}
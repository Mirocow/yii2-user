<?php

namespace yii\user\models;

use yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\swiftmailer\Mailer;
use yii\helpers\Inflector;
//use yii\helpers\Security;
use yii\user\models\Role;
use ReflectionClass;

/**
 * This is the model class for table "tbl_user_role".
 *
 * @property integer $user_id
 * @property integer $role_id
 *
 * @property PermissionRole[] $permissionRoles
 * @property User $user
 * @property Role $role
 */
class UserRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id'], 'required'],
            [['user_id', 'role_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'role_id' => 'Role ID',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getPermissionRoles()
    {
        return $this->hasMany(PermissionRole::className(), ['role_id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }
    
    public function register($userId, $roleId) {

        $this->setAttributes([ "user_id" => $userId, "role_id" => $roleId ]);
        $this->save(false);
        return $this;
    }
    
    public function can($permission_name, $permission_type = Permission::PERMISSION_DEFAULT){
        // isGrant
        if(Role::findOne( ['id' => $this->role_id, 'grant' => 1] )){
            return true;
        }
        return PermissionRole::find()
            ->leftJoin(Permission::tableName() . ' p', 'p.id = permission_id AND p.type = :permission_type')
            ->where( PermissionRole::tableName() . '.role_id = :role AND p.machine_name = :permission', 
            [
                'role' => $this->role_id , 
                'permission' => $permission_name,
                'permission_type' => $permission_type,
            ]
            )->all();
    }
}
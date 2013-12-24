<?php

namespace yii\user\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\swiftmailer\Mailer;
use yii\helpers\Inflector;
use yii\helpers\Security;
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
}
<?php

namespace yii\user\models;

use yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\user\models\UserRole;
use yii\user\models\Permission;
use yii\user\models\PermissionRole;
//use ReflectionClass;

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
              'class' => yii\behaviors\TimestampBehavior::className(),
              'createdAtAttribute' => 'create_time',
              'updatedAtAttribute' => 'update_time',
              'value' => new yii\db\Expression('NOW()'),
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
    public function getPermissionsItems($selected = false){
        $return = [];

        $query = Permission::find()
          ->joinWith('permissionRoles');

        if($selected){
            $query->where(['tbl_permission_role.role_id' => $this->id]);
        }

        $permissions = $query->all();

        if($selected){
            return ArrayHelper::map($permissions, 'id', 'id');
        } else {
            return ArrayHelper::map($permissions, 'id', 'name');
        }

    }

    public function afterSave($insert, $changedAttributes){

        foreach($_POST['permissions'] as $permission_id){
            PermissionRole::bind($permission_id, $this->id);
        }

    }


}

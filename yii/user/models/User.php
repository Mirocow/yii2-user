<?php

namespace yii\user\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\swiftmailer\Mailer;
use yii\helpers\Inflector;
use yii\helpers\Security;
use yii\user\models\UserRole;
use yii\user\models\Role;
use ReflectionClass;
/**
 * User model
 *
 * @property int $id
 * @property int $role_id
 * @property string $email
 * @property string $new_email
 * @property string $username
 * @property string $password
 * @property int $status
 * @property string $auth_key
 * @property string $create_time
 * @property string $update_time
 * @property string $ban_time
 * @property string $ban_reason
 * @property Profile $profile
 * @property Role $role
 * @property Session[] $sessions
 */
class User extends ActiveRecord implements IdentityInterface {

    /**
     * @var int Inactive status
     */
    const STATUS_INACTIVE = 'INACTIVE';

    /**
     * @var int Active status
     */
    const STATUS_ACTIVE = 'ACTIVE';

    /**
     * @var int Unconfirmed email status
     */
    const STATUS_UNCONFIRMED_EMAIL = 2;

    /**
     * @var string New password - for registration and changing password
     */
    public $newPassword;

    /**
     * @var string Current password - for account page updates
     */
    public $currentPassword;
    
    public $verifyCode;
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {

        // set initial rules
        $rules = [
        
            // Status
            [['status'], 'string'],
            
            // general email and username rules
            [['email', 'username'], 'string', 'max' => 255],
            [['email', 'username'], 'unique'],
            [['email', 'username'], 'filter', 'filter' => 'trim'],
            [['email'], 'email'],
            
            //[['username'], 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u', 'message' => "{attribute} can contain only letters, numbers, and '_'."],
            
            // captcha
            /*['verifyCode', 'captcha', 'skipOnEmpty' => !in_array('register', 
                \Yii::$app->getModule('user')->usedCaptcha), 
                'captchaAction' => \Yii::$app->getModule('user')->pathCaptcha],*/

            // password rules
            [['newPassword'], 'string', 'min' => 3],
            [['newPassword'], 'filter', 'filter' => 'trim'],
            [['newPassword'], 'required', 'on' => ['register']],

            // account page
            [['currentPassword'], 'required', 'on' => ['account']],
            [['currentPassword'], 'validateCurrentPassword', 'on' => ['account']],

            // admin crud rules
			//[['role_id', 'status'], 'required', 'on' => ['admin']],
			//[['role_id', 'status'], 'integer', 'on' => ['admin']],
            
			[['ban_time'], 'integer', 'on' => ['admin']],
			[['ban_reason'], 'string', 'max' => 255, 'on' => 'admin'],
        ];

        // add required rules for email/username depending on module properties
        $requireFields = ["requireEmail", "requireUsername"];
        
        foreach ($requireFields as $requireField) {
            if (Yii::$app->getModule("user")->$requireField) {
                $attribute = strtolower(substr($requireField, 7));
                $rules[] = [$attribute, "required"];
            }
        }

        return $rules;
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    /*public function getId0()
    {
        return $this->hasOne(UserRole::className(), ['role_id' => 'id']);
    }*/
    
    /**
     * @return \yii\db\ActiveRelation
     */
    /*public function getRole()
    {
        return $this->hasMany(UserRole::className(), ['user_id' => 'id'])
            ->viaTable('tbl_user_role', ['user_id' => 'id']);
    }*/
    
    public function getRoles()
    {
        return $this->hasMany(UserRole::className(), ['user_id' => 'id']);
    }    
    
    /**
     * Validate password
     */
    public function validateCurrentPassword() {

        // check password
        if (!$this->verifyPassword($this->currentPassword)) {
            $this->addError("currentPassword", "Current password incorrect");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            //'role_id' => 'Role ID',
            'email' => 'Email',
            'new_email' => 'New Email',
            'username' => 'Username',
            'password' => 'Password',
            'hash' => 'Hash (user key)',
            'status' => 'Status',
            //'auth_key' => 'Auth Key',
            'ban_time' => 'Ban Time',
            'ban_reason' => 'Ban Reason',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'verify_code' => 'Verification Code',

            // attributes in model
            'newPassword' => ($this->isNewRecord) ? 'Password' : 'New Password',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getSessions() {
        return $this->hasMany(Session::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    /*
    public function getProfiles() {
        return $this->hasMany(Profile::className(), ['user_id' => 'id']);
    }
    */

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getProfile() {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    /*public function getRole() {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }*/

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
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::find($id);
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->id === $authKey;
    }

    /**
     * Get a clean display name for the user
     *
     * @var string $default
     * @return string|int
     */
    public function getDisplayName($default = "") {

        // define possible names
        $possibleNames = [
            "username",
            "email",
            "id",
        ];

        // go through each and return if valid
        foreach ($possibleNames as $possibleName) {
            if (!empty($this->$possibleName)) {
                return $this->$possibleName;
            }
        }

        return $default;
    }

    /**
     * Send email confirmation to user
     *
     * @param Session $session
     * @return int
     */
    public function sendEmailConfirmation($session) {

        // modify view path to module views
        /** @var Mailer $mailer */
        $mailer = Yii::$app->mail;
        $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

        // send email
        $user = $this;
        //$profile = $user->profile;
        $subject = Yii::$app->id . " - Email confirmation";
        $from = isset(Yii::$app->params['adminEmail'])? Yii::$app->params['adminEmail']: 'admin@localhost';
        return $mailer->compose('confirmEmail', compact("subject", "user", "profile", "session"))
            ->setFrom($from)
            ->setTo($user->email)
            ->setSubject($subject)
            ->send();
    }
    
    public function beforeValidate(){
        
        if($this->getIsNewRecord()){
            
            // Automaticly generate a new password
            if (!($this->newPassword && Yii::$app->getModule("user")->requirePassword)) {
                $this->newPassword = self::randomPassword(Yii::$app->getModule("user")->passwordLength);
            }
            
            // Status is inactive for default
            if(Yii::$app->getModule("user")->userReristerActive){
                $this->status = self::STATUS_ACTIVE;
            } else {                
                $this->status = self::STATUS_INACTIVE;
            }
            
        }
        
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {

        // hash new password if set
        if ($this->newPassword) {            
            $this->encryptNewPassword();
        }

        // ensure fields are null so they won't get set as empty string
        $nullAttributes = ["email", "username", "ban_time", "ban_reason"];
        foreach ($nullAttributes as $nullAttribute) {
            $this->$nullAttribute = $this->$nullAttribute ? $this->$nullAttribute : null;
        }

        // convert ban_time checkbox to date
        if ($this->ban_time) {
            $this->ban_time = date("Y-m-d H:i:s");
        }
        
        // set User inactive
        if($this->getIsNewRecord()){
            $this->status = User::STATUS_INACTIVE;
            $this->hash = md5($this->password . time());
        }       

        return parent::beforeSave($insert);
    }

    /**
     * Encrypt newPassword into password
     *
     * @return static
     */
    public function encryptNewPassword() {
        $this->password = Security::generatePasswordHash($this->newPassword);
        return $this;
    }

    /**
     * Validate password
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password) {
        return Security::validatePassword($password, $this->password);
    }

    /**
     * Register a new user
     *
     * @param int $roleId
     * @return static
     */
    public function register() {

        // set default attributes for registration
        $attributes = [ "status" => static::STATUS_ACTIVE ];

        // determine if we need to change status based on module properties
        $emailConfirmation = Yii::$app->getModule("user")->emailConfirmation;

        // set inactive if email is required
        if (Yii::$app->getModule("user")->requireEmail and $emailConfirmation) {
            $attributes["status"] = User::STATUS_INACTIVE;
        }
        // set unconfirmed if email is set but NOT required
        elseif (Yii::$app->getModule("user")->useEmail and $this->email and $emailConfirmation) {
            $attributes["status"] = User::STATUS_UNCONFIRMED_EMAIL;
        }

        // set attributes
        $this->setAttributes($attributes, false);

        // save and return
        // note: we assume that we have already validated (both $user and $profile)
        $this->save(false);
        return $this;
    }

    /**
     * Check and prepare for email change
     *
     * @return bool
     */
    public function checkAndPrepareEmailChange() {

        // check for change in email
        if ($this->email != $this->getOldAttribute("email")) {

            // change status
            $this->status = static::STATUS_UNCONFIRMED_EMAIL;

            // set new_email attribute and restore old one
            $this->new_email = $this->email;
            $this->email = $this->getOldAttribute("email");

            return true;
        }

        return false;
    }

    /**
     * Confirm user email
     *
     * @return static
     */
    public function confirm() {

        // update status
        $this->status = static::STATUS_ACTIVE;

        // update new_email if set
        if ($this->new_email) {
            $this->email = $this->new_email;
            $this->new_email = null;
        }

        // save and return
        $this->save();
        return $this;
    }

    /**
     * Get list of statuses for creating dropdowns
     *
     * @return array
     */
    public static function statusDropdown() {

        // get data if needed
        static $dropdown;
        if ($dropdown === null) {

            // create a reflection class to get constants
            $refl = new ReflectionClass(get_called_class());
            $constants = $refl->getConstants();

            // check for status constants (e.g., STATUS_ACTIVE)
            foreach ($constants as $constantName => $constantValue) {

                // add prettified name to dropdown
                if (strpos($constantName, "STATUS_") === 0) {
                    $prettyName = str_replace("STATUS_", "", $constantName);
                    $prettyName = Inflector::humanize(strtolower($prettyName));
                    $dropdown[$constantValue] = $prettyName;
                }
            }
        }

        return $dropdown;
    }
    
    public static function randomPassword($password_length = 8, $strong = false){        
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        if($strong){
            $alphabet .= '~!@#$%^&*()_+';
        }
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $password_length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    
    public function can($permission_name, $permission_type = Permission::PERMISSION_DEFAULT){
        $permissions = [];
        foreach($this->roles as $role){
            if($role->can($permission_name, $permission_type)){
                return true;
            }
        }
        
        return false;
    }
    
    public function hasRole($role_name){
        return UserRole::find()
            //->with('role')
            ->joinWith(['role'])
            //->getRole()
            ->where([
                '{{%role}}.machine_name' => $role_name,
            ])
            ->one();
    }
    
}

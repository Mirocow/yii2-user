<?php

namespace yii\user\components;

use yii;

/**
 * User component
 */
class User extends \yii\web\User {

    /**
     * @inheritdoc
     */
    public $identityClass = "yii\user\models\User";

    /**
     * @inheritdoc
     */
    public $enableAutoLogin = true;

    /**
     * @inheritdoc
     */
    public $loginUrl = ["/user/login"];

    /**
     * @inheritdoc
     */
    public $emailViewPath = '@user/views/mail';

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function getIsLoggedIn() {
        return !$this->getIsGuest();
    }

    /**
     * Get user's email
     *
     * @return string
     */
    public function getEmail() {
        return $this->getIdentity()->email;
    }

    /**
     * Get user's username
     *
     * @return mixed
     */
    public function getUsername() {
        return $this->getIdentity()->username;
    }

    /**
     * Get user's display name
     *
     * @param string $default
     * @return string
     */
    public function getDisplayName($default = "") {
        return $this->getIdentity()->getDisplayName($default);
    }

    /**
     * Check if user can do $permission
     *
     * @param string $permission
     * @param bool $user
     * @return bool
     */
    public function can($permissionName, $params = [], $allowCaching = true) {

        // get current user if not specified
        $user = (!$params) ? $params : $this->getIdentity();

        // check role attribute
        //$roleAttribute = "can_{$permission}";
        //return ($user and $user->role->$roleAttribute);
        
        return ($user and $user->can($permissionName));
        
    }

    /**
     * Check if user cant do $permission
     *
     * @param string $permission
     * @param bool $user
     * @return bool
     */
    public function cant($permission, $user = false) {
        return !$this->can($permission, $user);
    }
    
    public function hasRole($role, $user = false){
        $user = ($user !== false) ? $user : $this->getIdentity();
        if(is_numeric($role)){
          return ($user and $user->hasRole($role));
        } else {
          return ($user and $user->hasRoleName($role));  
        }
    }
}

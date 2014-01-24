<?php

namespace yii\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\AccessControl;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

use yii\user\models\User;
use yii\user\models\UserRole;
use yii\user\models\Profile;
use yii\user\models\Role;
use yii\user\models\Session;
use yii\user\models\forms\LoginForm;
use yii\user\models\forms\ForgotForm;
use yii\user\models\forms\ResetForm;


/**
 * Default controller for User module
 */
class DefaultController extends Controller {

    /**
     * @inheritdoc
     */
//    public $defaultAction = "profile";

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'confirm', 'captcha'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['account', 'profile', 'resend', 'cancel', 'logout', 'captcha'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register', 'forgot', 'reset', 'captcha'],
                        'allow' => true,
                        'roles' => ['?', '*'],
                    ],
                ],
            ],
        ];
    }

    /**
    * put your comment there...
    * 
    */
    public function actions()
    {
            return [
                    'captcha' => [
                            'class' => 'yii\captcha\CaptchaAction',                            
                    ],
            ];
    }    
    
    /**
     * Display index
     */
    public function actionIndex() {

        // display debug page if YII_DEBUG is set
        if (defined('YII_DEBUG')) {
            $actions = Yii::$app->getModule("user")->getActions();
            return $this->render('index', ["actions" => $actions]);
        }
        // redirect to login page if user is guest
        elseif (Yii::$app->user->isGuest) {
            return $this->redirect(["/user/login"]);
        }
        // redirect to account page if user is logged in
        else {
            return $this->redirect(["/user/account"]);
        }
    }

    /**
     * Display login page and log user in
     */
    public function actionLogin() {

        // load data from $_POST and attempt login
        $model = new LoginForm();
        //$model = self::getLoginForm();
        if ($model->load($_POST) && $model->login(Yii::$app->getModule("user")->loginDuration)) {
            return $this->goBack();
        }

        // render view
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Log user out and redirect home
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Display register page
     */
    public function actionRegister($role = Role::ROLE_USER) {

        /** @var User $user */
        $User = $this->getUser(["scenario" => "register"]);        
        
        /** @var Profile $profile */
        $Profile = $this->getProfile();
        
        /** @var UserRole $userRole */
        $UserRole = new UserRole();
        
        // Get extented models
        $models = $this->getExtentedModels();
                    
        if ($User->load($_POST)) {

            // validate for ajax request
            $Profile->load($_POST);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;                                
                
                $models = array_merge(
                    [
                        'role' => Role::find($role),
                        'user_role' => $UserRole,
                        'user' => $User,
                        'profile' => $Profile,
                    ],
                    $models
                );
                
                //return ActiveForm::validate( $User, $Profile );
                $result = [];
                
                $validate = true;
                
                foreach ($models as $model) {
                    $model->load($_POST);
                    $model->validate(null);
                    foreach ($model->getErrors() as $attribute => $errors) {
                        $result[Html::getInputId($model, $attribute)] = $errors;
                        $validate = false;
                    }
                }
                
                //Yii::$app->session->setFlash(self::className(), 'В процессе обработки формы возникла ошибка');
                
                return $result;                
                
            }                       

            // validate for normal request
            if ($User->validate() and $Profile->validate()) {

                $transaction = Yii::$app->db->beginTransaction();
                
                // perform registration
                $User->register();
                
                // profile registration
                $Profile->register($User->id);
                
                // attached user role                
                $UserRole->register($User->id, Role::ROLE_USER);
                
                // Add main models                
                $_POST[ self::getClassName($User) ] = $User->getAttributes();
                $_POST[ self::getClassName($Profile) ] = $Profile->getAttributes();
                $_POST[ self::getClassName($UserRole) ] = $UserRole->getAttributes();
                
                $validate = true;
                
                // Save extented models                
                foreach($models as $model){
                    $model->load($_POST);
                    if(!$model->validate()){
                        $validate = false;
                        break;
                    }
                    $model->save(false);
                    $_POST[ self::getClassName($model) ] = $model->getAttributes();                    
                }
                
                if($validate){
                
                    $transaction->commit();
                    
                    $this->_calcEmailOrLogin($User);

                    // set flash
                    Yii::$app->session->setFlash("Register-success", $User->getDisplayName());
                    
                    return $this->redirect(['index']);                             
                
                } else {
                    
                    Yii::$app->session->setFlash("Register-error");
                    
                }                
                
                $transaction->rollback();
                
            }
        }
        
        $models = array_merge(
            [
                'role' => Role::find($role),
                'user_role' => $UserRole,
                'user' => $User,
                'profile' => $Profile,
            ],
            $models
        );        

        // render view
        return $this->render("register", $models);
    }

    /**
     * Calculate whether we need to send confirmation email or log user in
     *
     * @param User $user
     */
    protected function _calcEmailOrLogin($user) {

        // determine session type to see if we need to send email
        $sessionType = null;
        if ($user->status == User::STATUS_INACTIVE) {
            $sessionType = Session::TYPE_EMAIL_ACTIVATE;
        }
        elseif ($user->status == User::STATUS_UNCONFIRMED_EMAIL) {
            $sessionType = Session::TYPE_EMAIL_CHANGE;
        }

        // generate session and send email
        if ($sessionType !== null) {
            $session = Session::generate($user->id, $sessionType);
            $numSent = $user->sendEmailConfirmation($session);
        }
        // login user in automatically
        else {
            Yii::$app->user->login($user, Yii::$app->getModule("user")->loginDuration);
        }
    }

    /**
     * Confirm email
     */
    public function actionConfirm($hash = "", $sid = "") {

        // search for session
        $session = Session::findActiveByKey($hash, $sid, [Session::TYPE_EMAIL_ACTIVATE, Session::TYPE_EMAIL_CHANGE]);
        if ($session) {

            // confirm user
            /** @var User $user */
            $user = User::find($session->user_id);
            $user->confirm();

            // consume session
            $session->consume();

            // set flash and refresh
            Yii::$app->session->setFlash("Confirm-success", $user->email);
            $this->refresh();
        }

        // render view
        return $this->render("confirm", [
            "session" => $session,
        ]);

    }

    /**
     * Account
     */
    public function actionAccount() {

        // set up user/profile and attempt to load data from $_POST
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $user->setScenario("account");
        if ($user->load($_POST)) {

            // validate for ajax request
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($user);
            }

            // validate for normal request
            if ($user->validate()) {

                // generate session and send email if user changed his email
                if (Yii::$app->getModule("user")->emailChangeConfirmation and $user->checkAndPrepareEmailChange()) {
                    $session = Session::generate($user->id, Session::TYPE_EMAIL_CHANGE);
                    $numSent = $user->sendEmailConfirmation($session);
                }

                // save, set flash, and refresh page
                $user->save(false);
                Yii::$app->session->setFlash("Account-success", true);
                $this->refresh();
            }
        }

        // render view
        return $this->render("account", [
            'user' => $user,
        ]);
    }

    /**
     * Profile
     */
    public function actionProfile() {
        
        $profile = (object) [ 'formName' => function(){ return 'Хрен'; } ];

        // set up profile and attempt to load data from $_POST
        /** @var Profile $profile */
        /*$profile = Yii::$app->user->identity->profile;
        if ($profile->load($_POST)) {

            // validate for ajax request
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($profile);
            }

            // validate for normal request
            if ($profile->validate()) {

                // call something here if needed

                // save - pass false in so that we don't have to validate again
                $profile->save(false);
                Yii::$app->session->setFlash("Profile-success", true);
                $this->refresh();
            }
        }*/

        // render view
        return $this->render("profile", [
            'profile' => $profile,
        ]);
    }

    /**
     * Resend email change confirmation
     */
    public function actionResend() {

        // attempt to find session and get user/profile to send confirmation email
        $session = Session::findActiveByUser(Yii::$app->user->id, Session::TYPE_EMAIL_CHANGE);
        if ($session) {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            $user->sendEmailConfirmation($session);

            // set flash message
            Yii::$app->session->setFlash("Resend-success", true);
        }

        // go to account page
        return $this->redirect(["/user/account"]);
    }

    /**
     * Cancel email change
     */
    public function actionCancel() {

        // attempt to find session
        $session = Session::findActiveByUser(Yii::$app->user->id, Session::TYPE_EMAIL_CHANGE);
        if ($session) {

            // remove user.new_email
            /** @var User $user */
            $user = Yii::$app->user->identity;
            $user->new_email = null;
            $user->save(false);

            // delete session and set flash message
            $session->expire();
            Yii::$app->session->setFlash("Cancel-success", true);
        }

        // go to account page
        return $this->redirect(["/user/account"]);
    }

    /**
     * Forgot password
     */
    public function actionForgot() {

        // attempt to load $_POST data, validate, and send email
        $model = new ForgotForm();
        //$model = self::getForgotForm();
        if ($model->load($_POST) && $model->sendForgotEmail()) {

            // set flash and refresh page
            Yii::$app->session->setFlash('Forgot-success');
            return $this->refresh();
        }

        // render view
        return $this->render('forgot', [
            'model' => $model,
        ]);
    }

    /**
     * Reset password
     */
    public function actionReset($hash, $sid) {

        // check for success or invalid session
        $session = Session::findActiveByKey($hash, $sid, Session::TYPE_PASSWORD_RESET);
        $success = Yii::$app->session->getFlash('Reset-success');
        $invalidKey = !$session;
        if ($success or $invalidKey) {

            // render view with invalid flag
            // using setFlash()/refresh() would cause an infinite loop
            return $this->render('reset', compact("success", "invalidKey"));
        }

        // attempt to load $_POST data, validate, and reset user password
        $model = new ResetForm(["session" => $session]);
        //$model = self::getResetForm(["session" => $session]);
        if ($model->load($_POST) && $model->resetPassword()) {

            // set flash and refresh page
            Yii::$app->session->setFlash('Reset-success');
            return $this->refresh();
        }

        // render view
        return $this->render('reset', [
            'model' => $model,
        ]);
    }
    
    protected function getUser($params = []){
        return new User ($params);
    }
    
    protected function getProfile($params = []){
        return new Profile ($params);
    }
    
    protected function getExtentedModels(){
        return [];
    }
            
    /*protected static function LoginForm($params = []){
        return new LoginForm ($params);
    }
    
    protected static function getResetForm($params = []){
        return new ResetForm ($params);
    }

    protected static function ForgotForm($params = []){
        return new ForgotForm ($params);
    }*/
    
    private static function getClassName($class){
        return join('', array_slice(explode('\\', get_class($class)), -1));
    }             
}
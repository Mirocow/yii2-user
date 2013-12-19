<?php

namespace yii\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\VerbFilter;
use yii\user\models\User;
use yii\user\models\Profile;
use yii\user\models\search\UserSearch;

/**
 * AdminController implements the CRUD actions for User model.
 */
class AdminController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init() {

        // check for admin permission
        if (!Yii::$app->user->can("admin")) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        parent::init();
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UserSearch;
        $dataProvider = $searchModel->search($_GET);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single User model.
     *
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate() {
        $user = new User;
        $user->setScenario("admin");
        $profile = new Profile;

        if ($user->load($_POST) && $user->validate() && $profile->load($_POST) and $profile->validate()) {
            $user->save();
            $profile->setUser($user->id)->save(false);
            return $this->redirect(['view', 'id' => $user->id]);
        }
        else {
            return $this->render('create', [
                'user' => $user,
                'profile' => $profile,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $user = $this->findModel($id);
        $user->setScenario("admin");
        $profile = $user->profile;

        if ($user->load($_POST) && $user->validate() && $profile->load($_POST) and $profile->validate()) {
            $user->save();
            $profile->save();
            return $this->redirect(['view', 'id' => $user->id]);
        }
        else {
            return $this->render('update', [
                'user' => $user,
                'profile' => $profile,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {

        // delete profile first to handle foreign key constraint
        $user = $this->findModel($id);
        $profile = $user->profile;
        $profile->delete();
        $user->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     * @return User the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = User::find($id)) !== null) {
            return $model;
        }
        else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }
}

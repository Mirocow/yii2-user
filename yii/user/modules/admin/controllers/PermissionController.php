<?php

namespace yii\user\modules\admin\controllers;

use Yii;
use yii\user\models\Permission;
use yii\user\models\PermissionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\VerbFilter;

use yii\user\modules\admin\components\AdminController;

/**
 * PermissionController implements the CRUD actions for Permission model.
 */
class PermissionController extends AdminController
{
	public function behaviors()
	{
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
	 * Lists all Permission models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new PermissionSearch;
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}

	/**
	 * Displays a single Permission model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new Permission model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Permission;

		if ($model->load($_POST) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Permission model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load($_POST) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Permission model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();
		return $this->redirect(['index']);
	}

	/**
	 * Finds the Permission model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Permission the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if ($id !== null && ($model = Permission::find($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}

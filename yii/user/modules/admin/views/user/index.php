<?php

use yii\helpers\Html;
use yii\grid\GridView;

use yii\user\models\User;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\core\models\UserSearch $searchModel
 */

$this->title = 'Users';
$this->params['breadcrumbs'][] = ['label' => 'admin', 'url' => ['/user/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			//['class' => 'yii\grid\SerialColumn'],

			'id',
			'username',
			//'phone',
						[
								'attribute' => 'status',
								'label' => 'Status',
								'filter' => User::statusDropdown(),
								'value' => function($model, $index, $dataColumn) {
										$statusDropdown = User::statusDropdown();
										return $statusDropdown[$model->status];
								},
						],
			'email:email',
			// 'new_email:email',
			// 'password',
			// 'hash',
			// 'ban_time',
			// 'ban_reason',
			// 'create_time',
			// 'update_time',
			// 'data:ntext',

			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{update} {delete}',
				'buttons' => ['delete' => function ($url, $model) {
						if($model->id > 1){
								return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
										'title' => Yii::t('yii', 'Delete'),
										'data-confirm' => Yii::t('yii', 'Are you sure to delete this item?'),
										'data-method' => 'post',
								]);
						}
				}],
			],
		],
	]); ?>

</div>

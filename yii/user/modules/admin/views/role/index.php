<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\core\models\RoleSearch $searchModel
 */

$this->title = 'Roles';
$this->params['breadcrumbs'][] = ['label' => 'admin', 'url' => ['/user/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Create Role', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			//['class' => 'yii\grid\SerialColumn'],

			'id',
			'machine_name',
			'name',
			'description',
			'grant:boolean',
			// 'create_time',
			// 'update_time',

						[
								'class' => 'yii\grid\ActionColumn',
								'template' => '{update} {delete}',
						],
		],
	]); ?>

</div>

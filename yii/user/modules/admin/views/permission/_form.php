<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\core\models\Permission $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="permission-form">

	<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'machine_name')->textInput(['maxlength' => 50]) ?>

		<?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>

		<?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>

		<?= $form->field($model, 'type')->textInput() ?>

		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>

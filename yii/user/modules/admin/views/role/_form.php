<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\core\models\Role $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="role-form">

	<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'machine_name')->textInput(['maxlength' => 20]) ?>

		<?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>

		<?= $form->field($model, 'name')->textInput(['maxlength' => 20]) ?>
        
        <?= Html::listBox('permissions', $model->getPermissions(true), $model->getPermissions(), ['prompt' => '', 'maxlength' => 255]) ?>        
                
		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>

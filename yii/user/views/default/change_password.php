<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var yii\user\models\forms\ForgotForm $model
 */
$this->title = 'Change password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-forgot">
  <h1><?= Html::encode($this->title) ?></h1>

  <?php if (Yii::$app->session->getFlash('Change-password-success')): ?>

        <div class="alert alert-success">
          Your password change successful    
        </div>

  <?php else: ?>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'reset-form']); ?>
                    <?= $form->field($model, 'newPassword')->passwordInput() ?>
                    <?= $form->field($model, 'newPasswordConfirm')->passwordInput() ?>
                    <div class="form-group">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

  <?php endif; ?>
</div>
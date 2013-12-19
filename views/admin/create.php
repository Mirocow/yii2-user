<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var mirocow\user\models\User $user
 * @var mirocow\user\models\Profile $profile
 */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php echo $this->render('_form', [
		'user' => $user,
        'profile' => $profile,
	]); ?>

</div>

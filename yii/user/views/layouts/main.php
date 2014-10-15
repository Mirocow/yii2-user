<?php
use yii\helpers\Html;
//use yii\bootstrap\Nav;
//use yii\bootstrap\NavBar;
//use yii\widgets\Breadcrumbs;
use yii\user\assets\UserAsset;

/**
 * @var \yii\web\View $this
 * @var string $content
 */

UserAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

<?php echo $content;?>

<p><a href="#" onclick="history.go(-1);">Назад</a></p>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
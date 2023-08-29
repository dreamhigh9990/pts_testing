<?php
use app\assets\LoginAsset;
use yii\helpers\Html;
LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body data-col="1-column" class=" 1-column  blank-page blank-page">
<?php $this->beginBody() ?>
  <div class="wrapper nav-collapsed menu-collapsed">
      <div class="main-panel">
        <div class="main-content">
          <div class="content-wrapper">
    		 <?= $content ?>
          </div>
        </div>
       </div>
  </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

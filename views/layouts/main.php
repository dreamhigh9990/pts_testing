<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\AppAsset;

use yii\helpers\Html;

use yii\bootstrap\Nav;

use yii\bootstrap\NavBar;

use yii\widgets\Breadcrumbs;

use common\widgets\Alert;

AppAsset::register($this);

// print_r($projects);

$csrfToken = Yii::$app->request->csrfToken;

$baseurl=Yii::getAlias('@web');

// echo $baseurl;

?>

<?php $this->beginPage() ?>

<!DOCTYPE html>

<html lang="<?= Yii::$app->language ?>">

<head>

    <meta charset="<?= Yii::$app->charset ?>">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">

    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title).' - '.Yii::$app->name;?></title>

    <?php $this->head() ?>
    <link rel="icon" href="<?= Yii::getAlias('@web').'/favicon.png';?>" type="image/png">
    <script type="text/javascript" src='https://maps.google.com/maps/api/js?sensor=false&libraries=places&key=AIzaSyAE4AiFYAYHt9eBXwwN8zrb4wl0wQXnWWc'></script>


    <script>

          var baseurl = "<?php echo Yii::$app->urlManager->createAbsoluteUrl('', array(), 'https'); ?>";

         var _csrf= '<?=$csrfToken ?>';

    </script>

</head>

<body>

<?php $this->beginBody() ?>
<body data-col="2-columns" class="2-columns">
	<div class="pjax-loader"><i class=" spin-content fa fa-spinner fa-spin fa-lg"></i></div>
    <div class="wrapper nav-collapsed menu-collapsed">
    <?php if(Yii::$app->controller->id != "sync"){ ?>
        <?php include('leftbar.php')?>        
        <?php } ?>
        <div class="main-panel">
        <?php if(Yii::$app->controller->id != "sync"){ ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-faded ds-none">
           <div class="navbar-header">
               <button type="button" data-toggle="collapse" class="navbar-toggle d-lg-none float-right"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
             </div>
         </nav>
        <?php } ?>

            <div class="main-content">
             
                <div class="content mb-2 <?php echo Yii::$app->controller->id == "sync" ? 'custom-visual-div' : ''; ?>">

                    <?php echo $content;?>

                </div>

            </div>

        </div>

    </div>
   
    <?php include('_modal.php')?>

</body>

<?php $this->endBody() ?>

</html>

<?php $this->endPage() ?>


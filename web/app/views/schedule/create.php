<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\VehicleSchedule */

$this->title = Yii::t('app', 'Create Vehicle Schedule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vehicle Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vehicle-schedule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

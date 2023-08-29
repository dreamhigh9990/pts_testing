<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Log */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;  
?>
<div class="log-view">

     <h1><?= Html::encode($this->title) ?><?= Html::a('Back', ['/admin/log/index'],['class'=>'pull-right btn btn-success'])?></h1>
    <div class="row">
            <div class="col-md-6">
                 <div class="card-header">
                    <h4 class="card-title mb-0">Request</h4>
                </div>
                <pre>
                    <?php
                        print_r(json_decode($model->request));
                    ?>
                </pre>
            </div>
            <div class="col-md-6">
                 <div class="card-header">
                    <h4 class="card-title mb-0">Response</h4>
                </div>
                <pre>
                    <?php
                       print_r(json_decode($model->response));
                    ?>
                </pre>
            </div>
    </div>
</div>

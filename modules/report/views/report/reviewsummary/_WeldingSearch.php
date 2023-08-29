<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
?>
<!-- <div class="table-responsive"> -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        "filterSelector" => "select[name='per-page']",
        'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
        'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
        'emptyText' => Yii::$app->trans->getTrans('No results found.'),
        'columns' => [
            'date',
            [
                'attribute' => 'report_number',
                'filter' => true,
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->report_number,['/welding/welding/create','EditId'=>$model->id],['class'=>"card-link"]);
                },
            ],
            'weld_number',
            [
                'attribute' => 'line_type',
                'filter' => ['Main Line' => Yii::$app->trans->getTrans('Main Line'), 'Tie Line' => Yii::$app->trans->getTrans('Tie Line')],
                'headerOptions' => ['style' => 'width:5%'],
                'value' => function($model){
                    if($model->line_type == 'Main Line'){
                        return Yii::$app->trans->getTrans('Main Line');
                    } else if($model->line_type == 'Tie Line'){
                        return Yii::$app->trans->getTrans('Tie Line');
                    }
                }
            ],  
            'kp',
            'pipe_number',
            'next_pipe',
            'visual_acceptance',
            [
                'attribute' => 'created_by',
                'label' => Yii::$app->trans->getTrans('User'),
                'value' => function ($model) {
                    $List = Yii::$app->general->employeeList("");	
                    return !empty($List[$model->created_by]) ? $List[$model->created_by] : "";
                },
            ],
            'signed_off',
        ],
    ]); ?>
<!-- </div> -->
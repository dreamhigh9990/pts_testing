<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
?>
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
                return Html::a($model->report_number,['/precommissioning/cleangauge/create','EditId'=>$model->id],['class'=>"card-link"]);
            },
        ],
        'from_kp',
        'to_kp',
        [
            'attribute' => 'created_by',
            'label' => Yii::$app->trans->getTrans("User"),
            'value' => function ($model) {
                $List = Yii::$app->general->employeeList("");	
                return !empty($List[$model->updated_by])?$List[$model->updated_by]:"";
            },
        ],
        'signed_off',
    ],
]); ?>
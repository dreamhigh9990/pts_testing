<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
/* @var $this yii\web\View */
/* @var $searchModel app\models\VehicleInspectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    "filterSelector" => "select[name='per-page']",
    'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
    'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
    'emptyText' => Yii::$app->trans->getTrans('No results found.'),
    'columns' => [
        ['class' => 'yii\grid\CheckboxColumn'],
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    $url = Url::to(['/vehicle/inspection/create', 'EditId' => $model->id]);
                    if(!empty($_GET)){
                        $urlParams = Yii::$app->general->getFilters();
                        $urlParams[] = '/vehicle/inspection/create';
                        $urlParams['EditId'] = $model->id;
                        $url = Url::to($urlParams);
                    }
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,  ['title' => Yii::t('app', 'lead-update'),]);
                },
            ],
        ],
        [
            'attribute' => 'date',
            'format' => 'raw',			
            'filter' => DateRangePicker::widget([
                'model' => $searchModel, 
                'name' => 'VehicleInspectionSearch[date]',
                'convertFormat'=>true,
                'value' => $searchModel->date,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' / ',
                        ],
                    ]
            ])
        ],
        [
            'attribute' => 'vehicle_id',
            'value' => function ($model) {
                $getVehicleNumber = \app\models\VehicleSchedule::find()->select('vehicle_number')->where(['id' => $model->vehicle_id])->asArray()->one();
                return !empty($getVehicleNumber) ? $getVehicleNumber['vehicle_number'] : '';
            },
        ],
        'location',
        [
            'attribute' => 'service_due',
            'filter' => ['Yes' => 'Yes', 'No' => 'No'],
        ],
        [
            'attribute' => 'created_by',
            'label' => Yii::$app->trans->getTrans("User"),
            'filter' => Yii::$app->general->employeeList(""),
            'value' => function ($model) {
                $List = Yii::$app->general->employeeList("");	
                return !empty($List[$model->created_by]) ? $List[$model->created_by] : "";
            },
        ],
        [
            'attribute' => 'qa_manager',
            'filter' => Yii::$app->general->employeeList(""),
            'value' => function ($model) {
                $List = Yii::$app->general->employeeList("");		
                return !empty($List[$model->qa_manager]) ? $List[$model->qa_manager] : "";
            },
        ],
        [
            'attribute' => 'signed_off',
            'filter' => ['Yes' => 'Yes', 'No' => 'No']
        ],
    ],
]); ?>
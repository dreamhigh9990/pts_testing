<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);

$defectPosition = [
    'Root OS' => 'Root OS',
    'Root TS' => 'Root TS',
    'Hot OS' => 'Hot OS',
    'Hot TS' => 'Hot TS',
    'Fill OS' => 'Fill OS',
    'Fill TS' => 'Fill TS',
    'Cap OS' => 'Cap OS',
    'Cap TS' => 'Cap TS',
];
?>
<!-- <div class="table-responsive"> -->
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
                        $url = Url::to(['/welding/ndt/create', 'EditId' => $model->id]);
                        if(!empty($_GET)){
                            $urlParams = Yii::$app->general->getFilters();
                            $urlParams[] = '/welding/ndt/create';
                            $urlParams['EditId'] = $model->id;
                            $url = Url::to($urlParams);
                        }
                        if(Yii::$app->general->hasEditAccess($model->created_by)){
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => Yii::t('app', 'lead-update')]);
                        } else {
                            $url = Yii::$app->general->clientViewFilter($url);
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['title' => Yii::t('app', 'lead-update')]);
                        }
                    },
                ],
            ],
            [
                'attribute' => 'date',
                'format' => 'raw',
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'name' => 'NdtSearch[date]',
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
            'report_number',
            'kp',
            'weld_number',
            [
                'attribute' => 'weldType',
                'label' => Yii::$app->trans->getTrans('Weld Type'),
                'filter' => true,
                'value' => function($model){
                    
                    $weldData = \app\models\Welding::find()->where(['id'=>$model->main_weld_id])
                    // ->andWhere(['has_been_cut_out' => 'No'])
                    ->active()->asArray()->one();
                    // print_r($model);die;
                    
                    return !empty($weldData) ? $weldData['weld_type']: '-';
                 
                }
            ],
            [
                'attribute' => 'weldSubType',
                'label' => Yii::$app->trans->getTrans('Weld Sub Type'),
                'filter' => true,
                'value' => function($model){
                    $weldData = \app\models\Welding::find()->where(['id'=>$model->main_weld_id])
                    // ->andWhere(['has_been_cut_out' => 'No'])
                    ->active()->asArray()->one();
                    return !empty($weldData) ? $weldData['weld_sub_type'] : '-';
                       
                }
            ],
            [
                'attribute' => 'outcome',
                'filter' => ['Accepted'=>'Accepted', 'Rejected'=>'Rejected']
            ],           
            [
                'attribute' => 'created_by',
                'label' => Yii::$app->trans->getTrans('User'),
                'filter' => Yii::$app->general->employeeList(""),
                'value' => function ($model) {
                    $List = Yii::$app->general->employeeList("");	
                    return !empty($List[$model->created_by]) ? $List[$model->created_by] : "";
                },
            ],            
            [
                'attribute' => 'qa_manager',
                'filter' =>Yii::$app->general->employeeList(""),
                'value' => function ($model) {
                    $List = Yii::$app->general->employeeList("");		
                    return !empty($List[$model->qa_manager])?$List[$model->qa_manager]:"";
                },
            ],
            [
                'attribute' => 'signed_off',
                'filter' =>['Yes'=>'Yes','No'=>'No']
            ],
        ],
    ]); 
    ?>
<!-- </div> -->
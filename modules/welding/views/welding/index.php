<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
?>
<!-- <div class="table-responsive long"> -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id'=>'welding',
        'filterModel' => $searchModel,
        "filterSelector" => "select[name='per-page']",
        'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
        'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
        'emptyText' => Yii::$app->trans->getTrans('No results found.'),
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn','headerOptions' => ['style' => 'width:2%']],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['style' => 'width:2%'],
                'template'=>'{update}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        $url = Url::to(['/welding/welding/create', 'EditId' => $model->id]);
                        if(!empty($_GET)){
                            $urlParams = Yii::$app->general->getFilters();
                            $urlParams[] = '/welding/welding/create';
                            $urlParams['EditId'] = $model->id;
                            $url = Url::to($urlParams);
                        }
                        return $this->render('//partials/_clientView', ['url'=>$url, 'created_by' => $model->created_by]);
                        
                    },
                ],
            ],
            [
                'attribute' => 'date',
                'format' => 'raw',
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'name' => 'WeldingSearch[date]',
                    'convertFormat'=>true,
                    'value' => $searchModel->date,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' / ',
                        ],
                    ]
                ]) ,
                'headerOptions' => ['style' => 'width:4%'],
            ],
            [
                'attribute' => 'report_number',
                'headerOptions' => ['style' => 'width:8%'],
            ],  
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
            [
                'attribute' =>  'kp',
                'headerOptions' => ['style' => 'width:5%'],
            ],
            [
				'attribute' => 'weld_number',
				'headerOptions' => ['style' => 'width:5%'],
            ], 
            [
				'attribute' => 'pipe_number',
				'headerOptions' => ['style' => 'width:5%'],
            ], 
            [
				'attribute' => 'next_pipe',
				'headerOptions' => ['style' => 'width:5%'],
            ],
            [
				'attribute' => 'weld_type',
				'headerOptions' => ['style' => 'width:5%'],
            ],
            [
				'attribute' => 'weld_crossing',
				'headerOptions' => ['style' => 'width:5%'],
            ],
            [
				'attribute' => 'weld_sub_type',
				'headerOptions' => ['style' => 'width:5%'],
			],
            [
                'attribute' => 'visual_acceptance',
                'headerOptions' => ['style' => 'width:5%'],
				'filter' => ['Yes'=>'Yes', 'No'=>'No',]
            ],
            [
                'attribute' => 'has_been_cut_out',
                'headerOptions' => ['style' => 'width:5%'],
				'filter' => ['Yes'=>'Yes', 'No'=>'No',]
            ],
            [
                'attribute' => 'signed_off',
                'filter' =>['Yes'=>'Yes','No'=>'No'],
                'headerOptions' => ['style' => 'width:5%'],
            ],
            [
                'attribute' => 'created_by',
                'headerOptions' => ['style' => 'width:8%'],
                'filter' => Yii::$app->general->employeeList(""),
                'value' => function ($model) {
                    $List = Yii::$app->general->employeeList("");	
                    return !empty($List[$model->created_by]) ? $List[$model->created_by] : "";
                },
            ],
            [
                'attribute' => 'qa_manager',
                'headerOptions' => ['style' => 'width:8%'],
				'filter' =>Yii::$app->general->employeeList(""),
				'value' => function ($model) {
                    $List = Yii::$app->general->employeeList("");		
                    return !empty($List[$model->qa_manager])?$List[$model->qa_manager]:"";
				},
			],
        ],
    ]); ?>
<!-- </div> -->
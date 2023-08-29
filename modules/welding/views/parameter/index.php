<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
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
                        $url = Url::to(['/welding/parameter/create', 'EditId' => $model->id]);
                        if(!empty($_GET)){
                            $urlParams = Yii::$app->general->getFilters();
                            $urlParams[] = '/welding/parameter/create';
                            $urlParams['EditId'] = $model->id;
                            $url = Url::to($urlParams);
                        }
                        // Allow client to see data
                        return $this->render('//partials/_clientView', ['url'=>$url, 'created_by' => $model->created_by]);
                        // if(Yii::$app->general->hasEditAccess($model->created_by)){
                        //     $icon = 'pencil';
                        //     if(Yii::$app->user->identity->type == 'Client') $icon = 'eye-open';
                        //     return Html::a('<span class="glyphicon glyphicon-'.$icon.'"></span>', $url, ['title' => Yii::t('app', 'lead-update')]);
                        // } else {
                        //     $url = Yii::$app->general->clientViewFilter($url);
                        //     return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['title' => Yii::t('app', 'lead-update')]);
                        // }
                    },
                ],
            ],
            [
                'attribute' => 'date',
                'format' => 'raw',
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'name' => 'ParameterSearch[date]',
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
            'weld_number',
            'kp',
            'report_number',
            [
                'attribute' => 'welder',
                'filter' => Yii::$app->general->TaxonomyDrop(7),
            ],
            'pass_number',
            [
                'attribute' => 'k_factor',
                'filter' => true,
                'value' => function($model){
                    return !empty($model->k_factor) ? $model->k_factor : 0;
                }
            ],
            [
                'attribute' => 'rot',
                'filter' => true,
                'value' => function($model){
                    return !empty($model->rot) ? $model->rot : 0;
                }
            ],
            [
                'attribute' => 'wire_speed',
                'filter' => true,
                'value' => function($model){
                    return !empty($model->wire_speed) ? $model->wire_speed : 0;
                }
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
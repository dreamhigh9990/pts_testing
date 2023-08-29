<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
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
                        $url = Url::to(['/civil/trenching/create', 'EditId' => $model->id]);
                        if(!empty($_GET)){
                            $urlParams = Yii::$app->general->getFilters();
                            $urlParams[] = '/civil/trenching/create';
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
                    'name' => 'TrenchingSearch[date]',
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
            'from_kp',
            // 'from_weld',
            'to_kp',
            // 'to_weld',
            [
                'attribute' => 'created_by',
                'label' => Yii::$app->trans->getTrans('User'),
                'filter' =>Yii::$app->general->employeeList(""),
                'value' => function ($model) {
                    $List = Yii::$app->general->employeeList("");	
                    return !empty($List[$model->created_by])?$List[$model->created_by]:"";
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
    ]); ?>
<!-- </div> -->
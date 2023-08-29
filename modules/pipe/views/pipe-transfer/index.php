<?php
use yii\helpers\Html;
use yii\helpers\Url;;
use yii\grid\GridView;
use app\models\Pipe;
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
                        $url = Url::to(['/pipe/pipe-transfer/create', 'EditId' => $model->id]);
                        if(!empty($_GET)){
                            $urlParams = Yii::$app->general->getFilters();
                            $urlParams[] = '/pipe/pipe-transfer/create';
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
                    'name' => 'PipeTransferSearch[date]',
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
            'pipe_number',
            [
				'attribute' => 'pipe_number',
				'label' => Yii::$app->trans->getTrans('Pipe Length'),
				'filter' => false,
				'value' => function($model){
					$Pipe = \app\models\Pipe::find()->where(['pipe_number' => $model->pipe_number])->active()->asArray()->one();
					return !empty($Pipe['length']) ? $Pipe['length'] : '';
				}
			],	
            [
                'attribute' => 'pipe_number',
                'label' => Yii::$app->trans->getTrans("Pipe Weight"),
                'filter'=> false,
                'value' => function ($model) {
                    $Pipe = Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->asArray()->one();                  
                    return !empty($Pipe['weight'])?$Pipe['weight']:0;
                },
            ],
            [
                'attribute' => 'new_location',
                'filter' => Html::activeDropDownList($searchModel,'new_location',Yii::$app->general->TaxonomyDrop(2),['class'=>'form-control js-example-basic-multiple form-control','multiple'=>true]),				
            ],            
            [
                'attribute' => 'truck',
                'filter' =>Yii::$app->general->TaxonomyDrop(1)			
            ],
            // [
            //     'attribute' => 'defacts',
            //     'filter' => Html::activeDropDownList($searchModel,'defacts',Yii::$app->general->TaxonomyDrop(8),['class'=>'form-control js-example-basic-multiple form-control','multiple'=>true]),				
            // ],
            [
                'attribute' => 'created_by',
                'label' => Yii::$app->trans->getTrans("User"),
                'filter' => Yii::$app->general->employeeList(""),
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
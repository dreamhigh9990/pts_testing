<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
?>

	<?php echo GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			"filterSelector" => "select[name='per-page']",
			"layout" => "<div class='row'><div class='col-md-6'>{summary}</div><div class='col-md-6 right'>{$perpage}</div></div>\n<div class='table-responsive long'>{items}</div>\n{pager}",
			'columns' => [      
			['class' => 'yii\grid\CheckboxColumn'],
			[
				'class' => 'yii\grid\ActionColumn',
				'template'=>'{update}',
				'buttons' => [
					'update' => function ($url, $model) {
						$url = Url::to(['/pipe/reception/create', 'EditId' => $model->id]);
						return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,  ['title' => Yii::t('app', 'lead-update'),]);
					},
				],
			],
			[
				'attribute' => 'date',
				'format' => 'raw',
				'label' => "Date",				
				'filter' => DateRangePicker::widget([
					'model' => $searchModel, 
					'name' => 'ReceptionSearch[date]',
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
				'label' => "Pipe Weight",
				'filter' =>false,
				'value' => function ($model) {
						$Pipe = \app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->asArray()->one();
						return !empty($Pipe['weight'])?$Pipe['weight']:"";
				},
			],
			[
				'attribute' => 'truck',
				'filter' =>Yii::$app->general->TaxonomyDrop(1)			
			],
			[
				'attribute' => 'location',
				'filter' => Html::activeDropDownList($searchModel,'location',Yii::$app->general->TaxonomyDrop(2),['class'=>'form-control js-example-basic-multiple form-control','multiple'=>true]),				
			],	
			[
				'attribute' => 'created_by',
				'label' => "User",
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
	]);
	?>
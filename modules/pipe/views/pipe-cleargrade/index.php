<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
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
		['class' => 'yii\grid\ActionColumn',
			'template'=>'{update}',
			'buttons' => [
				'update' => function ($url, $model) {
					$url = Url::to(['/pipe/pipe-cleargrade/create', 'EditId' => $model->id]);
					if(!empty($_GET)){
						$urlParams = Yii::$app->general->getFilters();
						$urlParams[] = '/pipe/pipe-cleargrade/create';
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
				'name' => 'CleargradeSearch[date]',
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
		[
			'attribute' => 'location',
			'filter' => Html::activeDropDownList($searchModel,'location',Yii::$app->general->TaxonomyDrop(2),['class'=>'form-control multiple-select2 form-control','multiple'=>true]),				
		],
		'start_kp',
		'end_kp',
		[
			'attribute' => 'created_by',
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
			'filter' =>['Yes'=>'Yes','No'=>'No']
		],
	],
]); ?>
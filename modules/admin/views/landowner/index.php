<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
$Project = Yii::$app->general->TaxonomyDrop(4,true);

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
		['class' => 'yii\grid\ActionColumn',
			'template'=>'{update}',
			'buttons' => [
				'update' => function ($url, $model) {
					$url = Url::to(['/admin/landowner/create', 'EditId' => $model->id]);
					if(!empty($_GET)){
						$urlParams = Yii::$app->general->getFilters();
						$urlParams[] = '/admin/landowner/create';
						$urlParams['EditId'] = $model->id;
						$url = Url::to($urlParams);
					}
					return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,  ['title' => Yii::t('app', 'lead-update'),]);
				},           
			],
		],
		'from_kp',
		'to_kp',
		'landholder',
		[
			'attribute' => 'created_by',
			'label' => Yii::$app->trans->getTrans("User"),
			'filter' =>Yii::$app->general->employeeList(""),
			'value' => function ($model) {
						$List = Yii::$app->general->employeeList("");	
						return !empty($List[$model->created_by])?$List[$model->created_by]:"";
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
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
    
echo GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'filterSelector' => "select[name='per-page']",
	'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
	'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
	'emptyText' => Yii::$app->trans->getTrans('No results found.'),
	'columns' => [
		['class' => 'yii\grid\CheckboxColumn', 'headerOptions' => ['style' => 'width: 1%']],
		[
			'class' => 'yii\grid\ActionColumn',
			'headerOptions' => ['style' => 'width:1%'],
			'template' => '{update}',
			'buttons' => [
				'update' => function ($url, $model) {
					$url = Url::to(['/pipe/pipe/create', 'EditId' => $model->id]);
					if(!empty($_GET)){
						$urlParams = Yii::$app->general->getFilters();
						$urlParams[] = '/pipe/pipe/create';
						$urlParams['EditId'] = $model->id;
						$url = Url::to($urlParams);
					}
					return $this->render('//partials/_clientView', ['url'=>$url, 'created_by' => $model->created_by]);
                        
				},
			],
		],
		[
			'attribute' => 'pipe_number',
			'headerOptions' => ['style' => 'width: 10%'],
		],
		[
			'attribute' => 'wall_thikness',
			'headerOptions' => ['style' => 'width: 10%'],
		],
		[
			'attribute' => 'yeild_strength',
			'headerOptions' => ['style' => 'width: 10%'],
		],
		[
			'attribute' => 'heat_number',
			'headerOptions' => ['style' => 'width: 10%'],
		],
		[
			'attribute' => 'od',
			'headerOptions' => ['style' => 'width: 10%'],
		],
		[
			'attribute' => 'length',
			'headerOptions' => ['style' => 'width: 10%'],
		],
		[
			'attribute' => 'pups',
			'label' => Yii::$app->trans->getTrans('Pups'),
			'headerOptions' => ['style' => 'width: 10%'],
			'filter' => ['1' => 'Yes', '0' => 'No'],
			'value' => function ($model) {
				return $model->pups == 1 ? "Yes" : "No";
			},
		],
	],
]);
?>
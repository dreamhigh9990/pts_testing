<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Taxonomy;
use app\models\TaxonomyValue;
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
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    $url = Url::to(['/admin/taxonomy/create','TaxonomyValueSearch[taxonomy_id]'=>$model->taxonomy_id,'EditId' => $model->id]);
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,  ['title' => Yii::t('app', 'lead-update'),]);
                },
            ],
        ],
        [
            'attribute' => 'taxonomy_id',
            'format' => 'raw',
			'filter' => ArrayHelper::map(Taxonomy::find()->where(['!=','id','4'])->asArray()->all(), 'id', 'name'),
            'value' => function($dataProvider){
                $taxonomyName = Taxonomy::find()->select('name')->where(['id'=>$dataProvider->taxonomy_id])->asArray()->one();
                return $taxonomyName['name'];
            }
        ],
        [
            'attribute' => 'project_id',
            'format' => 'raw',
            'visible' => $searchModel->taxonomy_id == 2 ? true:false,
			'filter' => ArrayHelper::map(TaxonomyValue::find()->where(['=','taxonomy_id','4'])->asArray()->all(), 'id', 'value'),
            'value' => function($dataProvider){
                $taxonomyName = TaxonomyValue::find()->select('value')->where(['id'=>$dataProvider->project_id])->asArray()->one();
                return $taxonomyName['value'];
            }
        ],     
        [
            'attribute' => 'value',
            'filter'=> false
        ],
        [
            'attribute' => 'taxonomy_id',
            'label' => Yii::$app->trans->getTrans('WPS'),
            'format' => 'raw',
            'visible' => $searchModel->taxonomy_id == 7 ? true:false,
            'filter'=> false,
			//'filter' => ArrayHelper::map(TaxonomyValue::find()->where(['=','taxonomy_id','6'])->asArray()->all(), 'id', 'value'),
            'value' => function($dataProvider){
                $TaxonomyValueValue = \app\models\TaxonomyValueValue::find()->select(['taxonomy_value.value'])->leftJoin('taxonomy_value','taxonomy_value_value.child_id=taxonomy_value.id')->where(['taxonomy_value_value.parent_id'=>$dataProvider->id])->asArray()->all();
                $html = "";
                if(!empty($TaxonomyValueValue)){
                    foreach($TaxonomyValueValue as $ele){
                        $html.= '<span class="badge badge-info">'.$ele['value'].'</span> ';
                    }
                }
                return $html;
            }
        ],
        
    ],
]); ?>
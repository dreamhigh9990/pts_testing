<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
?>

<!-- <div class="table-responsive"> -->
    <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            "filterSelector" => "select[name='per-page']",
            'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
            'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
            'emptyText' => Yii::$app->trans->getTrans('No results found.'),
            'columns' => [
                ['class' => 'yii\grid\CheckboxColumn'],
                [
                    'attribute' => 'id',
                    'label' => Yii::$app->trans->getTrans("Make Active"),
                    'filter' => false,
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<button class="btn btn-sm btn-success make-active" Id="'.$model->id.'" Model="'.$model::ClassName().'">'.Yii::$app->trans->getTrans("Make Active").'</button>';
                    },
                ], 
                'why_anomally',           
                'pipe_number',
                'wall_thikness',
                'weight',
                'heat_number',
                'yeild_strength',
                'length',
                'od',
                'coating_type',
                'plate_number',
                'ship_out_number',
                'vessel',
                'hfb',
                'mto_number',
                'mto_certificate',
                'mill',
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
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{update}',
                    'buttons' => [                                         
                        'update' => function ($url, $model) {
                            $url = Url::to(['/pipe/pipe/create', 'EditId' => $model->id]);
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url,  [
                                'title' => Yii::t('app', 'View'),
                                'target'=>'_blank',
                                'data-pjax'=>'0'
                            ]);
                        },           
                    ],
                ],
            ], 
    ]); ?>
<!-- </div> -->
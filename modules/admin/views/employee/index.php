<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
$userType = !empty(Yii::$app->user->identity->type) ? Yii::$app->user->identity->type : '';
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
        [
            'class' => 'yii\grid\CheckboxColumn',
            'content' => function($model) use ($userType) {
                if($userType == 'QA Manager' && $model->type == 'Admin'){
                    return '';
                } else {
                    return '<input type="checkbox" name="selection[]" value="'.$model->id.'">';
                }
            },
        ],
        ['class' => 'yii\grid\ActionColumn',
            'template'=>'{update}',
            'buttons' => [                            
                'update' => function ($url, $model) use ($userType) {
                    if($userType == 'QA Manager' && $model->type == 'Admin'){
                        return '';
                    } else {
                        $url = Url::to(['/admin/employee/create', 'EditId' => $model->id]);
                        if(!empty($_GET)){
                            $urlParams = Yii::$app->general->getFilters();
                            $urlParams[] = '/admin/employee/create';
                            $urlParams['EditId'] = $model->id;
                            $url = Url::to($urlParams);
                        }
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,  ['title' => Yii::t('app', 'lead-update'),]);
                    }
                },             
            ],
        ],
        'username',
        'fullname',
        [
            'attribute' => 'type',
            'filter' => Html::activeDropDownList($searchModel,'type',[ 'Safety' => 'Safety','Admin' => 'Admin', 'Client' => 'Client', 'Inspector' => 'Inspector', 'QA Manager' => 'QA Manager', ], ['prompt' => 'Select Type','class'=>'form-control']),				
        ],
        'email:email',
        'phone',
        //'created_at:datetime',
        [
            'attribute' => 'created_at',
            'filter' => false,
            'value' => function($data){
                if($data->created_at != 0 && $data->created_at != ""){
                    return date('M d, Y h:i:s A',$data->created_at);
                } else {
                    return 'N/A';
                }
            }
        ],
        
    ],
]); 
?>
<!-- </div> -->
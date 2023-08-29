<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
$this->title = "Sync log";
?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
        <div class="col-xl-12 col-lg-12 col-12 ">
            <div class="card-body card"> 
                <div class="card-header">
                    <h4 class="card-title mb-0">Sync Log</h4>
                </div>
                <div class="table-responsive long">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\CheckboxColumn'],
                            [
                                'attribute' => 'status',
                                'label' => "Status",
                                'filter' =>['Success'=>'Success','Error'=>'Error'],
                            ],
                            'access_token',
                            'error:ntext',
                            [
                                'attribute' => 'project_id',
                                'label' => "Project",
                                'filter' =>Yii::$app->general->TaxonomyDrop("4",true),
                                'value' => function ($model) {
                                            $List = Yii::$app->general->TaxonomyDrop("4",true);
                                            return !empty($List[$model->project_id])?$List[$model->project_id]:"";
                                     },
                            ],
                            [
                                'attribute' => 'user_id',
                                'label' => "User",
                                'filter' =>Yii::$app->general->employeeList(""),
                                'value' => function ($model) {
                                            $List = Yii::$app->general->employeeList("");	
                                            return !empty($List[$model->user_id])?$List[$model->user_id]:"";
                                     },
                            ],
                            'date',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template'=>'{view}{delete}'
                            ],
                        ],
                    ]); ?>
                </div>
        </div> 
    </div>     
</div>
<?php Pjax::end(); ?>
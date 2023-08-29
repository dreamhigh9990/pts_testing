<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Plant Dashboard";
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
$vehicleFilterArray = [
    'issues_present' => 'Issue Present',
    'service_due' => 'Service Due',
    'overdue_inspection' => 'Overdue Inspection',
    'today_inspection' => 'Upcoming Inspection (Today)'
];
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<section id="basic-tabs-components">
    <div class="row match-height">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <?= Yii::$app->trans->getTrans('Plant Dashboard'); ?>
                        <a href ="<?= \yii\helpers\Url::current(['download' =>1]); ?>" data-pjax="0" target="_blank" >
                            <button class ="btn btn-raised btn-white btn-min-width mr-1 mb-1 black pull-right"><?= Yii::$app->trans->getTrans('Export to XLS'); ?></button>
                        </a>
                    </h4>
                </div>            
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="list-filter-options">
                                <?php $form = ActiveForm::begin([
                                    'id' => 'plant-dashboard-form',
                                    'action' => ['dashboard'],
                                    'method' => 'get',
                                    'options' => [
                                        'data-pjax' => 1
                                    ],
                                ]); ?>
                                    <span>
                                        <?= $form->field($searchModel, 'vehicle_filter')->dropDownList($vehicleFilterArray, ['prompt' => 'All']); ?>
                                    </span>
                                    <span>
                                        <?= Html::submitButton(Yii::t('app', '<i class="fa fa-search"></i> '.Yii::$app->trans->getTrans('Filter List')), ['class' => 'btn btn-outline-default']) ?>
                                        <?= Html::a('<i class="fa fa-filter"></i> '.Yii::$app->trans->getTrans('Clear Filter'),'dashboard',['class'=>'btn btn-raised btn-outline-info btn-min-width']); ?>
                                    </span>
                                    <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <button type="button" url="<?= '/pipe/default/signed-off?model=app\models\VehicleInspection'; ?>" class="pull-right mr-1 mb-1 btn btn-raised btn-outline-warning btn-min-width signed-selected">
                                <i class="fa fa-check"></i> <?= Yii::$app->trans->getTrans('Sign Off'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4><?= Yii::$app->trans->getTrans('Vehicle Inspection List'); ?></h4>
                            <hr>
                        </div>
                    </div>
                    
                    <?php
                    //For Inspection Records
                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        "filterSelector" => "select[name='per-page']",
                        'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
                        'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
                        'emptyText' => Yii::$app->trans->getTrans('No results found.'),
                        'columns' => [
                            ['class' => 'yii\grid\CheckboxColumn'],
                            [
                                'attribute' => 'vehicle_id',
                                'label' => Yii::$app->trans->getTrans("Schedule Record"),
                                'filter' => false,
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $getVehicleNumber = \app\models\VehicleSchedule::find()->select('report_number')->where(['id' => $model->vehicle_id])->active()->asArray()->one();
                                    return !empty($getVehicleNumber) ? Html::a(Html::encode($getVehicleNumber['report_number']), ['/vehicle/schedule/create','EditId' => $model->vehicle_id]) : '';
                                },
                            ],
                            [
                                'attribute' => 'vehicle_id',
                                'label' => Yii::$app->trans->getTrans("Vehicle Number"),
                                'filter' => false,
                                'value' => function ($model) {
                                    $getVehicleNumber = \app\models\VehicleSchedule::find()->select('vehicle_number')->where(['id' => $model->vehicle_id])->active()->asArray()->one();
                                    return !empty($getVehicleNumber) ? $getVehicleNumber['vehicle_number'] : '';
                                },
                            ],
                            [
                                'attribute' => 'report_number',
                                'label' => Yii::$app->trans->getTrans("Inspection Record"),
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::a(Html::encode($model->report_number), ['/vehicle/inspection/create','EditId' => $model->id]);
                                },
                            ],
                            [
                                'attribute' => 'date',
                                'label' => Yii::$app->trans->getTrans("Inspection Date"),
                                'format' => 'raw',
                                'filter' => false,
                                'value' => function ($model) {
                                    $date = '';
                                    if(!empty($model->date)){
                                        if(strtotime(date('Y-m-d', strtotime("-1 days"))) == strtotime($model->date)){
                                            $date = 'Today - '.date('d/m/Y', strtotime($model->date));
                                        } else if((strtotime(date('Y-m-d')) != strtotime($model->date)) && (strtotime(date('Y-m-d', strtotime("-1 days"))) != strtotime($model->date))){
                                            $date = 'Overdue - '.date('d/m/Y', strtotime($model->date));
                                        }
                                    }
                                    return $date;
                                },
                            ],
                            [
                                'attribute' => 'service_due',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'id',
                                'label' => Yii::$app->trans->getTrans("Status"),
                                'filter' => false,
                                'value' => function($model){
                                    $status = [];
                                    if($model->service_due == 'Yes'){
                                        $status[] = 'Service Due';
                                    }
                                    
                                    $getMapDetails = \app\models\MapPartVehicleInspection::find()->select('inspection_id')->where(['inspection_id' => $model->id, 'status' => 'Needs Attention'])->asArray()->all();
                                    if(!empty($getMapDetails)){
                                        $status[] = 'Issues Present';
                                    }

                                    if(strtotime(date('Y-m-d', strtotime("-1 days"))) == strtotime($model->date)){
                                        $status[] = 'Upcoming Inspection (Today)';
                                    } else if((strtotime(date('Y-m-d')) != strtotime($model->date)) && (strtotime(date('Y-m-d', strtotime("-1 days"))) != strtotime($model->date))){
                                        $status[] = 'Overdue Inspection';
                                    }

                                    return !empty($status) ? implode(', ', $status) : '-';
                                },
                            ],
                            'odometer_reading',
                            [
                                'attribute' => 'created_by',
                                'label' => Yii::$app->trans->getTrans("User"),
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
                                'filter' => ['Yes' => 'Yes', 'No' => 'No']
                            ],
                        ],
                    ]); ?>

                    <div class="row">
                        <div class="col-md-12">
                            <h4><?= Yii::$app->trans->getTrans('Newly Added Schedule Vehicles'); ?></h4>
                            <hr>
                        </div>
                    </div>

                    <?php
                    //For Schedule Records
                    echo GridView::widget([
                        'dataProvider' => $dataProviderSchedule,
                        'filterModel' => $searchModelSchedule,
                        "filterSelector" => "select[name='per-page']",
                        'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
                        'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
                        'emptyText' => Yii::$app->trans->getTrans('No results found.'),
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'report_number',
                                'label' => Yii::$app->trans->getTrans("Schedule Record"),
                                'format' => 'raw',
                                'value' => function ($model) {                                    
                                    return Html::a(Html::encode($model->report_number), ['/vehicle/schedule/create','EditId' => $model->id]);
                                },
                            ],
                            [
                                'attribute' => 'date',
                                'format' => 'raw',
                                'filter' => false,
                                'value' => function ($model) {
                                    $date = '';
                                    if(!empty($model->date)){
                                        $date = date('d/m/Y', strtotime($model->date));
                                    }
                                    return $date;
                                },
                            ],
                            [
                                'attribute' => 'vehicle_number',
                                'label' => Yii::$app->trans->getTrans("Vehicle Number"),
                            ],
                            [
                                'attribute' => 'created_by',
                                'label' => Yii::$app->trans->getTrans("User"),
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
                                'filter' => ['Yes' => 'Yes', 'No' => 'No']
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php Pjax::end(); ?>
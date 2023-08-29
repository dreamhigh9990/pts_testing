<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Heat Report";
$view='';   
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<!-- <div class="col-md-12"> -->
    <section id="basic-tabs-components">   
        <div class="row match-height">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                         <h4 class="card-title"><?= Yii::$app->trans->getTrans('Heat Report'); ?>
                            <?= Yii::$app->general->generateReport();?>
                         </h4>
                    </div>            
                    <div class="card-body">
                        <!-- <div class="table-responsive long" > -->
                            <?php
                            echo GridView::widget([
                                    'dataProvider' => $dataProvider,
                                    'filterModel' => $searchModel,
                                        "filterSelector" => "select[name='per-page']",
                                        'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
                                        'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
                                        'emptyText' => Yii::$app->trans->getTrans('No results found.'),
                                        'columns' => [  
                                            'heat_number',     
                                            'pipe_number',   
                                            'wall_thikness',
                                            'weight',
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
                                                'attribute' => 'pups',
                                                'label' => Yii::$app->trans->getTrans("Is Pups"),
                                                'filter' =>['1'=>'Yes','0'=>'No'],
                                                'value' => function ($model) {
                                                        
                                                            return $model->pups==1?"Yes":"No";
                                                    },
                                            ],
                                            [
                                                'attribute' => 'created_by',
                                                'label' => Yii::$app->trans->getTrans("User"),
                                                'filter' =>Yii::$app->general->employeeList(""),
                                                'value' => function ($model) {
                                                            $List = Yii::$app->general->employeeList("");	
                                                            return !empty($List[$model->created_by])?$List[$model->created_by]:"";
                                                    },
                                            ],
                                        
                                        ],
                            ]); ?>
                        </div>
                    </div>
                </div>
            <!-- </div> -->
        </div>
    </section>
<!-- </div> -->
<?php Pjax::end(); ?>
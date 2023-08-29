<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = "Report";
$view='';  
 
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="col-md-12">
    <section id="basic-tabs-components">   
    <div class="row match-height">
        <div class="col-xl-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Report List</h4>
            </div>            
            <div class="card-body">
            <div class="card-block">              
                <ul class="nav nav-tabs ">              
                    <li class="<?= $model=="PipeSearch"?"active":"";?>">
                        <?php ($model=="PipeSearch")?  $view="HeatSearch":"";?>
                        <?= Html::a('Heat Report',['/report/report/index','model'=>'PipeSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="Visualprograss"?"active":"";?> ">
                        <?php ($model=="Visualprograss")?  $view="Visualprograss":"";?>
                        <?= Html::a('Visual Progress',['/report/report/index','model'=>'Visualprograss'],['class'=>'nav-link success visual-prograss']);?>
                    </li>
                    <li class="<?= $model=="WeldingSearch"?"active":"";?>">
                        <?php ($model=="WeldingSearch")?  $view="WeldingSearch":"";?>
                        <?= Html::a('Open End Summary',['/report/report/index','model'=>'WeldingSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="Clearance"?"active":"";?>">
                        <?php ($model=="Clearance")?  $view="Clearance":"";?>
                        <?= Html::a('Clearance',['/report/report/index','model'=>'Clearance'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="Reception"?"active":"";?>">
                        <?php ($model=="Reception")?  $view="ReceptionSearch":"";?>
                        <?= Html::a('Review Summary',['/report/report/review-report','model'=>'ReceptionSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="WeldBook"?"active":"";?>">
                        <?php ($model=="WeldBook")?  $view="WeldBook":"";?>
                        <?= Html::a('Weld Book',['/report/report/index','model'=>'WeldBook'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="Welderanalysis"?"active":"";?>">
                        <?php ($model=="Welderanalysis")?  $view="Welderanalysis":"";?>
                        <?= Html::a('Welder Analysis',['/report/report/index','model'=>'Welderanalysis'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="Dailyproduction"?"active":"";?>">
                        <?php ($model=="Dailyproduction")?  $view="Dailyproduction":"";?>
                        <?= Html::a('Daily/ Weekly Production',['/report/report/index','model'=>'Dailyproduction'],['class'=>'nav-link success']);?>
                    </li>
                </ul>
                <div class="tab-content px-1 pt-1">
                 <?php if($view=='HeatSearch' || $view =='WeldingSearch')                 
                 { ?>                 
                    <div role="tabpanel" class="tab-pane active" id="Pipe">
                        <?php
                          
                            $searchModel ='\\app\models\\'.$model;
                            $searchModel = new $searchModel;
                            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                            echo $this->render('_'.$view, [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                            ]);
                        ?>
                 </div> <?php } else if($view=='ReviewSummary'){  ?>
                 <div>                                
                 </div>
               <?php }else{ ?>
                <div role="tabpanel" class="tab-pane active" id="Pipe">
                    <?php 
                   
                        echo $this->render($view);
                    ?>
                </div>
                <?php }?>
                </div>
            </div>
            </div>
        </div>
        </div>
        
    </div>
    </section>
</div>
<?php Pjax::end(); ?>
<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Welding";
//$weldTypes = Yii::$app->general->TaxonomyDrop(10);
$electrodList = $welderList = array();
$lastWeldData = Yii::$app->weld->getLastRecords('weld');
if($model->pipe_number == ""){
    $model->pipe_number   = isset($lastWeldData['next_pipe']) ? $lastWeldData['next_pipe'] : '';
    $model->kp            = isset($lastWeldData['kp']) ? $lastWeldData['kp'] : '';
    $model->geo_location  = isset($lastWeldData['geo_location']) ? $lastWeldData['geo_location'] : '';
   
}

if($model->weld_number == ""){
    $model->weld_number = !empty($lastWeldData['weld_number']) ? $lastWeldData['weld_number']+1 : ''; 
}

if(empty($_GET['EditId']) || $_GET['EditId'] == 0){
    $model->weld_type = !empty($lastWeldData['weld_type']) ? $lastWeldData['weld_type'] : ''; 
    $model->weld_crossing = !empty($lastWeldData['weld_crossing']) ? $lastWeldData['weld_crossing'] : '';
}

if($model->isNewRecord){

$model->WPS = !empty($lastWeldData['WPS']) ? $lastWeldData['WPS'] : ''; 
$model->electrodes = !empty($lastWeldData['electrodes']) ? json_decode($lastWeldData['electrodes'],true) : ''; 
$model->root_os = !empty($lastWeldData['root_os']) ? $lastWeldData['root_os'] : ''; 
$model->root_ts = !empty($lastWeldData['root_ts']) ? $lastWeldData['root_ts'] : ''; 
$model->hot_os = !empty($lastWeldData['hot_os']) ? $lastWeldData['hot_os'] : ''; 
$model->hot_ts = !empty($lastWeldData['hot_ts']) ? $lastWeldData['hot_ts'] : ''; 
$model->fill_os = !empty($lastWeldData['fill_os']) ? $lastWeldData['fill_os'] : ''; 
$model->fill_ts = !empty($lastWeldData['fill_ts']) ? $lastWeldData['fill_ts'] : ''; 
$model->cap_os = !empty($lastWeldData['cap_os']) ? $lastWeldData['cap_os'] : ''; 
$model->cap_ts = !empty($lastWeldData['cap_ts']) ? $lastWeldData['cap_ts'] : ''; 

}
if($model->WPS != ""){
    $electrodList = Yii::$app->weld->getElectrods($model->WPS);
    $welderList = Yii::$app->weld->getWelders($model->WPS);
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
<div class="left-sideDiv bgsm-side left-table">
    <div class="col-xl-5 col-lg-12 col-12 p-r-5">
        <div class="card-body card"> 
            <div class="card-header">
            <h4 class="card-title mb-0">
                        <?= Yii::$app->trans->getTrans('Welding Info'); ?>
                        <?php if(!Yii::$app->general->isAllowed()){ ?>
                            <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                        <?php } ?>
                    </h4>
            </div>  
            <?php
            $model->geo_location = $model->geo_location == "" ? '-25.2744,133.7751':$model->geo_location;

            $form = ActiveForm::begin([
                'id'=>'welding-form',
                'options'=>['autocomplete'=>'off'],
                'fieldConfig' => [
                    'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                ],
            ]);
            if($model->isNewRecord){
                $model->line_type = 'Main Line';
            }
            ?>            
            <?= Yii::$app->general->defautField($model,$form);?> 
            <?=
            $form->field($model, 'line_type',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])
                ->radioList(['Main Line' => Yii::$app->trans->getTrans('Main Line'), 'Tie Line' => Yii::$app->trans->getTrans('Tie Line')] );
            ?>
            <?= $form->field($model, 'kp',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class'=>'form-control weld-kp pull-kp', 'disabled'=> $model->isNewRecord && !Yii::$app->general->isAllowed() ? false : true]) ?>
            <?php
            $disabled = false;

            if(!$model->isNewRecord){
                $disabled = true;
            }
            ?>
            <?= $form->field($model, 'pipe_number')->textInput(['class' => 'form-control auto-pipe-from-stringing','disabled'=> $model->isNewRecord && !Yii::$app->general->isAllowed() ? false : true]) ?>
            <?= $form->field($model, 'next_pipe')->textInput(['class' => 'form-control auto-pipe-from-stringing-next','disabled'=> $model->isNewRecord && !Yii::$app->general->isAllowed() ? false : true]) ?>          
                        
            <div class="col-md-12 clearfix">
                <div class="input-group">
                    <input type="text" class="form-control geo-location put-kp" name="Welding[geo_location]" placeholder="<?= Yii::$app->trans->getTrans('Geo Location'); ?>" aria-describedby="basic-addon4" value="<?=$model->geo_location;?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                    <div class="input-group-append">
                        <span class="input-group-text map-picker-addon-single" id="map-picker-addon-single"><i class="icon-pointer"></i></span>
                    </div>
                </div>
            </div>

            <?= $form->field($model, 'weld_type' ,['template' => '<div class="col-md-6 col-sm-6 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList(Yii::$app->general->TaxonomyDrop(10), ['prompt' => Yii::$app->trans->getTrans('Please Select'), 'class'=>'form-control weld-type','disabled'=>Yii::$app->general->isAllowed()]); ?>
           
            <div class="weld-crossing">
                <?php if($model->weld_type != 'W'){ ?>
                    <div class="form-group field-welding-weld_crossing clearfix">
                        <div class="col-md-6 col-sm-6 clearfix">
                            <label class="control-label" for="welding-weld_crossing"><?= Yii::$app->trans->getTrans('Weld Crossing'); ?></label>
                            <!-- <input type="text" id="welding-weld_crossing" class="form-control" name="Welding[weld_crossing]" value="<?= $model->weld_crossing; ?>" <?= !Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>> -->
                            <input type="text" id="welding-weld_crossing" class="form-control" name="Welding[weld_crossing]" value="<?= $model->weld_crossing; ?>">
                        </div>
                    </div>
                <?php } ?>
            </div>  
            <?= $form->field($model, 'weld_number',['template' => '<div class="col-md-12 col-sm-6 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class' => 'form-control weld-number-check', 'disabled'=> $model->isNewRecord && !Yii::$app->general->isAllowed() ? false : true]) ?>


            <?php
                if(!$model->isNewRecord){               
                   echo  $form->field($model, 'weld_sub_type')->textInput(['disabled' => true]) ;
                }           
           ?>      




            <?= $form->field($model, 'WPS')->dropDownList(Yii::$app->general->TaxonomyDrop(6,true), ['prompt' => Yii::$app->trans->getTrans('Please Select'),'class'=>'form-control change-wps','disabled'=>Yii::$app->general->isAllowed()]); ?>            
            
            <?php if(!Yii::$app->general->isAllowed()){ ?>
                <?= $form->field($model, 'electrodes',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList($electrodList, ['multiple'=>'multiple','class'=>"list-electrods multiple-select2 form-control",'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?php
            } else {
                $model->electrodes = '';
                if(!empty($model->electrodes)){
                    $model->electrodes = json_encode($model->electrodes);
                }
            ?>
                <?= $form->field($model, 'electrodes',['template' => '<div class="col-md-12 col-sm-6 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>true]); ?>
            <?php } ?>

            <?= $form->field($model, 'root_os')->dropDownList($welderList, ['prompt'=>'', 'class'=>'form-control list-welders','disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'root_ts')->dropDownList($welderList, ['prompt'=>'', 'class'=>'form-control list-welders','disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'hot_os')->dropDownList($welderList, ['prompt'=>'', 'class'=>'form-control list-welders','disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'hot_ts')->dropDownList($welderList, ['prompt'=>'', 'class'=>'form-control list-welders','disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'fill_os')->dropDownList($welderList, ['prompt'=>'', 'class'=>'form-control list-welders','disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'fill_ts')->dropDownList($welderList, ['prompt'=>'', 'class'=>'form-control list-welders','disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'cap_os')->dropDownList($welderList, ['prompt'=>'', 'class'=>'form-control list-welders','disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'cap_ts')->dropDownList($welderList, ['prompt'=>'', 'class'=>'form-control list-welders','disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'visual_acceptance')->dropDownList(['Yes'=>'Yes', 'No'=>'No',], ['prompt'=>'','disabled'=>Yii::$app->general->isAllowed()]) ?>
            
            <?= $form->field($model, 'has_been_cut_out')->dropDownList(['Yes' => 'Yes', 'No' => 'No'], ['prompt' => '', 'disabled' => true]) ?>
            
            <?php Yii::$app->general->defautFileField($model,$form,'Welding');?>  
            
            <div class="col-md-12 clearfix">
                <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="col-xl-7 col-lg-12 col-12 p-r-5 p-l-5 middel-hide">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title "><?= Yii::$app->trans->getTrans('Welding Status'); ?></h4>
            </div>
            <?= $this->render('status',['model'=>$model]);?>
        </div>
        <div class="card-body card">
                    <div class="card-header">
                        <h4 class="card-title"><?= Yii::$app->trans->getTrans('Welding Ndt Defects'); ?></h4>
                    </div>
                    <ul class="list-group">
                    <li class="list-group-item">
                                         <b class="float-left"><?= Yii::$app->trans->getTrans('Position'); ?></b>
                                         <b class="float-right"><?= Yii::$app->trans->getTrans('Defects'); ?></b>

                                      </li>
                        <?php  
                            $defects = json_decode($model->ndt_defects,true);
                            if(!empty( $defects )){
                                foreach($defects as $ele){
                                     if(!empty($ele['repaired'])){
                                        continue;
                                     }
                                     $dep = !empty($ele['defect_position'])?$ele['defect_position']:"";
                                    ?>
                                      <li class="list-group-item"> 
                                         <div class="float-left">  <a class="btn btn-sm btn-danger" href="<?php echo Url::to(['/welding/welding/defect-update', 'id' => $model->id,'defectsItem'=>$ele['defects'],'defectsPos'=>$dep]);?>"><i class="fa fa-times"></i></a> <?= $dep;?></div>                                         
                                         <div class="float-right"><?= $ele['defects'];?></div>
                                      </li>
                                    <?php
                                }
                            }
                        ?>
                    </ul> 
     </div>
    </div>
    </div>
    <div class="width-bigSm bgsm-side right-table">  
            <div class="col-xl-12 col-lg-12 col-12 p-l-0">
                <div class="card-body card"> 
                    <div class="card-header">
                        <div class="pipe-listbarIcon">
                            <a href="#" class="add-remove"><i class="fa fa-bars fa-lg"></i></a>
                        </div>
                        <h4 class="card-title"><?= Yii::$app->trans->getTrans('Welding List'); ?></h4>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <?= Yii::$app->general->signOffButton('app\models\Welding');?>
                                <?= Yii::$app->general->gridButton('app\models\Welding');?>
                            </div>
                        </div>
                    </div>
                    <?php
                        $searchModel = new app\models\WeldingSearch();
                        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                
                        echo $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>


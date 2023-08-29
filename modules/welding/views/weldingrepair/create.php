<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Weld Repair";

$lastWeldData = Yii::$app->weld->getLastRecords('weldingrepair');

//auto populate WPS and Welders based on WPS while before weld is done
$weldDetails = array();
if(!empty($lastWeldData)){
$weldDetails = \app\models\Welding::find()->where(['kp' => $lastWeldData['kp'], 'weld_number' => $lastWeldData['weld_number']+1, 'has_been_cut_out' => 'No'])->active()->asArray()->one();
}
if(!empty($weldDetails)){
    $model->wps = $weldDetails['WPS'];
}

if($model->weld_number == ""){
    $model->weld_number = !empty($lastWeldData['weld_number']) ? $lastWeldData['weld_number']+1 : '';
    $model->kp = isset($lastWeldData['kp']) ? $lastWeldData['kp'] : '';

    $weldDetails = \app\models\Welding::find()->where(['kp' => $model->kp, 'weld_number' => $model->weld_number, 'has_been_cut_out' => 'No'])->active()->asArray()->one();
    
    $model->main_weld_id = 0;
    if(!empty($weldDetails)){
        $model->wps = $weldDetails['WPS'];
        $model->main_weld_id = $weldDetails['id'];
    }

} else if($model->weld_number != ""){
    $weldDetails = \app\models\Welding::find()->where(['kp'=>$model->kp, 'weld_number'=>$model->weld_number, 'has_been_cut_out' => 'No'])->active()->asArray()->one();
    if(!empty($weldDetails)){
        $model->wps = $weldDetails['WPS'];
    }
}

$ndtdefact = Yii::$app->general->TaxonomyDrop(9,true);

if(!empty($model->ndt_defact)){
	$model->ndt_defact = json_decode($model->ndt_defact,true);
}

$electrodList = $welderList = array();
if($model->wps != ""){
    $electrodList = Yii::$app->weld->getElectrods($model->wps);
    $welderList = Yii::$app->weld->getWelders($model->wps);
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
     <div class="col-xl-12 col-lg-12 col-12 p-r-5">
        <div class="card-body card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <?= Yii::$app->trans->getTrans('Weld Repair Info'); ?> 
                    <?php if(!Yii::$app->general->isAllowed()){ ?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                    <?php } ?>
                </h4>
            </div>  

            <?php
            $form = ActiveForm::begin([
                'id'=>'weldrepair-form',
                'fieldConfig' => [
                    'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                ],
                'options' => [
                    'data-type' => 'weld_repair',
                    'autocomplete'=>'off'
                ]
            ]);
            ?>            
            <?= Yii::$app->general->weldField($model,$form);?>
            <?= $form->field($model, 'main_weld_id')->hiddenInput(['maxlength' => true, 'class' => 'form-control main-weld-id', 'disabled' => Yii::$app->general->isAllowed()])->label(false); ?>
            <?= $form->field($model, 'excavation',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList(Yii::$app->general->TaxonomyDrop(13), ['prompt'=>'Please Select', 'class'=>'form-control wr-excavation', 'disabled'=>Yii::$app->general->isAllowed()]); ?>
            <div class="weld-type">
            <?php
            if($model->weld_number != ""){
                $weldData = \app\models\Welding::find()->where(['id'=>$model->main_weld_id])
                ->active()->asArray()->one();
                $weldType  = "";
                if(!empty($weldData)){
                    $weldType = !empty($weldData['weld_type']) ? $weldData['weld_type'] : '';
                }
            ?>
                <div class="form-group field-parameter-wps clearfix">
                    <div class="col-md-12 clearfix">
                        <label class="control-label" for="parameter-wps"><?= Yii::$app->trans->getTrans('Weld Type'); ?></label>
                        <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="<?= $weldType; ?>">
                    </div>
                </div>
            <?php } ?>
            </div>  
            <?= $form->field($model, 'wps',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList(Yii::$app->general->TaxonomyDrop(6,true), ['prompt'=>'Please Select','class'=>'form-control change-wps wr-req-fields', 'disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'weld_sub_type')->dropDownList(Yii::$app->general->TaxonomyDrop(11), ['options' => ['W' => ['Selected'=>'selected']],'prompt'=>'Please Select', 'class'=>'form-control wr-req-fields', 'disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'welder')->dropDownList($welderList, ['prompt'=>'Please Select', 'class'=>'form-control list-welders wr-req-fields', 'disabled'=>Yii::$app->general->isAllowed()]); ?>            
            <?= $form->field($model, 'electrodes',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList($electrodList, ['multiple'=>'multiple','class'=>"list-electrods multiple-select2 form-control", 'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <div class="ndt-report">
                <?php
                if($model->ndt_reportnumber != ""){
                    $ndtData = Yii::$app->weld->ndtData($model->weld_number, $model->kp);
                    $weldType  = "";
                    if(!empty($ndtData)){
                        $weldType = !empty($ndtData['weld_type']) ? $ndtData['weld_type'] : '';
                    }
                ?>
                    <div class="form-group field-parameter-wps clearfix">
                        <div class="col-md-12 clearfix">
                            <label class="control-label" for="parameter-wps"><?= Yii::$app->trans->getTrans('NDT REPORT'); ?></label>
                            <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="<?= $weldType; ?>">
                        </div>                        
                    </div>
                <?php } ?>
            </div>
            <?php if($model->isNewRecord){?>
                    <div class="form-group field-parameter-wps clearfix"> 
                        <div class="col-md-6 clearfix" for="reception-transferred"><label><?= Yii::$app->trans->getTrans('Defects'); ?></label></div>
                        <div class="col-md-6 clearfix" for="reception-transferred"><label><?= Yii::$app->trans->getTrans('Position'); ?></label></div>
                    </div>

                    <div class="field-holder">		 
                        <?php     
                        $Welding = \app\models\Welding::find()->where(['weld_number' => $model->weld_number])->active()->one();
                        $d = array();
                        if(!empty($Welding)){
                            $d = json_decode($Welding->ndt_defects,true);
                            Yii::$app->general->ndtfield($d,'disabled');   	
                        }	                 	              
                                        
                        ?>
                    </div>  
            <?php } ?>     
            <?php if(!$model->isNewRecord){?>
                        <div class="form-group field-parameter-wps clearfix"> 
                            <div class="col-md-6 clearfix" for="reception-transferred"><label><?= Yii::$app->trans->getTrans('Defects'); ?></label></div>
                            <div class="col-md-6 clearfix" for="reception-transferred"><label><?= Yii::$app->trans->getTrans('Position'); ?></label></div>
                        </div>
                        <div class="field-holder">		 
                            <?php     
                            
                            if(!empty($model->ndt_defact)){
                                Yii::$app->general->ndtfield($model->ndt_defact,'disabled');   	
                            }	                 	              
                                            
                            ?>
                        </div> 
            <?php } ?>  


            <?= $form->field($model, 'examination',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['rows' => 3, 'class' => 'form-control wr-req-fields', 'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'area')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'size')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            
            <?= $form->field($model, 'repair_examination',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['rows' => 3, 'class' => 'form-control wr-req-fields', 'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?php Yii::$app->general->defautFileField($model,$form,'WeldRepair');?>  
                
            <div class="col-md-12 clearfix">
                <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    </div>
    <div class="width-bigSm bgsm-side right-table">
        <div class="card-body card">
            <div class="card-header">
                <div class="pipe-listbarIcon">
                    <a href="#" class="add-remove"><i class="fa fa-bars fa-lg"></i></a>
                </div>
                <h4 class="card-title "><?= Yii::$app->trans->getTrans('Weld Repair List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Weldingrepair');?>
                        <?= Yii::$app->general->gridButton('app\models\Weldingrepair');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\WeldingrepairSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
                echo $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            ?>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
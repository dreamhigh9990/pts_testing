<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "NDT";

$lastWeldData = Yii::$app->weld->getLastRecords('ndt');

if($model->weld_number == ""){
    $model->weld_number = !empty($lastWeldData['weld_number']) ? $lastWeldData['weld_number']+1 : '';
    $model->kp = isset($lastWeldData['kp']) ? $lastWeldData['kp'] : '';

    $weldData = Yii::$app->weld->getWeldByKpAndWeldNum($model->kp, $model->weld_number);
    $model->main_weld_id = 0;
    if(!empty($weldData)){
        $model->main_weld_id = $weldData['id'];
    }
}


$ndtdefact = Yii::$app->general->TaxonomyDrop(9,true);

if(!empty($model->ndt_defact)){
	$model->ndt_defact = json_decode($model->ndt_defact,true);
}

?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
     <div class="col-xl-12 col-lg-12 col-12 p-r-5">
        <div class="card-body card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <?= Yii::$app->trans->getTrans('NDT Info'); ?>
                    <?php if(!Yii::$app->general->isAllowed()){ ?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                    <?php } ?>
                </h4>
            </div>
            <?php		
            $form = ActiveForm::begin([
                'id'=>'ndt-form',
                'fieldConfig' => [
					'template' => "<div class='col-md-12 col-sm-12 clearfix'>{label}{input}{error}</div>",
                ],
                'options' => [
                    'data-type' => 'ndt',
                    'autocomplete'=>'off'
                ]
            ]);
            ?>
            <?= Yii::$app->general->weldField($model,$form); ?> 
            <?= $form->field($model, 'main_weld_id')->hiddenInput(['maxlength' => true, 'class' => 'form-control main-weld-id', 'disabled' => Yii::$app->general->isAllowed()])->label(false); ?>
            <div class="weld-type">
                <?php
                if($model->weld_number != ""){
                    // $weldData = Yii::$app->weld->weldingData($model->weld_number, $model->kp);
                    $weldType = $weldSubType = "";
                    $weldData = \app\models\Welding::find()->where(['id'=>$model->main_weld_id])
                    ->active()->asArray()->one();
                    if(!empty($weldData)){
                        $weldType = !empty($weldData['weld_type']) ? $weldData['weld_type'] : '';
                        $weldSubType = !empty($weldData['weld_sub_type']) ? $weldData['weld_sub_type'] : '';
                    }  
                ?>
                <div class="form-group field-parameter-wps clearfix">
                    <div class="col-md-6 col-sm-6 clearfix">
                        <label class="control-label" for="parameter-wps"><?= Yii::$app->trans->getTrans('Weld Type'); ?></label>
                        <input type="text" disabled id="weld_type" class="form-control" name="Parameter[wps]" value="<?= $weldType; ?>">
                    </div>
                    <div class="col-md-6 col-sm-6 clearfix">
                        <label class="control-label"><?= Yii::$app->trans->getTrans('Weld Sub Type'); ?></label>
                        <input type="text" disabled id="weld_sub_type" class="form-control" name="Parameter[wps]" value="<?= $weldSubType; ?>">
                    </div>
                </div>
                <?php } ?>
            </div>
             <div class="form-group field-parameter-wps clearfix"> 
                <div class="col-md-6 clearfix" for="reception-transferred"><label><?= Yii::$app->trans->getTrans('Defects'); ?></label></div>
                <div class="col-md-6 clearfix" for="reception-transferred"><label><?= Yii::$app->trans->getTrans('Position'); ?></label></div>
            </div>
           <div class="field-holder">		 
                 <?php    
                 if(!$model->isNewRecord){ 
                    $Welding = \app\models\Welding::find()->where(['weld_number' => $model->weld_number])->active()->one();
                    $d = array();
                    if(!empty($Welding)){
                        $d = json_decode($Welding->ndt_defects,true);
                    }
                    Yii::$app->general->ndtfield($model->ndt_defact,'',1);  
                    
                }
                 ?>
            </div>
            <div class="col-md-12 clearfix" style="margin:10px 0">
                <button class="btn addFieldNdt btn-sm btn-success m-t-10" <?= Yii::$app->general->isAllowed() ? 'disabled' : '';?>><?= Yii::$app->trans->getTrans('Add Defect'); ?></button>
    	    </div>

 <!-- <?php if($model->isNewRecord){?>
                    <div class="form-group field-parameter-wps clearfix"> 
                        <div class="col-md-6 clearfix" for="reception-transferred"><label>Defects</label></div>
                        <div class="col-md-6 clearfix" for="reception-transferred"><label>Position</label></div>
                    </div>

                    <div class="field-holder">		 
                        <?php     
                        // $Welding = \app\models\Welding::find()->where(['weld_number' => $model->weld_number])->active()->one();
                        // $d = array();
                        // if(!empty($Welding)){
                        //     $d = json_decode($Welding->ndt_defects,true);
                        //     Yii::$app->general->ndtfield($d,'disabled');   	
                        // }	                 	              
                                        
                        ?>
                    </div>  
            <?php } ?>     
            <?php if(!$model->isNewRecord){?>
                        <div class="form-group field-parameter-wps clearfix"> 
                            <div class="col-md-6 clearfix" for="reception-transferred"><label>Defects</label></div>
                            <div class="col-md-6 clearfix" for="reception-transferred"><label>Position</label></div>
                        </div>
                        <div class="field-holder">		 
                            <?php     
                            
                            // if(!empty($model->ndt_defact)){
                            //     Yii::$app->general->ndtfield($model->ndt_defact);   	
                            // }	                 	              
                                            
                            ?>
                        </div> 
            <?php } ?>   -->



            


            <?= $form->field($model, 'outcome')->dropDownList([ 'Accepted' => 'Accepted', 'Rejected' => 'Rejected'], ['prompt' => 'Please Select', 'class'=>'form-control change-ndt-outcome','disabled'=>$model->isNewRecord && !Yii::$app->general->isAllowed() ? false : true]) ?>            
            <?php Yii::$app->general->defautFileField($model,$form,'Ndt');?>  
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
                <h4 class="card-title "><?= Yii::$app->trans->getTrans('NDT List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->ndtImport(); ?>
                        <?= Yii::$app->general->signOffButton('app\models\Ndt');?>
                        <?= Yii::$app->general->gridButton('app\models\Ndt');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\NdtSearch();
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
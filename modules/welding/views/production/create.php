<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Coating Production";

$batchNumberA = Yii::$app->general->TaxonomyDrop(31);
$batchNumberB = Yii::$app->general->TaxonomyDrop(32);

$lastWeldData = Yii::$app->weld->getLastRecords('production');

if($model->weld_number == ""){
    $model->weld_number = !empty($lastWeldData['weld_number']) ? $lastWeldData['weld_number']+1 : '';
    $model->kp = isset($lastWeldData['kp']) ? $lastWeldData['kp'] : '';

    $weldData = Yii::$app->weld->getWeldByKpAndWeldNum($model->kp, $model->weld_number);
    $model->main_weld_id = 0;
    if(!empty($weldData)){
        $model->main_weld_id = $weldData['id'];
    }
}
if($model->isNewRecord){
    $model->dew_point = !empty($lastWeldData['dew_point']) ? $lastWeldData['dew_point']: '';
    $model->temperature = !empty($lastWeldData['temperature']) ? $lastWeldData['temperature']: '';
    $model->substrate_temprature = !empty($lastWeldData['substrate_temprature']) ? $lastWeldData['substrate_temprature']: '';
    $model->humidity = !empty($lastWeldData['humidity']) ? $lastWeldData['humidity']: '';
    $model->abrasive_material = !empty($lastWeldData['abrasive_material']) ? $lastWeldData['abrasive_material']: '';
    $model->material_batch_number = !empty($lastWeldData['material_batch_number']) ? $lastWeldData['material_batch_number']: '';
    $model->surface_profile = !empty($lastWeldData['surface_profile']) ? $lastWeldData['surface_profile']: '';
    $model->batch_number_a = !empty($lastWeldData['batch_number_a']) ? $lastWeldData['batch_number_a']: '';
    $model->batch_number_b = !empty($lastWeldData['batch_number_b']) ? $lastWeldData['batch_number_b']: '';
}


$checkPointsArray = Yii::$app->general->TaxonomyDrop(23);
if(!empty($model->checkpoint)){
    // $model->checkpoint = json_encode($model->checkpoint);
    $model->checkpoint = Yii::$app->general->makeJsonDecode($model->checkpoint);
}


?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
    <div class="col-xl-12 col-lg-12 col-12 p-r-5">
        <div class="card-body card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <?= Yii::$app->trans->getTrans('Coating Production Info'); ?>
                    <?php if(!Yii::$app->general->isAllowed()){ ?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                    <?php } ?>
                </h4>
            </div>
            <?php			
            $form = ActiveForm::begin([
                'id'=>'production-form',
                'fieldConfig' => [
					'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                ],
                'options' => [
                    'data-type' => 'production',
                    'autocomplete'=>'off'
                ]
            ]);
            ?>
            <?= Yii::$app->general->weldField($model,$form);?> 
            <?= $form->field($model, 'main_weld_id', ['template' => "<div class='col-md-12 col-sm-12 clearfix'>{label}{input}{error}</div>",])->hiddenInput(['maxlength' => true, 'class' => 'form-control main-weld-id', 'disabled' => Yii::$app->general->isAllowed()])->label(false); ?>
            <div class="weld-type">
            <?php
            if($model->weld_number != ""){
                $weldData = Yii::$app->weld->weldingData($model->weld_number, $model->kp);
                $weldType = $weldSubType = "";
                if(!empty($weldData)){
                    $weldType = !empty($weldData['weld_type']) ? $weldData['weld_type'] : '';
                    $weldSubType = !empty($weldData['weld_sub_type']) ? $weldData['weld_sub_type'] : '';
                }
            ?>
                <div class="form-group field-production-wps clearfix">
                    <div class="col-md-6 col-sm-6 clearfix">
                        <label class="control-label" for="production-wps"><?= Yii::$app->trans->getTrans('Weld Type'); ?></label>
                        <input type="text" disabled id="weld_type" class="form-control" name="Production[weld_type]" value="<?= $weldType; ?>">
                    </div>
                    <div class="col-md-6 col-sm-6 clearfix">
                        <label class="control-label" for="production-wps"><?= Yii::$app->trans->getTrans('Weld Sub Type'); ?></label>
                        <input type="text" disabled id="weld_sub_type" class="form-control" name="Production[weld_sub_type]" value="<?= $weldSubType; ?>">
                    </div>
                </div>
            <?php } ?>
            </div>
            <?= $form->field($model, 'temperature')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'substrate_temprature')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'humidity')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'dew_point')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'abrasive_material', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['rows' => 6,'class'=>'form-control','style'=>'resize:none;','disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'material_batch_number', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['maxlength' => true,'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'surface_profile', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>

            <?= $form->field($model, 'batch_number_a')->dropDownList($batchNumberA, ['prompt' => Yii::$app->trans->getTrans('Please Select'),'disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'batch_number_b')->dropDownList($batchNumberB, ['prompt' => Yii::$app->trans->getTrans('Please Select'),'disabled'=>Yii::$app->general->isAllowed()]); ?>
            
            <?= $form->field($model, 'steel_adhesion', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['rows' => 6,'class'=>'form-control','style'=>'resize:none;','disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'fbe_adhesion', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['rows' => 6,'class'=>'form-control','style'=>'resize:none;','disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'salt_testing', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'dft', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'dft_2', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'dft_3', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'dft_4', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'dft_5', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'dft_6', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            
            <?php if(!Yii::$app->general->isAllowed()){ ?>
            <?= $form->field($model, 'checkpoint', ['template' => '<div class="col-md-12 clearfix check_boxes">{label}{input}{error}{hint}</div>'])->checkboxList($checkPointsArray); ?>            
            <?php } else {
                if(!empty($model->checkpoint)){
                    $model->checkpoint = json_encode($model->checkpoint);
            ?>
                <?= $form->field($model, 'checkpoint', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['disabled'=>true,'rows'=>10]); ?>            
            <?php
                }
            }
            ?>
            
            <?= $form->field($model, 'outcome',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList([ 'Accepted' => 'Accepted', 'Rejected' => 'Rejected'], ['prompt' => Yii::$app->trans->getTrans('Please Select'), 'class'=>'form-control change-ndt-outcome','disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?php Yii::$app->general->defautFileField($model,$form,'Production');?>  
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
                <h4 class="card-title "><?= Yii::$app->trans->getTrans('Coating Production List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Production');?>
                        <?= Yii::$app->general->gridButton('app\models\Production');?>
                        <?= Yii::$app->export->generateExcelExportButton(); ?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\ProductionSearch();
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
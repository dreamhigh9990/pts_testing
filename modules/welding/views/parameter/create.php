<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Parameter Check";

$lastWeldData = Yii::$app->weld->getLastRecords('parameter');

$defaultWps = '';
$welderArray = array();
//auto populate WPS and Welders based on WPS while before weld is done
$weldDetails = array();
if(!empty($lastWeldData)){
    $weldDetails = \app\models\Welding::find()->where(['kp'=>$lastWeldData['kp'], 'weld_number'=>$lastWeldData['weld_number']+1])->active()->asArray()->one();
}


if(!empty($weldDetails)){
    $details = Yii::$app->general->getTaxomonyData($weldDetails['WPS']);
    if(!empty($details)){
        $defaultWps = $details['value'];
        $welderArray = Yii::$app->weld->getWelders($weldDetails['WPS']);
    }
}

if($model->weld_number == ""){
    $model->weld_number = !empty($lastWeldData['weld_number']) ? $lastWeldData['weld_number']+1 : '';
    $model->kp = isset($lastWeldData['kp']) ? $lastWeldData['kp'] : '';    
} else if($model->weld_number != "") {
    $weldDetails = \app\models\Welding::find()->where(['kp'=>$model->kp, 'weld_number'=>$model->weld_number])->active()->asArray()->one();

    $defaultWps = '';
    if(!empty($weldDetails)){
        $details = Yii::$app->general->getTaxomonyData($weldDetails['WPS']);

        if(!empty($details)){
            $defaultWps = $details['value'];
            $welderArray = Yii::$app->weld->getWelders($weldDetails['WPS']);
        }
    }
}

//! as per client says, show all welders in dropdown
$welderArray = Yii::$app->general->TaxonomyDrop(7);
?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
     <div class="col-xl-12 col-lg-12 col-12 p-r-5">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <?= Yii::$app->trans->getTrans('Parameter Check Info'); ?>
                    <?php if(!Yii::$app->general->isAllowed()){ ?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                    <?php } ?>
                </h4>
            </div>
            <?php
            $form = ActiveForm::begin([
                'id'=>'parameter-form',
                'options'=>['autocomplete'=>'off'],
                'fieldConfig' => [
					'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
				],
            ]);
            ?>
            <?= Yii::$app->general->weldField($model,$form);?> 
            <div class="weld-wps">
                <?php if($defaultWps != ""){ ?>
                <div class="form-group field-parameter-wps clearfix">
                    <div class="col-md-12 clearfix">
                        <label class="control-label" for="parameter-wps">WPS</label>
                        <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="<?= $defaultWps; ?>">
                    </div>
                </div>
                <?php } ?>
            </div>
            <?= $form->field($model, 'welder', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList($welderArray, ['prompt'=>'Please Select','class'=>'form-control wps-welder','disabled'=>Yii::$app->general->isAllowed()]); ?>
            <?= $form->field($model, 'pass_number')->textInput(['maxlength' => true,'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'preheat')->textInput(['maxlength' => true,'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'amps')->textInput(['class' => 'form-control calc-heat parameter-amps', 'maxlength' => true,'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'volt')->textInput(['class' => 'form-control calc-heat parameter-volt', 'maxlength' => true,'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'rot')->textInput(['class' => 'form-control calc-travel parameter-rot', 'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'rol')->textInput(['class' => 'form-control calc-travel parameter-rol', 'maxlength' => true,'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'travel', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class' => 'form-control calc-heat parameter-travel calc-travel-result', 'maxlength' => true, 'readonly' => true, 'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'heat_input', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class' => 'form-control calc-heat-result', 'readonly' => true, 'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'interpass_temperature')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'gas_flow')->textInput(['maxlength' => true,'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'wire_speed')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'k_factor')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?php //echo $form->field($model, 'hit')->textInput(['maxlength' => true,'disabled'=>Yii::$app->general->isAllowed()]) ?>
            <?php Yii::$app->general->defautFileField($model,$form,'Parameter');?>  
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
                <h4 class="card-title "><?= Yii::$app->trans->getTrans('Parameter Check List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Parameter');?>
                        <?= Yii::$app->general->gridButton('app\models\Parameter');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\ParameterSearch();
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
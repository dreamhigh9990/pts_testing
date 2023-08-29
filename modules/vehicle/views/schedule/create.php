<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\models\VehicleSchedule;
use app\models\VehicleScheduleSearch;

/* @var $this yii\web\View */
/* @var $model app\models\VehicleSchedule */

$this->title = Yii::t('app', 'Vehicle Schedule');

$getPartList = Yii::$app->general->TaxonomyDrop(30, true);
$getPartList = array('0' => 'Please Select') + $getPartList;
if(!empty($clone)){
    $model->sca_unit_number = '';
    $model->vehicle_number = '';
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-3 col-lg-12 col-12 p-r-0 p-r-15">
            <div class="card-body card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Vehicle Schedule'); ?>
                        <?php if(!Yii::$app->general->isAllowed()){?>
                            <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                        <?php } ?>
                    </h4>
                </div>
                <?php
                $form = ActiveForm::begin([
                    'action' => !empty($clone) ? Url::to(['/vehicle/schedule/create']) : '',
                    'id' => 'vehicle-schedule-form',
                    'fieldConfig' => [
                        'template' => "<div class='col-md-12 col-sm-12 clearfix'>{label}{input}{error}</div>",
                    ],
					'options' => [
                        'autocomplete' => 'off',
                    ]
                ]);
                echo Yii::$app->general->defautField($model, $form);
                ?>
                <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'vehicle_type')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'vehicle_number')->textInput(['class' => 'form-control vehicle-number', 'data-vehicle' => $model->id, 'maxlength' => true, 'disabled' => (!$model->isNewRecord && empty($clone)) ? true : false]) ?>
                <?= $form->field($model, 'in_use')->dropDownList(['Yes' => 'Yes', 'No' => 'No']); ?>
                <div class="form-group clearfix">
					<div class="col-md-12 part-list-container" style="">
						<label class="control-label"><h4><?= Yii::$app->trans->getTrans('Part List'); ?></h4></label>
                        <div class="row selected-parts">
                            <?php
                                $getSelectedParts = \app\models\VehicleSchedule::find()->where(['id' => $model->id])->active()->asArray()->one();                                
                                if(!empty($getSelectedParts)){
                                    $selectedPartsArry = json_decode($getSelectedParts['part_list'], true);
                                    foreach($selectedPartsArry as $selectedPart){
                            ?>
                                    <div class="clearfix">
                                        <div class="col-md-9">
                                            <div class="form-group field-vehicleschedule-part_id">
                                                <div class="col-md-12 col-sm-12 clearfix p-0">
                                                    <select id="vehicleschedule-part_id" class="form-control vehicle-part" name="VehicleSchedule[part_id][]" readonly>
                                                        <?php
                                                        foreach($getPartList as $key => $val){
                                                            $selected = '';
                                                            if($selectedPart['part'] == $key) $selected = 'selected="selected"';
                                                        ?>
                                                        <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-5">
                                            <div class="form-group field-vehicleschedule-barcode">
                                                <div class="col-md-12 col-sm-12 clearfix p-0">
                                                    <input type="text" id="vehicleschedule-barcode" class="form-control part-barcode" name="VehicleSchedule[barcode][]" value="<?php //echo $selectedPart['barcode']; ?>" readonly>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="col-md-3">
                                            <?= Html::button('<i class="fa fa-trash-o"></i>', ['class' => 'btn btn-sm btn-raised btn-outline-danger pull-right btn-remove-part-schedule']) ?>
                                        </div>
                                    </div>
                            <?php
                                    }
                                }
                            ?>
                        </div>
                        <?php if($model->isNewRecord){ ?>
						<div class="row section-part-list">
                            <div class="clearfix part-container">
                                <div class="col-md-9 v-container">
                                    <?= $form->field($model, 'part_id[]', ['template' => "<div class='col-md-12 col-sm-12 clearfix p-0'>{label}{input}{error}</div>"])->dropDownList($getPartList,['class' => 'form-control vehicle-part'])->label(false); ?>
                                </div>
                                <!-- <div class="col-md-3 b-container">
                                    <?php //echo $form->field($model, 'barcode[]', ['template' => "<div class='col-md-12 col-sm-12 clearfix p-0'>{label}{input}{error}</div>"])->textInput(['maxlength' => true, 'class' => 'form-control part-barcode hide', 'placeholder' => 'Barcode'])->label(false); ?>
                                </div> -->
                                <div class="col-md-3">
                                    <?= Html::button('<i class="fa fa-trash-o"></i>', ['class' => 'btn btn-sm btn-raised btn-outline-danger pull-right btn-remove-part-schedule']) ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="row section-new-added-part"></div>
					</div>
                </div>
                <div class="col-md-12 clearfix">
                    <?= Html::button(Yii::t('app', Yii::$app->trans->getTrans('Add Part')), ['class' => 'btn btn-sm btn-raised btn-outline-default pull-right btn-clone-part-schedule']) ?>
                </div>
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Vehicle Schedule List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\VehicleSchedule');?>
                        <?= Yii::$app->general->gridButton('app\models\VehicleSchedule');?>
                        <button type="button" url="<?php echo \yii\helpers\Url::to(['/vehicle/schedule/copy']); ?>" class="mr-1 mb-1 btn btn-raised btn-outline-default btn-min-width btn-copy-selected"><i class="fa fa-copy"></i> <?= Yii::$app->trans->getTrans('Copy Selected'); ?></button>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new VehicleScheduleSearch();
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
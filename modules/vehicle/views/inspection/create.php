<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\models\VehicleInspection;
use app\models\VehicleInspectionSearch;

/* @var $this yii\web\View */
/* @var $model app\models\VehicleInspection */

$this->title = Yii::t('app', 'Vehicle Inspection');
if($model->isNewRecord) $model->geolocation = '-25.2744, 133.7751';
$list = [];
if(!$model->isNewRecord){
    $getInspectionMapList = \app\models\MapPartVehicleInspection::find()->where(['inspection_id' => $model->id])->asArray()->all();
    $allQue = [];
    if(!empty($getInspectionMapList)){
        foreach($getInspectionMapList as $key => $mapList){
            $getPartName = \app\models\TaxonomyValue::find()->where(['id' => $mapList['part_id']])->active()->asArray()->one();
            if(!empty($getPartName)){
                if(!in_array($mapList['part_id'], array_column($list, 'id'))) {
                    $list[$key]['id'] = $mapList['part_id'];
                    $list[$key]['name'] = $getPartName['value'];
                }
                $getPartQue = \app\models\MapPartQuestion::find()->select('question')->where(['id' => $mapList['que_id']])->asArray()->one();
                if(!empty($getPartQue)){
                    $allQue[$mapList['part_id']][$key]['map_id'] = $mapList['id'];
                    $allQue[$mapList['part_id']][$key]['que_id'] = $mapList['que_id'];
                    $allQue[$mapList['part_id']][$key]['question'] = $getPartQue['question'];
                    $allQue[$mapList['part_id']][$key]['status'] = $mapList['status'];
                    $allQue[$mapList['part_id']][$key]['defect_comments'] = $mapList['defect_comments'];
                }
            }
        }
        $list = array_values($list);
    }
    if(!empty($list)){
        foreach($list as $key => $ele){
            $list[$key]['questions'] = !empty($allQue[$ele['id']]) ? array_values($allQue[$ele['id']]) : [];
        }
    }    
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-3 col-lg-12 col-12 p-r-0 p-r-15">
            <div class="card-body card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Vehicle Inspection'); ?>
                        <?php if(!Yii::$app->general->isAllowed()){?>
                            <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                        <?php } ?>
                    </h4>
                </div>
                <?php
                $form = ActiveForm::begin([
                    'id' => 'vehicle-inspection-form',
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
                <?= $form->field($model, 'vehicle_id')->textInput(['class' => 'form-control auto-vehicle-number valid-vehi-number', 'disabled' => (!$model->isNewRecord) ? true : false]); ?>
                <?= $form->field($model, 'service_due')->dropDownList([ 'Yes' => 'Yes', 'No' => 'No', ], ['prompt' => '']) ?>
                <?= $form->field($model, 'geolocation', ['template' => '<div class="col-md-12 clearfix">{label}<div class="input-group">{input}<div class="input-group-append"><span class="input-group-text map-picker-vehicle-inspection" id="map-picker-vehicle-inspection"><i class="icon-pointer"></i></span></div></div>{error}</div>'])->textInput(['maxlength' => true, 'class' => 'form-control geo-location']) ?>
                <?= $form->field($model, 'odometer_reading')->textInput() ?>
                <div class="form-group clearfix">
					<div class="col-md-12 clearfix">
						<label class="control-label"><h4><?= Yii::$app->trans->getTrans('Part List'); ?></h4></label>
                        <div class="row selected-part-list">
                            <?php if(!empty($list)){ ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?= Yii::$app->trans->getTrans('Part'); ?></th>
                                        <th><?= Yii::$app->trans->getTrans('Result'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 0;
                                foreach($list as $element){
                                    $id = $element['id'];
                                    $name = $element['name'];
                                ?>
                                    <tr class="table-active">
                                        <th colspan="2"><?= $name; ?></th>
                                    </tr>
                                    <?php
                                    if(!empty($element['questions'])){
                                        foreach($element['questions'] as $que){
                                            $mapId = $que['map_id'];
                                            $queId = $que['que_id'];
                                            $queName = $que['question'];
                                            $queStatus = $que['status'];
                                            $queDefectComments = $que['defect_comments'];
                                    ?>
                                        <tr>
                                            <td><?= $queName; ?></td>
                                            <td>
                                                <input type="hidden" name="MapPartVehicleInspection[<?= $i; ?>][map_id]" value="<?= $mapId; ?>" />
                                                <select class="form-control part-status" name="MapPartVehicleInspection[<?= $i; ?>][status]">
                                                    <option value="">Please Select</option>
                                                    <option value="Acceptable" <?= $queStatus == 'Acceptable' ? 'selected' : ''; ?>>Acceptable</option>
                                                    <option value="Needs Attention" <?= $queStatus == 'Needs Attention' ? 'selected' : ''; ?>>Needs Attention</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr class="defect-comment" style="display:<?= ($queStatus == 'Acceptable' ? 'none' : 'block'); ?>">
                                            <td colspan="2">
                                                <textarea class="form-control" name="MapPartVehicleInspection[<?= $i; ?>][defect_comment]" placeholder="Defect Comments" style="resize:none;"><?= $queDefectComments; ?></textarea>
                                            </td>
                                        </tr>
                                    <?php
                                        $i++;
                                        }
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 clearfix">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Vehicle Inspection List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\VehicleInspection');?>
                        <?= Yii::$app->general->gridButton('app\models\VehicleInspection');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new VehicleInspectionSearch();
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

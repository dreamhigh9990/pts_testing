<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Coating Repair";

$lastWeldData = Yii::$app->weld->getLastRecords('coatingrepair');

if($model->weld_number == ""){
    $model->weld_number = !empty($lastWeldData['weld_number']) ? $lastWeldData['weld_number']+1 : '';
    $model->kp = isset($lastWeldData['kp']) ? $lastWeldData['kp'] : '';

    $weldData = Yii::$app->weld->getWeldByKpAndWeldNum($model->kp, $model->weld_number);
    $model->main_weld_id = 0;
    if(!empty($weldData)){
        $model->main_weld_id = $weldData['id'];
    }
}

if(!empty($model->checkpoint)){
    // $model->checkpoint = json_encode($model->checkpoint);
    $model->checkpoint = Yii::$app->general->makeJsonDecode($model->checkpoint);
}


$checkPointsArray = Yii::$app->general->TaxonomyDrop(22);
$getBatchNumDropListA = Yii::$app->general->TaxonomyDrop(31);
$getBatchNumDropListB = Yii::$app->general->TaxonomyDrop(32);

?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-5 col-lg-12 col-12 p-r-5">
            <div class="card-body card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <?= Yii::$app->trans->getTrans('Coating Repair Info'); ?> 
                        <?php if(!Yii::$app->general->isAllowed()){ ?>
                            <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                        <?php } ?>
                    </h4>
                </div>
                <?php		
                $form = ActiveForm::begin([
                    'id'=>'coatingrepair-form',
                    'fieldConfig' => [
                        'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                    ],
                    'options' => [
                        'data-type' => 'coating_repair',
                        'autocomplete'=>'off'
                    ]
                ]);
                ?>
                <?= Yii::$app->general->weldField($model,$form);?>           
                <?= $form->field($model, 'main_weld_id', ['template' => "<div class='col-md-12 col-sm-12 clearfix'>{label}{input}{error}</div>"])->hiddenInput(['maxlength' => true, 'class' => 'form-control main-weld-id', 'disabled' => Yii::$app->general->isAllowed()])->label(false); ?>
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
            
                <?= $form->field($model, 'pipe_number', ['template' => '<div class="col-md-12  clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class' => 'form-control cr-pipe-defect cr-pipe', 'disabled'=>Yii::$app->general->isAllowed()]) ?>        
                <?= $form->field($model, 'ambient_temperature')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'substrate_temprature')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'humidity')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'dew_point')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
                <?php //echo $form->field($model, 'coating_product')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'batch_number_a')->dropDownList($getBatchNumDropListA, ['prompt' => 'Select Batch Number', 'disabled'=>Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'batch_number_b')->dropDownList($getBatchNumDropListB, ['prompt' => 'Select Batch Number', 'disabled'=>Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'type_repair', ['template' => "<div class='col-md-12 col-sm-12 clearfix'>{label}{input}{error}</div>"])->dropDownList([ 'Brush' => 'Brush', 'Roller' => 'Roller', 'Spray' => 'Spray', ], ['prompt' => '','disabled'=>Yii::$app->general->isAllowed()]); ?>
                <?php if(!Yii::$app->general->isAllowed()){ ?>
                    <?= $form->field($model, 'checkpoint', ['template' => '<div class="col-md-12 clearfix check_boxes">{label}{input}{error}{hint}</div>'])->checkboxList($checkPointsArray); ?>
                <?php } else {
                    // if(!empty($model->checkpoint)){
                        // $model->checkpoint = json_encode($model->checkpoint);
                        echo $form->field($model, 'checkpoint', ['template' => '<div class="col-md-12 clearfix check_boxes"><h3>{label}</h3>{input}{error}{hint}</div>'])->checkboxList($checkPointsArray, [
                            'item' => function($index, $label, $name, $checked, $value){
                                $disable = true;
                        
                                $checkbox = Html::checkbox($name, $checked, ['value' => $value, 'disabled' => $disable]);
                                return Html::tag('div', Html::label($checkbox . $label), ['class' => 'checkbox']);
                            }
                        ]);
                ?>
                    <?php //echo $form->field($model, 'checkpoint', ['template' => '<div class="col-md-12  clearfix">{label}{input}{error}{hint}</div>'])->textarea(['disabled'=>true,'rows'=>10]); ?>            
                <?php
                    // }
                }
                ?>
            
            
                <?= $form->field($model, 'temperature')->textInput(['disabled'=>Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'time')->textInput(['placeholder'=>'in minute','disabled'=>Yii::$app->general->isAllowed()]) ?>

                <?php Yii::$app->general->defautFileField($model,$form,'CoatingRepair');?>  
                <div class="col-md-12 clearfix">
                    <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <div class="col-xl-7 col-lg-12 col-12 p-r-5 p-l-5 middel-hide">
            <div class="card-body card">
                <div class="card-header">
                    <h4 class="card-title"><?= Yii::$app->trans->getTrans('Pipe Defects'); ?></h4>
                </div>
                <div class="card-body pipe-defect-coating-repair">
                    <?php
                    $pipeNumber = $model->pipe_number;
                    $getPipeDefects = \app\models\Pipe::find()->select(['defects'])->where(['pipe_number' => $pipeNumber])->active()->asArray()->one();
                    $defects = [];
                    if(!empty($getPipeDefects)){
                        $defects = json_decode($getPipeDefects['defects'], true);
                    }
                    ?>
                    <ul class="list-group defect-list">
                        <?php
                        if(!empty($defects)){
                            foreach($defects as $defect){
                        ?>
                            <li class="list-group-item"><b><?= $defect; ?></b></li>
                        <?php
                            }
                        } else {
                        ?>
                            <li class="list-group-item"><?= Yii::$app->trans->getTrans('No defects found.'); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
	<div class="width-bigSm bgsm-side right-table">
        <div class="card-body card">
             <div class="card-header">
                <div class="pipe-listbarIcon">
                    <a href="#" class="add-remove"><i class="fa fa-bars fa-lg"></i></a>
                </div>
                <h4 class="card-title "><?= Yii::$app->trans->getTrans('Coating Repair List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Coatingrepair');?>
                        <?= Yii::$app->general->gridButton('app\models\Coatingrepair');?>
                        <?= Yii::$app->export->generateExcelExportButton(); ?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\CoatingrepairSearch();
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
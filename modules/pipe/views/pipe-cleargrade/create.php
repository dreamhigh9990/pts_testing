<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Taxonomy;
use app\models\TaxonomyValue;
$this->title = "Pipe Clear & Grade";
$getAllLocationList = Yii::$app->general->TaxonomyDrop(2);

$checkPointsArray = Yii::$app->general->TaxonomyDrop(26);
if(!empty($model->check_points)){
	$model->check_points = json_decode($model->check_points,true);
}
?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-5 col-lg-12 col-12 p-r-5">
            <div class="card-body card"> 
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <?= Yii::$app->trans->getTrans('Clear & Grade Info'); ?>
                        <?php if(!Yii::$app->general->isAllowed()){ ?>
                            <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                        <?php } ?>
                    </h4>
                </div>
                <?php
                $form = ActiveForm::begin([
                    'options'=>[
                        'autocomplete'=>'off',
                        'id' => 'pipe-cleargrade-form'
                    ],
                    'fieldConfig' => [
                        'template' => "<div class='col-md-12 clearfix'>{label}{input}{error}</div>",
                    ],
                ]);
                if($model->isNewRecord){
                    $lastWeldData                    = Yii::$app->weld->getLastRecords('cleargrade');               
                    $model->start_kp                 = !empty($lastWeldData['end_kp']) ? $lastWeldData['end_kp'] : '';;
                    $model->start_geo_location       = !empty($lastWeldData['start_geo_location']) ? $lastWeldData['start_geo_location'] : '';
                    $model->end_geo_location         = !empty($lastWeldData['end_geo_location']) ? $lastWeldData['end_geo_location'] : '';;
                    $model->location                 = !empty($lastWeldData['location']) ? $lastWeldData['location'] : '';;
                 }
                ?>
                <input type="hidden" name="sectionType" class="section-type" value="cg"/>
                <?php echo Yii::$app->general->defautField($model,$form);?>
                    
                <?= $form->field($model, 'location')->dropDownList($getAllLocationList, ['prompt' => 'Please Select','class'=>'form-control location-drop','disabled'=>Yii::$app->general->isAllowed()]) ?>
                
                <?= $form->field($model, 'start_kp')->textInput(['class'=>'form-control kp-landowner landowner-from-kp','disabled'=>$model->isNewRecord && !Yii::$app->general->isAllowed()?false:true]) ?>
                <div class="col-md-12 clearfix">
                    <div class="input-group">
                        <input type="text" class="form-control geo-location-twice geo-start" name="Cleargrade[start_geo_location]" placeholder="Start Geo Code" aria-describedby="basic-addon4" value="<?= $model->start_geo_location; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                        <div class="input-group-append">
                            <span class="input-group-text map-picker-addon" id="map-picker-addon"><i class="icon-pointer"></i></span>
                        </div>
                    </div>
                </div>
                
                <?= $form->field($model, 'end_kp')->textInput(['class'=>'form-control kp-landowner landowner-to-kp','disabled'=>$model->isNewRecord && !Yii::$app->general->isAllowed()?false:true]) ?>
                <div class="col-md-12 clearfix">
                    <div class="input-group">
                        <input type="text" class="form-control geo-location-twice geo-end" name="Cleargrade[end_geo_location]" placeholder="End Geo Code" aria-describedby="basic-addon4" value="<?= $model->end_geo_location; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                        <div class="input-group-append">
                            <span class="input-group-text map-picker-addon" id="map-picker-addon"><i class="icon-pointer"></i></span>
                        </div>
                    </div>
                </div>
                <?php if(!Yii::$app->general->isAllowed()){ ?>
                <?= $form->field($model, 'check_points', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->checkboxList($checkPointsArray); ?>            
                <?php } else {
                    if(!empty($model->check_points)){
                        $model->check_points = json_encode($model->check_points);
                ?>
                    <?= $form->field($model, 'check_points', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['disabled'=>true,'rows'=>10]); ?>            
                <?php
                    }
                }
                ?>                
                <?php Yii::$app->general->defautFileField($model,$form,'Cleargrade');?>  
                <div class="col-md-12 clearfix">
                    <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="col-xl-7 col-lg-12 col-12 p-r-5 p-l-5 middel-hide">
            <div class="card-body card"> 
                <div class="card-header">
                    <h4 class="card-title"><?= Yii::$app->trans->getTrans('Landowner Details'); ?></h4>
                </div>
                <div class="landowner-details div-landowner">                    
                    <?php
                    $landOwnerList = \app\models\Landowner::find()->where(['AND',['>=', 'from_kp', $model->start_kp],['<=', 'to_kp', $model->end_kp]])->active()->asArray()->all();
                    if(!empty($landOwnerList)){
                        echo $this->render('landowner',['landownerlist' => $landOwnerList]);
                    } else {
                    ?>
                        <h4 class="text-center"><?= Yii::$app->trans->getTrans('No Landowner Available Now'); ?></h4>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="width-bigSm bgsm-side right-table">
        <div class="col-xl-12 col-lg-12 col-12 p-l-0 p-r-0">
            <div class="card-body card"> 
                <div class="card-header">
                    <div class="pipe-listbarIcon">
                        <a href="#" class="add-remove"><i class="fa fa-bars fa-lg"></i></a>
                    </div>
                    <h4 class="card-title"><?= Yii::$app->trans->getTrans('Clear & Grade List'); ?></h4>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <?= Yii::$app->general->signOffButton('app\models\Cleargrade');?>
                            <?= Yii::$app->general->gridButton('app\models\Cleargrade');?>
                        </div>
                    </div>
                </div>
                <?php
                    $searchModel = new app\models\CleargradeSearch();
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
<?php Pjax::end(); ?>
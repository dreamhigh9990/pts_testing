<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Pipe Stringing";

?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-5 col-lg-12 col-12 p-r-5">
            <div class="card-body card"> 
                <div class="card-header">
                    <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Stringing Info'); ?> <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?></h4>
                </div>
                <?php
                $form = ActiveForm::begin([
                    'id'=>'pipe-stringing-form',
                    'fieldConfig' => [
                        'template' => "<div class='col-md-12 clearfix'>{label}{input}{error}</div>",
                    ],
                    'options'=>['autocomplete'=>'off']
                ]);
            
                echo Yii::$app->general->defautField($model,$form);	
                if($model->isNewRecord){
                    $lastWeldData = Yii::$app->weld->getLastRecords('stringing');
                    $model->location      = !empty($lastWeldData['location']) ? $lastWeldData['location'] : '';
                    $model->kp            = isset($lastWeldData['kp']) ? $lastWeldData['kp'] : '';
                    $model->geo_location  = !empty($lastWeldData['geo_location']) ? $lastWeldData['geo_location'] : '';
                }
                ?>
                <?= $form->field($model, 'location')->dropDownList(Yii::$app->general->TaxonomyDrop(2),['prompt'=>'Please Select','class'=>'form-control location-drop','disabled' => Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'kp')->textInput(['class'=>'form-control kp-range','disabled' => Yii::$app->general->isAllowed()]) ?>
                <?php Yii::$app->general->pipeFiled($model,$form);?>  
                <?php Yii::$app->general->defautFileField($model,$form,'Stringing');?>  
                <div class="col-md-12 clearfix">
                    <div class="input-group">
                        <input type="text" class="form-control geo-location" name="Stringing[geo_location]" placeholder="<?= Yii::$app->trans->getTrans('Geo Location'); ?>" aria-describedby="basic-addon4" value="<?= $model->geo_location; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                        <div class="input-group-append">
                            <span class="input-group-text map-picker-addon-single" id="map-picker-addon-single"><i class="icon-pointer"></i></span>
                        </div>
                    </div>
                </div>
                <?= $form->field($model, 'relocated')->dropDownList([ 'No' => 'No', 'Yes' => 'Yes' ], ['disabled' => 'disabled']) ?>
                <div class="form-group clearfix">
                    <div class="col-md-12 clearfix">                       
                        <?php Yii::$app->general->pipeTransfer($model);?>  
                    </div>
                </div>
                <div class="col-md-12 clearfix">
                    <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
	<div class="width-bigSm bgsm-side right-table">
        <div class="card-body card"> 
            <div class="card-header">
                <div class="pipe-listbarIcon">
                    <a href="#" class="add-remove"><i class="fa fa-bars fa-lg"></i></a>
                </div>
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Stringing List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Stringing');?>
                        <?= Yii::$app->general->gridButton('app\models\Stringing');?>
                        <?= Yii::$app->export->generateExcelExportButton(); ?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\StringingSearch();
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
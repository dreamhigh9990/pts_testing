<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Cable Stringing";
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-12 p-r-5 p-r-15">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Cable stringing Info'); ?>
                    <?php if(!Yii::$app->general->isAllowed()){?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                    <?php } ?>
                </h4>
            </div>   
            <?php 
            $form = ActiveForm::begin([
                'id'=>'cab-stringing-form',
                'fieldConfig' => [
                    'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                ],
                'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
            ]);
            ?>
                <div class="col-md-2 tab-width clearfix">                   
                    <?= Yii::$app->general->cableFiled($model,$form);  
                    if ($model->isNewRecord){ 	
                        echo  $form->field($model, 'drum_number',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class'=>'form-control drum_number','disabled' => Yii::$app->general->isAllowed()]); 
                    }else{ ?>
                        <div class="form-group clearfix">
                            <div class="col-md-12 clearfix">
                                <label class="control-label">Drum Number</label>
                                <input type="text" class="form-control " value="<?=$model->drum_number;?>" disabled="disabled">
                            </div>
                        </div>
                    <?php } ?>                             
                    <?= $form->field($model, 'length')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'location')->dropDownList(Yii::$app->general->TaxonomyDrop(2), ['prompt' => 'Please select','class'=>'location-drop form-control','disabled' => Yii::$app->general->isAllowed()]); ?>
                    <?= $form->field($model, 'from_kp')->textInput(['class'=>'kp-range form-control','disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'to_kp')->textInput(['class'=>'kp-range form-control','disabled' => Yii::$app->general->isAllowed()]) ?>                    
                    <div class="col-md-12 clearfix">
                        <div class="input-group">
                            <input type="text" class="form-control geo-location" name="CabStringing[geo_location]" placeholder="<?= Yii::$app->trans->getTrans('Geo Location'); ?>" aria-describedby="basic-addon4" value="<?= $model->geo_location; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                            <div class="input-group-append">
                                <span class="input-group-text map-picker-addon-single" id="map-picker-addon-single"><i class="icon-pointer"></i></span>
                            </div>
                        </div>
                    </div>
                    <?php Yii::$app->general->defautFileField($model,$form,'CabStringing');?> 
                </div>         
                <div class="col-md-10 col-sm-12 tab-width-matrix">
                    <div class="row">
                        <?= $this->render('_matrix', ['model'=>$model]);?>
                    </div>
                </div>         
                
                <div class="col-md-12 clearfix">
                    <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    
	<div class="col-xl-12 col-lg-12 col-12 p-r-5 p-r-15">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Cable stringing List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->gridButton('app\models\CabStringing');?>
                        <?= Yii::$app->general->signOffButton('app\models\CabStringing');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\CabStringingSearch();
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
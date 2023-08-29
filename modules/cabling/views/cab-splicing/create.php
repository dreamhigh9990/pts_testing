<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Cable Splicing";
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-12 p-r-5 p-r-15">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Cable Splicing Info'); ?>
                    <?php if(!Yii::$app->general->isAllowed()){?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                    <?php } ?>
                </h4>
            </div>   
            <?php 
            $form = ActiveForm::begin([
                'id'=>'cab-splicing-form',
                'fieldConfig' => [
                    'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                ],
                'options' => ['enctype' => 'multipart/form-data','method' => 'post','autocomplete'=>'off']
            ]);
            ?>
                <div class="col-md-2 tab-width clearfix">                   
                    <?= Yii::$app->general->cableFiled($model,$form);
                    $nextDisabled        = $model->isNewRecord && !Yii::$app->general->isAllowed()?false:true;
                    $model->geo_location = $model->isNewRecord?"-25.2744,133.7751":$model->geo_location;
                    if ($model->isNewRecord){ 	
                        echo  $form->field($model, 'drum_number',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class'=>'form-control splice_drum_number','disabled' => Yii::$app->general->isAllowed()]); 
                    }else{ 
                        ?>
                        <div class="form-group clearfix">
                            <div class="col-md-12 clearfix">
                                <label class="control-label">Drum Number</label>
                                <input type="text" class="form-control " value="<?=$model->drum_number;?>" disabled="disabled">
                            </div>
                        </div>
                    <?php } ?>
                    <?= $form->field($model, 'next_drum',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class'=>'form-control splice_next_drum_number','disabled'=>$nextDisabled]); ?>
                    <?= $form->field($model, 'splice_number',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'kp',['template' => '<div class="col-md-12 clearfix ">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'light_source',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <div class="col-md-12 clearfix">
                        <div class="input-group">
                            <input type="text" class="form-control geo-location" name="CabSplicing[geo_location]" placeholder="<?= Yii::$app->trans->getTrans('Geo Location'); ?>" aria-describedby="basic-addon4" value="<?= $model->geo_location; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                            <div class="input-group-append">
                                <span class="input-group-text map-picker-addon-single" id="map-picker-addon-single"><i class="icon-pointer"></i></span>
                            </div>
                        </div>
                    </div>
                    
                    
                    <?= $form->field($model, 'power_meter_1',['template' => '<div class="col-md-12 clearfix ">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'power_meter_2',['template' => '<div class="col-md-12 clearfix ">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?> 
                    <?php Yii::$app->general->defautFileField($model,$form,'CabSplicing');?> 
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Cable Splicing List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->gridButton('app\models\CabSplicing');?>
                        <?= Yii::$app->general->signOffButton('app\models\CabSplicing');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\CabSplicingSearch();
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
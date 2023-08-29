<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Line;
$this->title = "Land Owner";
$projectList = Yii::$app->general->TaxonomyDrop(4,true);
?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
    <div class="col-xl-3 col-lg-12 col-12 p-r-5 add-15-991">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Landowner Info'); ?> <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?></h4>
            </div>
            <?php
            $form = ActiveForm::begin([
                'fieldConfig' => [
					'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                ]
                ,'options' => ['method' => 'post','autocomplete'=>'off']
            ]);
            ?>
            <?php
            if(!$model->isNewRecord){
                $Employee = app\models\Employee::find()->where(['id'=>$model->created_by])->asArray()->one();
                if(!empty($Employee)) $model->created_by = $Employee['username'];
                echo $form->field($model, 'created_by', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>true])->label('User');
            }
            ?>
            <?= $form->field($model, 'from_kp', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])
            ->textInput(['class'=>'form-control from-kp from-kp-land', 'disabled'=>!Yii::$app->general->isAllowed() ? false : true]); ?>
            <div class="col-md-12 clearfix">
                <div class="input-group">
                    <input type="text" class="form-control geo-location-twice geo-start geo-from-land" name="Landowner[from_geo_code]" placeholder="<?= Yii::$app->trans->getTrans('From Geocode'); ?>" aria-describedby="basic-addon4" value="<?= $model->from_geo_code; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                    <div class="input-group-append">
                        <span class="input-group-text map-picker-addon" id="map-picker-addon" data-section="landowner"><i class="icon-pointer"></i></span>
                    </div>
                </div>
            </div>
            

            <?= $form->field($model, 'to_kp', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])
            ->textInput(['class'=>'form-control to-kp to-kp-land', 'disabled'=> !Yii::$app->general->isAllowed() ? false : true]); ?>                
            <div class="col-md-12 clearfix">
                <div class="input-group">
                    <input type="text" class="form-control geo-location-twice geo-end geo-to-land" name="Landowner[to_geo_code]" placeholder="<?= Yii::$app->trans->getTrans('To Geocode'); ?>" aria-describedby="basic-addon4" value="<?= $model->to_geo_code; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                    <div class="input-group-append">
                        <span class="input-group-text map-picker-addon" id="map-picker-addon" data-section="landowner"><i class="icon-pointer"></i></span>
                    </div>
                </div>
            </div> 
            
            
            <?= $form->field($model, 'landholder', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>

			<?= $form->field($model, 'site_reference',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textArea(['disabled' => Yii::$app->general->isAllowed()]) ?>
        
            <?= $form->field($model, 'fencing_details',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textArea(['disabled' => Yii::$app->general->isAllowed()]) ?>
        
            <?= $form->field($model, 'gate_management',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textArea(['disabled' => Yii::$app->general->isAllowed()]) ?>
        
            <?= $form->field($model, 'stock_impact',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textArea(['disabled' => Yii::$app->general->isAllowed()]) ?>
        
            <?= $form->field($model, 'vegetation_impact',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textArea(['disabled' => Yii::$app->general->isAllowed()]) ?>
        
            <?= $form->field($model, 'weed_hygiene',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textArea(['disabled' => Yii::$app->general->isAllowed()]) ?>
            <?= $form->field($model, 'foregin_service',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textArea(['disabled' => Yii::$app->general->isAllowed()]) ?>
            <?php Yii::$app->general->defautFileField($model,$form,'Landowner');?>
            <div class="col-md-12 clearfix">
                <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
	<div class="col-xl-9 col-lg-12 col-12 p-l-5 add-15-991">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Landowner List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Landowner');?>
                        <?= Yii::$app->general->gridButton('app\models\Landowner');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\LandownerSearch();
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


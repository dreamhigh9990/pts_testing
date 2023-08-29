<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\TaxonomyValue;
use yii\helpers\ArrayHelper;
$this->title = "Line";
$searchModel = new app\models\LineSearch();
?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-12 col-lg-12 col-12 p-r-5 add-15-991">
            <div class="card-body card"> 
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <?= Yii::$app->trans->getTrans('Line info'); ?>
                        <?php if(!Yii::$app->general->isAllowed()){ ?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?></h4>
                        <?php } ?>
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
                    <?= $form->field($model, 'from_kp', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>$model->isNewRecord && !Yii::$app->general->isAllowed() ? false : true]) ?>
                    
                    <div class="col-md-12 clearfix">
                        <div class="input-group">
                            <input type="text" class="form-control geo-location-twice geo-start" name="Line[from_geo_code]" placeholder="<?= Yii::$app->trans->getTrans('From Geocode'); ?>" aria-describedby="basic-addon4" value="<?= $model->from_geo_code; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                            <div class="input-group-append">
                                <span class="input-group-text map-picker-addon" id="map-picker-addon" data-section="line"><i class="icon-pointer"></i></span>
                            </div>
                        </div>
                    </div>

                    <?= $form->field($model, 'to_kp', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>$model->isNewRecord && !Yii::$app->general->isAllowed() ? false : true]); ?>
                    
                    <div class="col-md-12 clearfix">
                        <div class="input-group">
                            <input type="text" class="form-control geo-location-twice geo-end" name="Line[to_geo_code]" placeholder="<?= Yii::$app->trans->getTrans('To Geocode'); ?>" aria-describedby="basic-addon4" value="<?= $model->to_geo_code; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                            <div class="input-group-append">
                                <span class="input-group-text map-picker-addon" id="map-picker-addon" data-section="line"><i class="icon-pointer"></i></span>
                            </div>
                        </div>
                    </div>                         
                    
                    <?= $form->field($model, 'pipe_diameter',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'wall_thickness')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'depth_of_cover')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'coating_type')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'bend_location')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'road_crossing')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'river_crossing')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'foreign_service_crossing' ,['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'fence_crossing')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'hdd_locations')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'backfill_material',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'marker_tape_location',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?php Yii::$app->general->defautFileField($model,$form,'Line');?>  
                    <div class="col-md-12 clearfix">
                        <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
                    </div>                
                <?php ActiveForm::end(); ?>
            </div>
        </div> 
    </div>            
    <div class="width-bigSm bgsm-side right-table">
        <div class="col-xl-12 col-lg-12 col-12 p-l-5 add-15-991">
            <div class="card-body card"> 
                <div class="card-header">
                    <div class="pipe-listbarIcon">
                        <a href="#" class="add-remove"><i class="fa fa-bars fa-lg"></i></a>
                    </div>
                    <h4 class="card-title"><?= Yii::$app->trans->getTrans('Line List'); ?></h4>
                </div>
                <div class="row">
                    <div class="col-12">
                    <div class="form-group">
                            <?=  Yii::$app->general->exportImport('line');?>    
                           
                            <?= Html::a(Yii::$app->trans->getTrans('Export Line'),['/report/report/index','model'=>'LineSearch','download'=>1],['class'=>'mr-1 mb-1 btn btn-raised btn-outline-blue btn-min-width pull-right','data-pjax'=>0]);?>
                            <?= Yii::$app->general->gridButton('app\models\Line');?>
                        </div>
                    </div>
                </div>
                <?php               
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
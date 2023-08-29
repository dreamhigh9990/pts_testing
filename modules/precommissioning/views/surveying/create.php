<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Surveying";

?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
      <div class="col-xl-12 col-lg-12 col-12 p-r-5">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Surveying Info'); ?> <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?></h4>
            </div>
            <?php 
            	$Picture = new \app\models\Picture;
                $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                    ],
					'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
            ]);?>
                <?= Yii::$app->general->precommFiled($model, $form, 'surveying');?>
                <div class="col-md-12 col-sm-6 clearfix">
                    <div class="input-group">
                        <input type="text" class="form-control geo-location" name="Surveying[geo_location]" placeholder="<?= Yii::$app->trans->getTrans('Geo Location'); ?>" aria-describedby="basic-addon4" value="<?= $model->geo_location; ?>" <?= Yii::$app->general->isAllowed() ? 'disabled="disabled"' : ''; ?>>
                        <div class="input-group-append">
                            <span class="input-group-text map-picker-addon-single" id="map-picker-addon-single" data-section="surveying"><i class="icon-pointer"></i></span>
                        </div>
                    </div>
                </div>
                
                <?= $form->field($model, 'ir_reading',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                <?php Yii::$app->general->defautFileField($model,$form,'Surveying');?>
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Surveying List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Surveying');?>
                        <?= Yii::$app->general->gridButton('app\models\Surveying');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\SurveyingSearch();
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

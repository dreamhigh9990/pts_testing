<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Trenching";

$prestart = Yii::$app->general->TaxonomyDrop(16);
if(!empty($model->pre_start)){
	$model->pre_start = json_decode($model->pre_start,true);
}
$duringtrenching = Yii::$app->general->TaxonomyDrop(17);
if(!empty($model->during_trenching)){
	$model->during_trenching = json_decode($model->during_trenching,true);
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
     <div class="col-xl-12 col-lg-12 col-12 p-r-5">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Trenching Info');?>
                <?php if(!Yii::$app->general->isAllowed()){?>
                    <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                <?php } ?>
                </h4>
            </div>          
            <?php 
            $form = ActiveForm::begin([
                'id'=>'trenching-form',
                'fieldConfig' => [
                    'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                ],
                'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
            ]);
            ?>
                <?= Yii::$app->general->civilFiled($model,$form);?> 
                <?= $form->field($model, 'width')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'depth')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>                
                <?php if(!Yii::$app->general->isAllowed()){?>
                    <?= $form->field($model, 'pre_start', ['template' => '<div class="col-md-12 clearfix check_boxes"><h3>{label}</h3>{input}{error}{hint}</div>'])->checkboxList($prestart); ?>
                    <?= $form->field($model, 'during_trenching', ['template' => '<div class="col-md-12 clearfix check_boxes"><h3>{label}</h3>{input}{error}{hint}</div>'])->checkboxList($duringtrenching); ?>  
                <?php }else{ 
                    $model->pre_start = json_encode($model->pre_start);
                    $model->during_trenching = json_encode($model->during_trenching);
                    
                    $form->field($model, 'pre_start', ['template' => '<div class="col-md-12 clearfix check_boxes"><h3>{label}</h3>{input}{error}{hint}</div>'])->textInput(['disabled'=>true]);
                    $form->field($model, 'during_trenching', ['template' => '<div class="col-md-12 clearfix check_boxes"><h3>{label}</h3>{input}{error}{hint}</div>'])->textInput(['disabled'=>true]);
                }
                ?>
                <?= Yii::$app->general->defautFileField($model,$form,'Trenching');?>                
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Trenching List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Trenching');?>
                        <?= Yii::$app->general->gridButton('app\models\Trenching');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\TrenchingSearch();
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
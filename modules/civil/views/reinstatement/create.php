<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Reinstatement";
$checkpoints = Yii::$app->general->TaxonomyDrop(19);
if(!empty($model->check_points)){
	$model->check_points = json_decode($model->check_points,true);
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
     <div class="col-xl-12 col-lg-12 col-12 p-r-5">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Reinstatement Info'); ?>
                <?php if(!Yii::$app->general->isAllowed()){?>
                    <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                <?php } ?></h4>
            </div>         
            <?php
            $form = ActiveForm::begin([
                'id'=>'reinstatement-form',
                'fieldConfig' => [
                    'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                ],
                'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
            ]);
            ?>
                <?= Yii::$app->general->civilFiled($model,$form);?> 
                <?= $form->field($model, 'length', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'qty_markers_installed', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                <?php if(!Yii::$app->general->isAllowed()){?>
                    <?= $form->field($model, 'check_points', ['template' => '<div class="col-md-12 clearfix check_boxes">{label}{input}{error}{hint}</div>'])->checkboxList($checkpoints); ?>
                <?php }else{
                    $model->check_points = json_encode($model->check_points);
                    echo $form->field($model, 'check_points',['template' => '<div class="col-md-12 clearfix check_boxes">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>true]);
                }
                ?>
                <?= Yii::$app->general->defautFileField($model,$form,'Reinstatement');?>
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Reinstatement List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Reinstatement');?>
                        <?= Yii::$app->general->gridButton('app\models\Reinstatement');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\ReinstatementSearch();
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
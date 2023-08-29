<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Hydro Testing";

$checkpoints = Yii::$app->general->TaxonomyDrop(18);
$checkpoints = ArrayHelper::map($checkpoints, 'id', 'value');
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
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Hydro Testing Info'); ?> <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?></h4>
            </div>
            <?php 
            	$Picture = new \app\models\Picture;
                $form = ActiveForm::begin([
                    'id'=>'hydrotesting-form',
                    'fieldConfig' => [
                        'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                    ],
					'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
            ]);?>  
                <?= Yii::$app->general->precommFiled($model, $form, 'hydrotesting');?>                
                <?= $form->field($model, 'test_result',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList([ 'Yes' => 'Accept', 'No' => 'Reject', ], ['prompt' => '','disabled' => Yii::$app->general->isAllowed()]) ?>
                <?php Yii::$app->general->defautFileField($model,$form,'Hydrotesting');?>                
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Hydro Testing List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Hydrotesting');?>
                        <?= Yii::$app->general->gridButton('app\models\Hydrotesting');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\HydrotestingSearch();
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

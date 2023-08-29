<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Cutting";
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-12 col-lg-12 col-12 p-r-5">
            <div class="card-body card"> 
                <div class="card-header">
                    <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Cutting Info'); ?> 
                        <?php if(!Yii::$app->general->isAllowed()){?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                        <?php } ?>
                    </h4>
                </div>
                <?php
                $form = ActiveForm::begin([
                    'id'=>'pipe-cutting-form',
                    'fieldConfig' => [
                        'template' => "<div class='col-md-6 clearfix'>{label}{input}{error}</div>",
                    ],
					'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
                ]);
                ?>
                <?php echo Yii::$app->general->defautField($model,$form);?>        
                <?php Yii::$app->general->pipeFiled($model,$form);?>

                <?= $form->field($model, 'length',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->hiddenInput(['maxlength' => true,'class'=>'form-control','disabled' => Yii::$app->general->isAllowed()])->label(false); ?>

                
                <?= $form->field($model, 'new_pipe_2')->textInput(['maxlength' => true, 'class' => 'form-control', 'disabled' => true]) ?>
                <?= $form->field($model, 'length_2')->textInput(['maxlength' => true,'class'=>'form-control','disabled' => Yii::$app->general->isAllowed()]) ?>
                
                <!-- remove field as per client say -->
                <?php //echo $form->field($model, 'retain_pipe_number',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList(['1' => '1', '2' => '2'],['disabled' => Yii::$app->general->isAllowed()]) ?>
                <!-- remove field as per client say -->

                <?php Yii::$app->general->defautFileField($model,$form,'Cutting');?>  
                <?php if ($model->isNewRecord){	?>
                    <div class="col-md-12 clearfix">
                        <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
                    </div>
                <?php  } ?>
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Cutting List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Cutting');?>
                        <?= Yii::$app->general->gridButton('app\models\Cutting');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\CuttingSearch();
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
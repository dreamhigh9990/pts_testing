<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Bending";

$bendingCheckListArray = Yii::$app->general->TaxonomyDrop(33);
$bendDesignationList = Yii::$app->general->TaxonomyDrop(34);

if(!empty($model->bending_checkpoints)){
	$model->bending_checkpoints = json_decode($model->bending_checkpoints, true);
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-12 col-lg-12 col-12 p-r-5">
            <div class="card-body card"> 
                <div class="card-header">
                    <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Bending Info');?>
                        <?php if(!Yii::$app->general->isAllowed()){?>
                            <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                        <?php } ?>
                    </h4>
                </div>         
                <?php 
                $form = ActiveForm::begin([
                    'id'=>'pipe-bending-form',
                    'fieldConfig' => [
                        'template' => "<div class='col-md-6 clearfix'>{label}{input}{error}</div>",
                    ],
                    'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
                ]);
                ?>
                    <?php echo Yii::$app->general->defautField($model,$form);?>        
                    <?php Yii::$app->general->pipeFiled($model,$form);?>

                    <?= $form->field($model, 'designation')->dropDownList($bendDesignationList, ['prompt'=>'Please Select', 'disabled' => Yii::$app->general->isAllowed()]) ?>

                    <?= $form->field($model, 'angle',['template' => '<div class="col-md-6 col-sm-6 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['maxlength' => true,'class'=>'form-control','disabled' => Yii::$app->general->isAllowed()]) ?>
                    
                    <?= $form->field($model, 'position',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['maxlength' => true,'class'=>'form-control','disabled' => Yii::$app->general->isAllowed()]) ?>
                    
                    <?= $form->field($model, 'pull_through_accepted',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList(['Yes' => 'Yes', 'No' => 'No'],['prompt' => 'Please Select', 'disabled' => Yii::$app->general->isAllowed()]) ?>

                    <?php if(!Yii::$app->general->isAllowed()){ ?>
                        <?= $form->field($model, 'bending_checkpoints', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->checkboxList($bendingCheckListArray); ?>            
                    <?php } else {
                        if(!empty($model->bending_checkpoints)){
                            $model->bending_checkpoints = json_encode($model->bending_checkpoints);
                    ?>
                        <?= $form->field($model, 'bending_checkpoints', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['disabled'=>true,'rows'=>10]); ?>            
                    <?php
                        }
                    }
                    ?>

                    <?php Yii::$app->general->defautFileField($model,$form,'Bending');?>  
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Bending List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Bending');?>
                        <?= Yii::$app->general->gridButton('app\models\Bending');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\BendingSearch();
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
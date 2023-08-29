<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\TaxonomyValue;
use yii\helpers\ArrayHelper;
$this->title = "Transfer";


$locations = Yii::$app->general->TaxonomyDrop(2);
$trucks = Yii::$app->general->TaxonomyDrop(1);

?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-12 col-lg-12 col-12 p-r-5">
            <div class="card-body card"> 
                <div class="card-header">
                    <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Transfer Info'); ?> <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?></h4>
                </div>           
                <?php 
                $form = ActiveForm::begin([
                    'id'=>'pipe-transfer-form',
                    'options'=>['autocomplete'=>'off'],
                    'fieldConfig' => [
                        'template' => "<div class='col-md-6 clearfix'>{label}{input}{error}</div>",
                    ],
                ]);
                ?>
                <?php echo Yii::$app->general->defautField($model,$form);?>        
                <?php Yii::$app->general->pipeFiled($model,$form);?>  
                <?= $form->field($model, 'new_location',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList($locations,['prompt' => Yii::$app->trans->getTrans('Please Select'), 'disabled' => Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'truck',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList($trucks,['prompt' => Yii::$app->trans->getTrans('Please Select'), 'disabled' => Yii::$app->general->isAllowed()]) ?>
                <?php Yii::$app->general->defautFileField($model,$form,'PipeTransfer');?> 
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Transfer List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\PipeTransfer');?>
                        <?= Yii::$app->general->gridButton('app\models\PipeTransfer');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\PipeTransferSearch();
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


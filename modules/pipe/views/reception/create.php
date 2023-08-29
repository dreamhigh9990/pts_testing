<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Reception";

$lastRecord = \app\models\Reception::find()->active()->orderBy('id DESC')->asArray()->one();
if($model->isNewRecord && !empty($lastRecord))$model->truck = $lastRecord['truck'];
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
<div class="left-sideDiv bgsm-side left-table">
            <div class="col-xl-12 col-lg-12 col-12 p-r-5">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Reception Info'); ?>
                    <?php if(!Yii::$app->general->isAllowed()){?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                    <?php } ?>
                </h4>
            </div>           
            <?php
            $form = ActiveForm::begin([
                'id'=>'pipe-reception-form',
                'fieldConfig' => [
                    'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                ],
                'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
            ]);
            
                echo Yii::$app->general->defautField($model,$form);	
                echo Yii::$app->general->pipeFiled($model,$form);
                if($model->isNewRecord){
                    $lastWeldData = Yii::$app->weld->getLastRecords('reception');               
                    $model->location = !empty($lastWeldData['location']) ? $lastWeldData['location'] : '';;
                }
            ?>
                <?= $form->field($model, 'truck')->dropDownList(Yii::$app->general->TaxonomyDrop(1),['prompt' => Yii::$app->trans->getTrans('Please Select'), 'disabled' => Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'location')->dropDownList(Yii::$app->general->TaxonomyDrop(2),['prompt' => Yii::$app->trans->getTrans('Please Select'), 'disabled' => Yii::$app->general->isAllowed()]) ?>

                <?= $form->field($model, 'transferred',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList(['No' => 'No', 'Yes' => 'Yes'],['disabled' =>true]) ?>
                
                <?php Yii::$app->general->defautFileField($model, $form, 'Reception');?>  
                <div class="form-group clearfix">
                    <div class="col-md-12 clearfix">                       
                        <?php Yii::$app->general->pipeTransfer($model);?>  
                    </div>
                </div>
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Reception List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->signOffButton('app\models\Reception');?>
                        <?= Yii::$app->general->gridButton('app\models\Reception');?>
                        <?= Yii::$app->export->generateExcelExportButton(); ?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\ReceptionSearch();
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
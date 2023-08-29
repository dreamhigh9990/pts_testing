<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Cable";
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
<div class="left-sideDiv bgsm-side left-table">
<div class="col-xl-12 col-lg-12 col-12 p-r-5">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Cable Info'); ?>
                <?php if(!Yii::$app->general->isAllowed()){?>
                         <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                <?php } ?>                
                </h4>
            </div>          
            <?php 
                $form = ActiveForm::begin([
                    'id'=>'cable-form',
                    'fieldConfig' => [
                        'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                    ],
                    'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
              ]);?>
                 <?= $form->field($model, 'drum_number',['template' => '<div class="col-md-12 clearfix ui-front">{label}{input}{error}{hint}</div>'])
                ->textInput(['maxlength' => true,'class'=>'form-control','disabled' => Yii::$app->general->isAllowed()]) ?>

                 <?= $form->field($model, 'drum_cable')->dropDownList(Yii::$app->general->TaxonomyDrop(24), ['prompt' => 'Please select','disabled' => Yii::$app->general->isAllowed()]); ?>
                 <?= $form->field($model, 'length')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>

                <?= $form->field($model, 'brand')->textInput(['maxlength' => true,'disabled' => Yii::$app->general->isAllowed()]) ?>

                <?= $form->field($model, 'cores')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>

               <?= $form->field($model, 'standard')->dropDownList(Yii::$app->general->TaxonomyDrop(25), ['prompt' => Yii::$app->trans->getTrans('Please select'),'disabled' => Yii::$app->general->isAllowed()]); ?>

                <?= $form->field($model, 'colour')->textInput(['maxlength' => true,'disabled' => Yii::$app->general->isAllowed()]) ?>

                <?= $form->field($model, 'comment',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['rows' => 2]) ?>
                
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
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Cable List'); ?></h4>
            </div>
             <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Yii::$app->general->gridButton('app\models\Cable');?>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\CableSearch();
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
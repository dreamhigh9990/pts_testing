<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Upload Your Brand Logo";
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
    <div class="row">
            <div class="col-xl-5 col-lg-12 col-12 p-r-5">
                <div class="card-body card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Upload your Brand Logo'); ?></h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
                                    <?= $form->field($model, 'value')->fileInput(['maxlength' => true]) ?>
                                    <div class="form-group">
                                        <?= Html::submitButton(Yii::$app->trans->getTrans('Save'), ['class' => 'btn btn-success']) ?>
                                    </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                        <div class="col-md-6">        
                             <label class="control-label" for="setting-value"><?= Yii::$app->trans->getTrans('Current Logo'); ?></label> 
                             <div class="form-group">              
                                    <img src="<?php echo Yii::$app->general->logo();?>" style="max-width:200px"/>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php Pjax::end(); ?>
            

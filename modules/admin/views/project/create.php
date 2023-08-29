<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Project;
use app\models\ProjectSearch;
$this->title = "Project";
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="col-xl-3 col-lg-12 col-12">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Project Info'); ?>
                    <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                </h4>
            </div>
            <?php
            $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='col-md-12 clearfix'>{label}{input}{error}</div>",
                ]
                ,'options' => ['method' => 'post','autocomplete'=>'off']
            ]);
            ?>
            <?= $form->field($model, 'value')->textInput(['maxlength' => true])->label(Yii::$app->trans->getTrans('Project Name')); ?>                   
            <div class="col-md-12 clearfix">
                <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
	<div class="col-xl-9 col-lg-12 col-12 p-l-0 add-pedding">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Project List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <!-- <button type="button" url="pipe/default/delete-multiple?model=app\models\TaxonomyValue" class="mb-1 btn btn-raised btn-outline-danger btn-min-width delete-multipe"><i class="fa fa-times"></i> Delete selected</button> -->
                        <button type="button" url="admin/project/delete-multiple?model=app\models\TaxonomyValue" class="mb-1 btn btn-raised btn-outline-danger btn-min-width delete-multipe"><i class="fa fa-times"></i> <?= Yii::$app->trans->getTrans('Delete selected'); ?></button>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\ProjectSearch();
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


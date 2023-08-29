<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Pipe";
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="left-sideDiv bgsm-side left-table">
        <div class="col-xl-5 col-lg-12 col-12 p-r-5">
            <div class="card-body card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Pipe Info'); ?>
                        <?php if(!Yii::$app->general->isAllowed()){?>
                            <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                        <?php } ?>
                    </h4>
                </div>
                <?php 
                $form = ActiveForm::begin([
                    'id'=>'pipe-form',
                    'options'=>['autocomplete'=>'off'],
                    'fieldConfig' => [
                        'template' => "<div class='col-md-6 col-sm-6 col-xs-12 clearfix'>{label}{input}{error}</div>",
                    ],
                ]);?>
                    <?= $form->field($model, 'pipe_number', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>',])->textInput(['disabled' => Yii::$app->general->isAllowed()]); ?>
                    <?= $form->field($model, 'wall_thikness')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'weight')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'heat_number')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'yeild_strength')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'length')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'od')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'coating_type')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'plate_number')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'ship_out_number')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'vessel')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'hfb')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'mto_number')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'mto_certificate')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'mill')->textInput(['disabled' => Yii::$app->general->isAllowed()]) ?>
                    <?= $form->field($model, 'comments', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['rows' => 6]); ?>
                    
                    <div class="savebtn-info clearfix">
                        <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="col-xl-7 col-lg-12 col-12 p-r-5 p-l-5 middel-hide">
            <div class="card-body card">
                <div class="card-header">
                    <h4 class="card-title"><?= Yii::$app->trans->getTrans('Pipe Status'); ?></h4>
                </div>
                <?= $this->render('status',['model'=>$model]);?>
            </div>
            <div class="card-body card">
                <div class="card-header">
                    <h4 class="card-title"><?= Yii::$app->trans->getTrans('Pipe Defects'); ?></h4>
                </div>
                <ul class="list-group">
                    <?php  
                    $defects = json_decode($model->defects,true);
                    if(!empty( $defects )){
                        foreach($defects as $ele){
                    ?>
                        <li class="list-group-item">
                            <b class="float-left"><?= $ele;?></b>;
                            <a class="btn btn-danger pull-right" href="<?php echo Url::to(['/pipe/pipe/defect-update', 'id' => $model->id,'defectsItem'=>$ele]);?>"><i class="fa fa-trash"></i></a>
                        </li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="width-bigSm bgsm-side right-table">
        <div class="col-xl-12 col-lg-12 col-12 p-l-0 p-r-0">
            <div class="card-body card">
                <div class="card-header">
                    <div class="pipe-listbarIcon">
                        <a href="#" class="add-remove"><i class="fa fa-bars fa-lg"></i></a>
                    </div>
                    <h4 class="card-title"><?= Yii::$app->trans->getTrans('Pipe List'); ?></h4>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <?= Yii::$app->general->exportImport('import'); ?>
                            <?php
                            $sort = '';
                            if(!empty($_GET['sort']) && $_GET['sort'] == 'pipe_number'){
                                $sort = 'DESC';
                            } else if(!empty($_GET['sort']) && $_GET['sort'] == '-pipe_number') {
                                $sort = 'ASC';
                            }
                            echo Yii::$app->general->gridButton('app\models\Pipe', $sort);
                            ?>
                            <?= Yii::$app->export->generateExcelExportButton(true); ?>
                        </div>
                    </div>
                </div>
                <?php
                $searchModel = new app\models\PipeSearch();
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

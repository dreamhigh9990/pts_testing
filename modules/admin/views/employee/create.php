<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
$this->title = "Employees";
$currentProjectId = !empty(Yii::$app->user->identity->project_id) ? Yii::$app->user->identity->project_id : 0;
$userType = !empty(Yii::$app->user->identity->type) ? Yii::$app->user->identity->type : '';
$empTypeList = [
    'Safety' => 'Safety',
    'Admin' => 'Admin',
    'Client' => 'Client',
    'Inspector' => 'Inspector',
    'QA Manager' => 'QA Manager'
];
if($userType == 'QA Manager'){
    unset($empTypeList['Admin']);
}

?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="col-xl-3 col-lg-12 col-12 p-r-5 add-15-991">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Employee Info'); ?> <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?></h4>
            </div>            
		<?php $form = ActiveForm::begin([
            'options' => ['method' => 'post','autocomplete'=>'off']
    ]); ?>
     <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>

    <?php 
     if ($model->isNewRecord) { 
         echo $form->field($model, 'password_hash')->passwordInput();
     }
    ?>

    <?= $form->field($model, 'type')->dropDownList($empTypeList, ['prompt' => '']) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
   

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    <?php $projects = Yii::$app->general->TaxonomyDrop(4,true);?>
    <?php if($userType == 'QA Manager') { $model->project_id = $currentProjectId; } ?>
    <?= $form->field($model, 'project_id')->dropDownList($projects,['class'=>"form-control", 'disabled' => $userType == 'QA Manager' ? true : false]) ?>
  

    <div class="savebtn-info clearfix p-0">
        <button type="submit" class="btn btn-raised btn-primary"><?= Yii::$app->trans->getTrans('Submit'); ?></button>       
	</div>
    <?php ActiveForm::end(); ?>

   	<?php 
	if (!$model->isNewRecord) { 
			$model->password_hash = ''; ?>
    		<div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Change Password'); ?></h4>
            </div>
    	<?php $form2 = ActiveForm::begin(['action' => ['employee/changepassword','EditId'=>$model->id],'options' => ['method' => 'post','autocomplete'=>'off']]); ?>
    	<?=  $form2->field($model, 'password_hash')->passwordInput(['maxlength' => true]);?>
      
        <div class="savebtn-info clearfix p-0">
            <button type="submit" class="btn btn-raised btn-primary"><?= Yii::$app->trans->getTrans('Submit'); ?></button>
         
        </div>
     <?php ActiveForm::end(); ?>
	<?php 
	}
	?>
            
		</div>
	</div>
    <div class="col-xl-9 col-lg-12 col-12 p-l-5 add-15-991">
    	<div class="card-body card"> 
             <div class="card-header">
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Employee List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Html::a(Yii::$app->trans->getTrans('Clear Filter'),'create',['class'=>'pull-right mr-1 mb-1 btn btn-raised btn-outline-info btn-min-width signed-selected"><i class="ft-power mr-2']); ?>
                        <button type="button" url="pipe/default/delete-multiple?model=app\models\Employee" class="mb-1 btn btn-raised btn-outline-danger btn-min-width delete-multipe"><i class="fa fa-times"></i> <?= Yii::$app->trans->getTrans('Delete selected'); ?></button>
                    </div>
                </div>
            </div>
        <?php
            $searchModel = new app\models\EmployeeSearch();
            if($userType == 'QA Manager') { $searchModel->project_id = $currentProjectId; }
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

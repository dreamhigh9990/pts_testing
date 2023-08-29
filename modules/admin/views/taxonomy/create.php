<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Taxonomy;
use app\models\TaxonomyValue;
$this->title = "Taxonomy";
$currentLang = Yii::$app->user->identity->lang;
$getTaxonomyList = TaxonomyValue::getTaxonomyList();
if(!empty($currentLang) && $currentLang == 'fr'){
    $getTaxonomyList = ArrayHelper::map($getTaxonomyList, 'id', 'trans_name');
} else {
    $getTaxonomyList = ArrayHelper::map($getTaxonomyList, 'id', 'name');
}
$beforChildList = array();
$existchild = array();
if(!empty($model->taxonomy_id)){
    $beforChildList = Yii::$app->general->TaxonomyChild($model->taxonomy_id);
    $existchild = Yii::$app->general->getInsertedTaxonomyChild($model->id);
}
$projectList = Yii::$app->general->TaxonomyDrop(4,true);
$disabled = false;
if($model->taxonomy_id != "") $disabled = true;
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
    <div class="col-xl-3 col-lg-12 col-12 p-r-0 p-r-15">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Catalouge Info'); ?>
                    <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['create'],['class'=>'pull-right white']);?>
                </h4>
            </div>
            <?php
            $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='col-md-12 clearfix'>{label}{input}{error}</div>",
                ]
                ,'options' => ['method' => 'post','autocomplete'=>'off'],
                'id' => 'taxonomy-form',
            ]);
            ?>
            <?= $form->field($model, 'taxonomy_id')->dropDownList($getTaxonomyList,['prompt' => Yii::$app->trans->getTrans('Select Taxonomy'), 'class'=>'form-control taxonomy-val', 'disabled' => $disabled]); ?>
            <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
            
            <div class="col-md-12 clearfix mb-2">
                <div class="row taxonomy-html">
                    <?php
                    if(!empty($beforChildList)){
                        foreach($beforChildList as $key => $childList){
                            $checked = '';
                            if(!empty($existchild) && in_array($childList['id'],$existchild)){
                                $checked = 'checked';
                            }
                    ?>
                        <div class="col-6">
                            <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                <input type="checkbox" name="TaxonomyValue[taxonomyChildId][]" class="custom-control-input" <?= $checked; ?> id="customcheckbox<?= $key; ?>" value="<?= $childList['id']; ?>">
                                <label class="custom-control-label" for="customcheckbox<?= $key; ?>"><?= $childList['value']; ?></label>
                            </div>
                        </div>
                    <?php
                        }
                    }
                    ?>
                    <?php if(isset($model->taxonomy_id) && $model->taxonomy_id == 2){ ?>
                    	<div class="col-md-6 clearfix">
                            <label class="control-label" for="taxonomyValue-location_lat"><?= Yii::$app->trans->getTrans('Latitude'); ?></label>
                            <input type="text" id="taxonomyValue-location_lat" class="form-control tx-lat" name="TaxonomyValue[location_lat]" aria-required="true" aria-invalid="true" value="<?= $model->location_lat; ?>">
                        </div>
                        <div class="col-md-6 clearfix">
                            <label class="control-label" for="taxonomyValue-location_long"><?= Yii::$app->trans->getTrans('Longitude'); ?></label>
                            <input type="text" id="taxonomyValue-location_long" class="form-control tx-long" name="TaxonomyValue[location_long]" aria-required="true" aria-invalid="true" value="<?= $model->location_long; ?>">
                        </div>
                        <div class="col-md-12 mt-1 clearfix text-right">
                            <div class="taxo-loc-picker"><?= Yii::$app->trans->getTrans('Pick from Map'); ?></div>
                        </div>
                    <?php } ?>

                    <?php
                    if(!empty($model->taxonomy_id) && $model->taxonomy_id == 30){
                        $getQuestions = \app\models\MapPartQuestion::find()->where(['part_id' => $model->id])->asArray()->all();
                    ?>
                        <div class="col-md-12 clearfix">
                            <label class="control-label" for="partList-question"><?= Yii::$app->trans->getTrans('Questions'); ?> *</label>
                            <div class="part-question clearfix">
                                <?php
                                if(!empty($getQuestions)){
                                    foreach($getQuestions as $key => $que){
                                        if($key == 0){
                                        ?>
                                            <input type="hidden" name="MapPartQuestion[questionId][]" value="<?php echo $que['id']; ?>"/>
                                            <input type="text" id="partList-question" class="form-control list-question" name="MapPartQuestion[question][]" aria-required="true" aria-invalid="true" value="<?php echo $que['question']; ?>">
                                        <?php
                                        } else {
                                        ?>
                                            <div class="new-que-container col-md-12 clearfix mt-2 p-0">
                                                <div class="col-md-10 clearfix p-0">
                                                    <input type="hidden" name="MapPartQuestion[questionId][]" value="<?php echo $que['id']; ?>"/>
                                                    <input type="text" id="partList-question" class="form-control list-question" name="MapPartQuestion[question][]" aria-required="true" aria-invalid="true" value="<?php echo $que['question']; ?>">
                                                </div>
                                                <div class="col-md-2 clearfix p-r-0 text-right">
                                                    <a href="#" class="btn btn-danger btn-sm btn-remove-que"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                    }
                                } else {
                                ?>
                                    <input type="text" id="partList-question" class="form-control list-question" name="MapPartQuestion[question][]" aria-required="true" aria-invalid="true">
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-12 mt-1 clearfix text-right">
                            <a href="#" class="btn btn-primary btn-sm btn-add-more-que"><?= Yii::$app->trans->getTrans('Add Question'); ?></a>
                        </div>
                    <?php } ?>
                </div>
                </div>
                <div class="<?= $model->taxonomy_id == 5?"":"electrode";?>">
                    <?= $form->field($model, 'type')->textInput(['class' => ' form-control']) ?>
                    <?= $form->field($model, 'size')->textInput(['class' => ' form-control']) ?>
                </div>
            
            <div class="col-md-12 clearfix">
                <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Copy Catalouge'); ?></h4>
            </div>
            <?php
            $form = ActiveForm::begin([
                'action'=>\yii\helpers\Url::to(['/admin/taxonomy/copy-all']),
                'fieldConfig' => [
                    'template' => "<div class='col-md-12 clearfix'>{label}{input}{error}</div>",
                ]
                ,'options' => ['method' => 'post','autocomplete'=>'off']
            ]);
            ?>
            <div class="col-md-12 clearfix">
                <label class="control-label" for="taxonomyvalue-taxonomy_id"><?= Yii::$app->trans->getTrans('Catalouge Type'); ?></label>
                <?= Html::dropDownList('TaxonomyValue[taxonomy_id]','', $getTaxonomyList,
                    ['class'=>'custom-select cz-sidebar-width float-right','prompt'=>'All'])
                ?>
            </div>

            <div class="col-md-12 clearfix"  style="margin-top:10px">
            <label class="control-label" for="taxonomyvalue-taxonomy_id"><?= Yii::$app->trans->getTrans('Copy From Which Project ?'); ?></label>
                <?php $projects = Yii::$app->general->TaxonomyDrop(4,true);               
                ?>
                <?= Html::dropDownList('TaxonomyValue[project_id]',Yii::$app->user->identity->project_id, $projects,
                ['id'=>'copy-from-project','class'=>'custom-select cz-sidebar-width float-right'])
                ?>
            </div>			
            <div class="col-md-12 mt-10 clearfix" style="margin-top:10px">
                <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
	<div class="col-xl-9 col-lg-12 col-12">
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('Catalouge List'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?= Html::a('Clear Filter','create',['class'=>'pull-right mr-1 mb-1 btn btn-raised btn-outline-info btn-min-width signed-selected"><i class="ft-power mr-2']); ?>
                        <button type="button" url="pipe/default/delete-multiple?model=app\models\TaxonomyValue" class="mb-1 btn btn-raised btn-outline-danger btn-min-width delete-multipe"><i class="fa fa-times"></i> Delete selected</button>
                    </div>
                </div>
            </div>
            <?php
                $searchModel = new app\models\TaxonomyValueSearch();
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


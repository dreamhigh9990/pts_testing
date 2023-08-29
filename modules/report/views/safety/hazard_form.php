<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
$this->title = "Hazard Report";
$List = Yii::$app->general->employeeList("");	
$Username =  !empty($List[$model->created_by])?$List[$model->created_by]:$List[Yii::$app->user->id];

if($model->isNewRecord && !empty($_COOKIE['hazard_location'])){
    $model->location = $_COOKIE['hazard_location'];
}
if($model->isNewRecord && !empty($_COOKIE['hazard_crew'])){
    $model->crew = $_COOKIE['hazard_crew'];
}
if($model->isNewRecord && !empty($_COOKIE['hazard_name'])){
    $model->name = $_COOKIE['hazard_name'];
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
<style>
.tdborder td , .tdborder th{
    border:1px solid !important;
}
</style>
<div class="left-sideDiv bgsm-side left-table" style="width:55%">
            <div class="col-xl-12 col-lg-12 col-12 p-r-5"  >
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('Hazard Report'); ?>
                    <?php if(!Yii::$app->general->isAllowed()){?>
                        <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['hazard'],['class'=>'pull-right white']);?>                            
                    <?php } ?>
                </h4>
            </div> 
            <?php if($model->id){ ?>
            <button class="btn btn-raised btn-white btn-min-width mr-1 mb-1 black pull-right" onclick="printDiv();">Print Report</button>
            <?php } ?>
            <div id="print-body">          
            <?php
                $form = ActiveForm::begin([
                    'id'=>'hazard-form',
                    'fieldConfig' => [
                        'template' => "<div class='col-md-6 col-sm-6 clearfix'>{label}{input}{error}</div>",
                    ],
					'options' => ['enctype' => 'multipart/form-data','autocomplete'=>'off']
             ]);             
                if($model->id){
                   
                }else{
                    $model->date_time = date('Y-m-d h:i:s');
                }
                ?>	
             
                <?= $form->field($model, 'name')->textInput(); ?>
            
                <div class="form-group field-safetyslam-date_time" style="margin-bottom:15px">
                    <div class="col-md-6 col-sm-6 clearfix">
                        <label class="control-label" for="safetyslam-name"><?= Yii::$app->trans->getTrans('Project / Division'); ?></label>
                        <input type="text" id="safetyslam-name" class="form-control" value="<?=$Username; ?>" readOnly>               
                    </div>
                </div>
                <?= $form->field($model, 'date_time')->textInput(['disabled'=>'disabled']); ?>
                
                <?= $form->field($model, 'location')->dropDownList(Yii::$app->general->TaxonomyDrop(2),['prompt' => Yii::$app->trans->getTrans('Please Select'), 'disabled' => Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'crew')->dropDownList(Yii::$app->general->TaxonomyDrop(27),['prompt' => Yii::$app->trans->getTrans('Please Select'), 'disabled' => Yii::$app->general->isAllowed()]) ?>


                <?= $form->field($model, 'details',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])
                ->textArea(); ?>
                <?= $form->field($model, 'action',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])
                ->textArea(); ?>
                <?= $form->field($model, 'supervisor_in_charged')->dropDownList(['Yes' => Yii::$app->trans->getTrans('Yes'), 'No' => Yii::$app->trans->getTrans('No')],['prompt' => Yii::$app->trans->getTrans('Please Select')]) ?>
                <?= $form->field($model, 'is_followup')->dropDownList(['Yes' => Yii::$app->trans->getTrans('Yes'), 'No' => Yii::$app->trans->getTrans('No')],['prompt' => Yii::$app->trans->getTrans('Please Select')]) ?>
                
                <div class="col-md-12 clearfix">
                    <?php if ($model->isNewRecord) { ?>
                        <?= Html::submitButton(Yii::t('app', Yii::$app->trans->getTrans('Save')), ['class' => 'btn btn-success']) ?>
                    <?php } ?>
                </div>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    </div>
    <div class="width-bigSm bgsm-side right-table" style="width:40%">
        <div class="card-body card"> 
             <div class="card-header">
                <div class="pipe-listbarIcon">
                    <a href="#" class="add-remove"><i class="fa fa-bars fa-lg"></i></a>
                </div>
                <h4 class="card-title "><?= Yii::$app->trans->getTrans('List of Hazard Report'); ?></h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                    <?php
                    $html = Html::a('<i class="fa fa-filter"></i> '.Yii::$app->trans->getTrans('Clear Filter'),'hazard',['class'=>'pull-right mr-1 mb-1 btn btn-raised btn-outline-info btn-min-width']);
                    
                      
                    $html .='<button type="button" url="pipe/default/delete-multiple?model=\app\models\Hazard"  class="mr-1 mb-1 btn btn-raised btn-outline-danger btn-min-width delete-multipe"><i class="fa fa-times"></i> '.Yii::$app->trans->getTrans('Delete selected').'</button>';
                    $html .= Html::a('<i class="fa fa-download"></i> '.Yii::$app->trans->getTrans('Export CSV'),['export-hazard'],['target'=>'_blank','data-pjax'=>0,'class'=>'pull-right mr-1 mb-1 btn btn-raised btn-outline-warning btn-min-width']);
                    
                    
                    echo $html;
                    ?>    
                    </div>
                </div>
            </div>
            <?php 
                $searchModel = new app\models\HazardSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                "filterSelector" => "select[name='per-page']",
                'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
                'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
                'emptyText' => Yii::$app->trans->getTrans('No results found.'),
                'columns' => [      
                ['class' => 'yii\grid\CheckboxColumn'],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{update}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            $url = Url::to(['/report/safety/hazard', 'EditId' => $model->id]);
                            if(!empty($_GET)){
                                $urlParams = Yii::$app->general->getFilters();
                                $urlParams[] = '/report/safety/hazard';
                                $urlParams['EditId'] = $model->id;
                                $url = Url::to($urlParams);
                            }
                            return $this->render('//partials/_clientView', ['url'=>$url, 'created_by' => $model->created_by]);
                        
                        },
                    ],
                ],
                [
                    'attribute' => 'date_time',
                    'format' => 'raw',
                    'label' => Yii::$app->trans->getTrans("Date"),				
                    'filter' => false
                ],         
                [
                    'attribute' => 'location',
                    'filter' => Html::activeDropDownList($searchModel,'location',Yii::$app->general->TaxonomyDrop(2),['class'=>'form-control js-example-basic-multiple form-control','multiple'=>true]),				
                ],	
                [
                    'attribute' => 'created_by',
                    'label' => Yii::$app->trans->getTrans("User"),
                    'filter' =>Yii::$app->general->employeeList(""),
                    'value' => function ($model) {
                        $List = Yii::$app->general->employeeList("");	
                        return !empty($List[$model->created_by])?$List[$model->created_by]:"";
                    },
                ],
            ],
        ]);
        
        ?>
        </div>
    </div>
</div>
</div>
<?php Pjax::end(); ?>
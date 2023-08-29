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



if($model->isNewRecord && !empty($_COOKIE['slam_location'])){
    $model->location = $_COOKIE['slam_location'];
}
if($model->isNewRecord && !empty($_COOKIE['slam_crew'])){
    $model->crew = $_COOKIE['slam_crew'];
}
if($model->isNewRecord && !empty($_COOKIE['slam_name'])){
    $model->name = $_COOKIE['slam_name'];
}



$this->title = "Slam Report";
$PotentilHazard = array_reverse(Yii::$app->general->TaxonomyDrop(29));
$d = json_decode($model->potential_hazards,true);
if(!empty($d)){
    foreach($d as $e){
        $n[$e['question']]['ans'] = $e['ans'];
        $n[$e['question']]['action'] = $e['sub']['q'];
        $n[$e['question']]['action_ans'] = $e['sub']['ans'];
    }
  //  print_r($n);die;
}else{
    $d = array();
}
$List = Yii::$app->general->employeeList("");	
$Username =  !empty($List[$model->created_by])?$List[$model->created_by]:$List[Yii::$app->user->id];
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="row">
<style>
.tdborder td , .tdborder th{
    border:1px solid !important;
}
</style>
<div class="left-sideDiv bgsm-side left-table" style="width:50%">
            <div class="col-xl-12 col-lg-12 col-12 p-r-5"  >
        <div class="card-body card"> 
            <div class="card-header">
                <h4 class="card-title mb-0"><?= Yii::$app->trans->getTrans('SLAM report'); ?>
                        <?php if(!Yii::$app->general->isAllowed()){?>
                            <?=Html::a('<i class="fa fa-plus fa-lg"></i>',['slam'],['class'=>'pull-right white']);?>                            
                        <?php } ?>
                </h4>
            </div> 
            <?php if($model->id){ ?>
            <button class="btn btn-raised btn-white btn-min-width mr-1 mb-1 black pull-right" onclick="printDiv();">Print Report</button>
            <?php } ?>
            <div id="print-body">          
            <?php 
            	
                $form = ActiveForm::begin([
                    'id'=>'slam-form',
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
                        <input type="text" id="safetyslam-name" class="form-control" value="<?=$Username; ?>" disabled="disabled">               
                    </div>
                </div>
                <?= $form->field($model, 'date_time')->textInput(['disabled'=>'disabled']); ?>
                <?= $form->field($model, 'location')->dropDownList(Yii::$app->general->TaxonomyDrop(2),['prompt' => Yii::$app->trans->getTrans('Please Select'), 'disabled' => Yii::$app->general->isAllowed()]) ?>
                <?= $form->field($model, 'crew')->dropDownList(Yii::$app->general->TaxonomyDrop(27),['prompt' => Yii::$app->trans->getTrans('Please Select'), 'disabled' => Yii::$app->general->isAllowed()]) ?>


               <?= $form->field($model, 'task',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])
                ->textArea(); ?>
                 <div class="col-md-12 clearfix">
                        <table class="table tdborder">
                            <thead>
                                <tr style="background: #146c80;color: #fff;">
                                    <th class=""> <?= Yii::$app->trans->getTrans('Look For The Following Potential Hazards'); ?> </th>
                                    <th style="width:15%"><?= Yii::$app->trans->getTrans('Yes / No'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $i=0;
                                foreach($PotentilHazard as $q){
                                    // $model->potential_hazards[$i]['sub']['q']   = !empty($d[$i]) && !empty($d[$i]['sub']['q'])?$d[$i]['sub']['q']:"";
                                    // $model->potential_hazards[$i]['sub']['ans'] = !empty($d[$i]) && !empty($d[$i]['sub']['ans'])?$d[$i]['sub']['ans']:"";
                                    // $model->potential_hazards[$i]['ans']        = !empty($d[$i]) && !empty($d[$i]['ans'])?$d[$i]['ans']:"";
                                ?>
                                <tr>
                                    <td>
                                        <b><?= $i+1;?>.) <?= $q;?> </b>
                                            <div class="sub-holder" style="display:<?= !empty($n[$q]) && !empty($n[$q]['ans']) && $n[$q]['ans']=="Yes"?"block":"none";?>;">
                                                <table class="table" style="margin-top:15px;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Actions Taken </th>
                                                            <th class="text-center">Is it managed</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-center">
                                                                <?= $form->field($model, 'potential_hazards['.$i.'][sub][q]',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textArea(['class'=>'q form-control',
                                                                'value' => !empty($n[$q]) && !empty($n[$q]['action'])?$n[$q]['action']:""])->label(false); ?>
                                                            </td>
                                                            <td class="text-center">
                                                            <?= $form->field($model, 'potential_hazards['.$i.'][sub][ans]',
                                                            ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])
                                                            ->dropDownList(['Yes' => Yii::$app->trans->getTrans('Yes'), 'No' => Yii::$app->trans->getTrans('No')],['class'=>'ans form-control','prompt'=>'','value' => !empty($n[$q]) && !empty($n[$q]['action_ans'])?$n[$q]['action_ans']:""])->label(false); ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                    </td>
                                    <td>
                                        <?= $form->field($model, 'potential_hazards['.$i.'][question]')->hiddenInput(['value' => $q])->label(false);?>
                                        <?= $form->field($model, 'potential_hazards['.$i.'][ans]',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->dropDownList(['Yes' => Yii::$app->trans->getTrans('Yes'), 'No' => Yii::$app->trans->getTrans('No')],
                                        ['prompt'=>'','class'=>'mainans','value' => !empty($n[$q]) && !empty($n[$q]['ans'])?$n[$q]['ans']:""])->label(false); ?>
                                    </td>
                                </tr>  
                            <?php 
                                $i++;
                            }  ?>

                            </tbody>
                        </table>
                    </div>
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
    <div class="width-bigSm bgsm-side right-table" style="width:46%">
        <div class="card-body card"> 
             <div class="card-header">
             <div class="pipe-listbarIcon">
                            <a href="#" class="add-remove"><i class="fa fa-bars fa-lg"></i></a>
                        </div>
                <h4 class="card-title"><?= Yii::$app->trans->getTrans('List of Slam Report'); ?></h4>
            </div>
             <div class="row">
                <div class="col-12">
                    <div class="form-group">
                    <?php
                    $html =Html::a('<i class="fa fa-filter"></i> '.Yii::$app->trans->getTrans('Clear Filter'),'slam',['class'=>'pull-right mr-1 mb-1 btn btn-raised btn-outline-info btn-min-width']);
                    
                      
                    $html .='<button type="button" url="pipe/default/delete-multiple?model=\app\models\SafetySlam"  class="mr-1 mb-1 btn btn-raised btn-outline-danger btn-min-width delete-multipe"><i class="fa fa-times"></i> '.Yii::$app->trans->getTrans('Delete selected').'</button>';
                    $html .= Html::a('<i class="fa fa-download"></i> '.Yii::$app->trans->getTrans('Export CSV'),['export-slam'],['target'=>'_blank','data-pjax'=>0,'class'=>'pull-right mr-1 mb-1 btn btn-raised btn-outline-warning btn-min-width']);
                    
                    
                    
                    echo $html;
                    ?>    
                    </div>
                </div>
            </div>
            <?php 
                $searchModel = new app\models\SafetySearch();
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
                            $url = Url::to(['/report/safety/slam', 'EditId' => $model->id]);
                            if(!empty($_GET)){
                                $urlParams = Yii::$app->general->getFilters();
                                $urlParams[] = '/report/safety/slam';
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
<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
$view='';   
// $model='PipeSearch';
$this->title = 'Review Summary';
$models = [
    'Reception' => '\app\models\Reception',
    'Cleargrade' =>'\app\models\Cleargrade',
    'Stringing' =>'\app\models\Stringing',
    'PipeTransfer' => '\app\models\PipeTransfer',
    'Bending' => '\app\models\Bending',
    'Cutting' => '\app\models\Cutting',
    'Welding' => '\app\models\Welding',
    'Parameter' => '\app\models\Parameter',
    'Ndt' => '\app\models\Ndt',
    'Weldingrepair' => '\app\models\Weldingrepair',
    'Production' => '\app\models\Production',
    'Coatingrepair' => '\app\models\Coatingrepair',
    'Trenching' =>'\app\models\Trenching',
    'Lowering' => '\app\models\Lowering',
    'Backfilling' => '\app\models\Backfilling',
    'Reinstatement' => '\app\models\Reinstatement',
    'Cathodicprotection' => '\app\models\Cathodicprotection',
    'Cleangauge' => '\app\models\Cleangauge',
    'Hydrotesting' => '\app\models\Hydrotesting',
    'Surveying' => '\app\models\Surveying',
    'CabStringing' => '\app\models\CabStringing',
    'CabSplicing' => '\app\models\CabSplicing',
];

foreach($models as $key=>$value){  
    $count = $value::find()->where(['signed_off'=>"No"])->active()->count();
    $Data[$key] = $count != 0 ? '<span class="tag badge badge-pill badge-danger custom-badge pull-right ml-3">'.$count.'</span>' : '';
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="col-xl-12 col-lg-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                <?= Yii::$app->trans->getTrans('Review Summary'); ?>
                <?= Html::a('<i class="fa fa-arrow-left"></i> '.Yii::$app->trans->getTrans('Go Back'),['/report/report/index','model'=>'PipeSearch'],['class'=>'pull-right white']);?> 
            </h4>
        </div>   
        <div class="card-body">
            <div class="card-block">
                <div class="nav-vertical">
                    <ul class="nav nav-tabs nav-left flex-column custom-side-tab">
                        <li class="<?= ($model == "ReceptionSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "ReceptionSearch") ? $view = "ReceptionSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Reception').'</span> '.$Data['Reception'],['/report/report/review-report','model'=>'ReceptionSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "CleargradeSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "CleargradeSearch") ? $view = "CleargradeSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Clear & Grade').'</span> '.$Data['Cleargrade'],['/report/report/review-report','model'=>'CleargradeSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "StringingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "StringingSearch") ? $view = "StringingSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Stringing').'</span> '.$Data['Stringing'],['/report/report/review-report','model'=>'StringingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "PipeTransferSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "PipeTransferSearch") ? $view = "PipeTransferSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Transfer').'</span> '.$Data['PipeTransfer'],['/report/report/review-report','model'=>'PipeTransferSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "BendingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "BendingSearch") ? $view = "BendingSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Bending').'</span> '.$Data['Bending'],['/report/report/review-report','model'=>'BendingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "CuttingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "CuttingSearch") ? $view = "CuttingSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Cutting').'</span> '.$Data['Cutting'],['/report/report/review-report','model'=>'CuttingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "WeldingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "WeldingSearch") ? $view = "WeldingSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Welding').'</span> '.$Data['Welding'],['/report/report/review-report','model'=>'WeldingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "ParameterSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "ParameterSearch") ? $view = "ParameterSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Parameter Check').'</span> '.$Data['Parameter'],['/report/report/review-report','model'=>'ParameterSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "NdtSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "NdtSearch") ? $view = "NdtSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('NDT').'</span> '.$Data['Ndt'],['/report/report/review-report','model'=>'NdtSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "WeldingrepairSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "WeldingrepairSearch") ? $view = "WeldingrepairSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Weld Repair').'</span> '.$Data['Weldingrepair'],['/report/report/review-report','model'=>'WeldingrepairSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "ProductionSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "ProductionSearch") ? $view = "ProductionSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Coating Production').'</span> '.$Data['Production'],['/report/report/review-report','model'=>'ProductionSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "CoatingrepairSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "CoatingrepairSearch") ? $view = "CoatingrepairSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Coating Repair').'</span> '.$Data['Coatingrepair'],['/report/report/review-report','model'=>'CoatingrepairSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "TrenchingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "TrenchingSearch") ? $view = "TrenchingSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Trenching').'</span> '.$Data['Trenching'],['/report/report/review-report','model'=>'TrenchingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "LoweringSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "LoweringSearch") ? $view = "LoweringSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Lowering').'</span> '.$Data['Lowering'],['/report/report/review-report','model'=>'LoweringSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "BackfillingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "BackfillingSearch") ? $view = "BackfillingSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Backfilling').'</span> '.$Data['Backfilling'],['/report/report/review-report','model'=>'BackfillingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "ReinstatementSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "ReinstatementSearch") ? $view = "ReinstatementSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Reinstatement').'</span> '.$Data['Reinstatement'],['/report/report/review-report','model'=>'ReinstatementSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "CathodicprotectionSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "CathodicprotectionSearch") ? $view = "CathodicprotectionSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Cathodic Protection').'</span> '.$Data['Cathodicprotection'],['/report/report/review-report','model'=>'CathodicprotectionSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "CleangaugeSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "CleangaugeSearch") ? $view = "CleangaugeSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Clean Gauge').'</span> '.$Data['Cleangauge'],['/report/report/review-report','model'=>'CleangaugeSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "HydrotestingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "HydrotestingSearch") ? $view = "HydrotestingSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('Hydro Testing').'</span> '.$Data['Hydrotesting'],['/report/report/review-report','model'=>'HydrotestingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "SurveyingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "SurveyingSearch") ? $view = "SurveyingSearch" : "";?>
                            <?= Html::a('<span class="label-main">'.Yii::$app->trans->getTrans('DCVG Surveying').'</span> '.$Data['Surveying'],['/report/report/review-report','model'=>'SurveyingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "CabStringingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "CabStringingSearch") ? $view = "CabStringingSearch" : "";?>
                            <?= Html::a('<span class="label-main">Cable '.Yii::$app->trans->getTrans('Stringing').'</span> '.$Data['CabStringing'],['/report/report/review-report','model'=>'CabStringingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                        <li class="<?= ($model == "CabSplicingSearch") ? $active = "active" : "";?> nav-items">
                            <?php ($model == "CabSplicingSearch") ? $view = "CabSplicingSearch" : "";?>
                            <?= Html::a('<span class="label-main">Cable '.Yii::$app->trans->getTrans('Splicing').'</span> '.$Data['CabSplicing'],['/report/report/review-report','model'=>'CabSplicingSearch'],['class'=>'nav-link success ']);?>
                        </li>
                    </ul>
                    <div class="tab-content px-1">
                        <div role="tabpanel" class="tab-pane none-side active" id="Pipe">
                            <?php                   
                                $searchModel ='\\app\models\\'.$view;
                                $searchModel = new $searchModel;
                                $searchModel->signed_off='No';
                                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                                echo $this->render('reviewsummary/_'.$model, [
                                    'searchModel' => $searchModel,
                                    'dataProvider' => $dataProvider,
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Menu;
$models = [
    'Pipe' => '\app\models\Pipe',
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
    'Cleangauge' => '\app\models\Cleangauge',
    'Hydrotesting' => '\app\models\Hydrotesting',
    'Surveying' => '\app\models\Surveying',
    'Cable' => '\app\models\Cable',
    'CabStringing' => '\app\models\CabStringing',
    'CabSplicing' => '\app\models\CabSplicing',
];

foreach($models as $key=>$value){  
    $c = $value::find()->anomally()->count();
    $Data[$key] =  $c==0?"":'<span class="tag badge badge-pill badge-danger custom-badge">'.$c.'</span>';
}
?>
<?= Menu::widget([
    'options' => [
        'class' => 'nav nav-tabs my-ul',
    ],
    'itemOptions'=>[
        'class' => 'nav-item',
    ],
    'encodeLabels' => false,
    'items' => [						
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Pipe').'</span> '.$Data['Pipe'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'PipeSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Reception').'</span> '.$Data['Reception'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'ReceptionSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Clear & Grade').'</span> '.$Data['Cleargrade'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'CleargradeSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Stringing').'</span> '.$Data['Stringing'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'StringingSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Transfer').'</span> '.$Data['PipeTransfer'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'PipeTransferSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Bending').'</span> '.$Data['Bending'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'BendingSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Cutting').'</span> '.$Data['Cutting'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'CuttingSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Welding').'</span> '.$Data['Welding'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'WeldingSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Parameter Check').'</span> '.$Data['Parameter'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'ParameterSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('NDT').'</span> '.$Data['Ndt'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'NdtSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Weld Repair').'</span> '.$Data['Weldingrepair'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'WeldingrepairSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Coating Production').'</span> '.$Data['Production'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'ProductionSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Coating Repair').'</span> '.$Data['Coatingrepair'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'CoatingrepairSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Trenching').'</span> '.$Data['Trenching'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'TrenchingSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Lowering').'</span> '.$Data['Lowering'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'LoweringSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Backfilling').'</span> '.$Data['Backfilling'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'BackfillingSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Reinstatement').'</span> '.$Data['Reinstatement'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'ReinstatementSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Clean Gauge').'</span> '.$Data['Cleangauge'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'CleangaugeSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Hydro Testing').'</span> '.$Data['Hydrotesting'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'HydrotestingSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('DCVG Surveying').'</span> '.$Data['Surveying'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'SurveyingSearch']],
        ['label' => '<span class="label-main">'.Yii::$app->trans->getTrans('Cable').'</span> '.$Data['Cable'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'CableSearch']],
        ['label' => '<span class="label-main">Cable '.Yii::$app->trans->getTrans('Stringing').'</span> '.$Data['CabStringing'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'CabStringingSearch']],
        ['label' => '<span class="label-main">Cable '.Yii::$app->trans->getTrans('Splicing').'</span> '.$Data['CabSplicing'], 'options'=> ['class'=>''], 'url' => ['/admin/anomaly/index','model'=>'CabSplicingSearch']],
    ],
]);
?>

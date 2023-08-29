<?php
use yii\widgets\Pjax;
use miloschuman\highcharts\Highcharts;

$defects = Yii::$app->general->TaxonomyDrop(9);
unset($defects['None']);
$defLeg = $defects;

$label = '';
$rData = '';
$dateArray = array();

$mWeldArray = array();
$mNdtArray = array();
$mRepairArray = array();

$tWeldArray = array();
$tNdtArray = array();
$tRepairArray = array();

$defectGraphArray = array();
// $defectArray = array('AS','LP','LR','LS','LI','SRC','BT','IL','WT','IN','WH','GP','PU','PG','HB','C','SUC','SGI');
$defectArray = $defects;
$pieces = !empty($defectArray) ? array_chunk($defectArray, ceil(count($defectArray) / 2)) : array();
$defectLeft = array();
$defectRight = array();

if(!empty($pieces) && !empty($pieces[0]) && !empty($pieces[1])){
    $defectLeft = $pieces[0];
    $defectRight = $pieces[1];
}

$defectArrayLegend = array_values($defLeg);
    // Weld All
    $filterType = !empty($filterType) ? $filterType : "";
    $dateRange = !empty($dateRange) ? $dateRange : "";
    $weldAllData = Yii::$app->general->getWeldAll($filterType, $dateRange);
    // print_r($weldAllData);die;
    $MianLineWeldAll = (float)$weldAllData['Main_Line']['total_weld'];
    $TieLineWeldAll = (float)$weldAllData['Tie_Line']['total_weld'];;
    $totalWeld = $MianLineWeldAll+$TieLineWeldAll;
    $def = $weldAllData['defect'];
    // Weld Repair All
    $MianLineWeldRepairAll = (float)$weldAllData['Main_Line']['total_repair'];
    $TieLineWeldRepairAll =(float)$weldAllData['Tie_Line']['total_repair'];
    $totalRepair = $MianLineWeldRepairAll+$TieLineWeldRepairAll;    

    // Weld Cut Out All
    $weldCutAllData = Yii::$app->general->getWeldCutAll($filterType, $dateRange);
    $MianLineCutAll = $weldCutAllData['MianLineCut'];
    $TieLineCutAll = $weldCutAllData['TieLineCut'];
    $totalCutOut = $MianLineCutAll+$TieLineCutAll;

    // Weld NDT All
    $weldNdtAllData = Yii::$app->general->getWeldNdtAll($filterType, $dateRange);
    $MianLineNdtAll = $weldNdtAllData['MianLineNdt'];
    $TieLineNdtAll = $weldNdtAllData['TieLineNdt'];
    $totalNdt = $MianLineNdtAll+$TieLineNdtAll;

    // Weld Repair Rate All
    $Per_MianLineWeldAll =  number_format($MianLineNdtAll > 0 ? $MianLineWeldRepairAll*100/$MianLineNdtAll :0,2);
    $Per_TieLineWeldAll  =  number_format($TieLineNdtAll > 0 ? $TieLineWeldRepairAll*100/$TieLineNdtAll:0,2);
    $totalRepairRate = number_format($totalNdt > 0 ? ($totalRepair) * 100 / $totalNdt : 0,2);

    // Weld Coated All
    $weldCoatedAllData = Yii::$app->general->getWeldCoatedAll($filterType, $dateRange);
    $MianLineCoatedAll = $weldCoatedAllData['MianLineCoated'];
    $TieLineCoatedAll = $weldCoatedAllData['TieLineCoated'];
    $totalCoating = $MianLineCoatedAll+$TieLineCoatedAll;
    $defctName      = [];
    $defRepairRate  = [];
    if(!empty($def)){
        foreach($def as $k=>$v){
            array_push($defctName,$k);
            array_push($defRepairRate,(float)number_format(($v/$totalRepair)*100, 2));
        }
    }
?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="table-Approval-main" id="print-body">
    <div class="table-summary">
        <div class="clearfix">
            <div class="logo-appro">
                <?= \yii\helpers\Html::img(Yii::$app->general->logo(), ['alt'=>'some', 'class'=>'appro-logo']);?>
                </div>
                <div class="appro-title">
                    <?php if($filterType == "all"){ $d = date('d-M-y');?>
                        <h1><?= Yii::$app->trans->getTrans('OverAll Summary Report'); ?></h1>
                    <?php } ?>
                    <?php if($filterType != "all"){ $d = $dateRange;?>
                        <h1><?= Yii::$app->trans->getTrans("Summary Report for").' '.$dateRange;?></h1>
                    <?php } ?>
                </div>
            </div>
            <div class="title-blueBG"></div>

            <div class="clearfix">
                <div class="col-md-6 col-print-6">
                    <div class="ntd-table-title clearfix">
                        <span class="text-center"><?= Yii::$app->trans->getTrans('Weld Production / Repair / NDT Summary'); ?></span>
                    </div>
                    <div class="ntd-summary-table">
                        <table class="table table-selectiondetail performed">
                            <thead>
                                <tr>
                                    <th><?= Yii::$app->trans->getTrans('Item'); ?></th>
                                    <th><?= Yii::$app->trans->getTrans('Mainline'); ?></th>
                                    <th><?= Yii::$app->trans->getTrans('Tie-In'); ?></th>
                                    <th><?= Yii::$app->trans->getTrans('Total'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th><?= Yii::$app->trans->getTrans('Overall Welds To Date'); ?></th>
                                    <td><?php echo $MianLineWeldAll; ?></td>
                                    <td><?php echo $TieLineWeldAll; ?></td>
                                    <td><?php echo $totalWeld; ?></td>
                                </tr>
                                <tr>
                                    <th><?= Yii::$app->trans->getTrans('Overall Repairs To Date'); ?></th>
                                    <td><?php echo $MianLineWeldRepairAll; ?></td>
                                    <td><?php echo $TieLineWeldRepairAll; ?></td>
                                    <td><?php echo $totalRepair; ?></td>
                                </tr>
                                <tr>
                                    <th><?= Yii::$app->trans->getTrans('% Repair Rate Overall'); ?></th>
                                    <td><?php echo $Per_MianLineWeldAll.' %'; ?></td>
                                    <td><?php echo $Per_TieLineWeldAll.' %'; ?></td>
                                    <td><?php echo $totalRepairRate.' %'; ?></td>
                                </tr>
                                <tr>
                                    <th><?= Yii::$app->trans->getTrans('Welds Cut Out Overall'); ?></th>
                                    <td><?php echo $MianLineCutAll; ?></td>
                                    <td><?php echo $TieLineCutAll; ?></td>
                                    <td><?php echo $totalCutOut; ?></td>
                                </tr>
                                <tr>
                                    <th><?= Yii::$app->trans->getTrans('Welds NDT Overall'); ?></th>
                                    <td><?php echo $MianLineNdtAll; ?></td>
                                    <td><?php echo $TieLineNdtAll; ?></td>
                                    <td><?php echo $totalNdt; ?></td>
                                </tr>
                                <tr>
                                    <th><?= Yii::$app->trans->getTrans('Coating Overall'); ?></th>
                                    <td><?php echo $MianLineCoatedAll; ?></td>
                                    <td><?php echo $TieLineCoatedAll; ?></td>
                                    <td><?php echo $totalCoating; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 col-print-6">
                    <div class="ntd-table-title clearfix">
                        <span class="text-center"><?= '<span class="mr-3"> '.Yii::$app->trans->getTrans('Up to Date').' </span>'.$d; ?></span>
                    </div>
                    <div class="ntd-summary-table">
                        <table class="table table-selectiondetail performed">
                            <thead>
                                <tr>
                                    <th colspan="4"><?= Yii::$app->trans->getTrans('General Information'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="clearfix defect-type-border">
                <div class="col-md-12">
                    <div class="ntd-table-title clearfix">
                        <span class="text-center"><?= Yii::$app->trans->getTrans('Overall Repair By Defect Type'); ?></span>
                    </div>
                </div>
            </div>

            <div class="clearfix">
                <div class="col-md-6 col-print-6">
                    <div class="ntd-summary-table">
                        <table class="table table-selectiondetail performed">
                            <thead>
                                <tr>
                                    <th><?= Yii::$app->trans->getTrans('Defect'); ?></th>
                                    <th><?= Yii::$app->trans->getTrans('Qty'); ?></th>
                                    <th><?= Yii::$app->trans->getTrans('% of Repair Rate'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($defectLeft as $defect){ ?>
                                <tr>
                                    <th><?php echo $defect; ?></th>
                                    <td>
                                        <?php                                          
                                            $r = isset($def[$defect])?$def[$defect]:0;
                                            echo $r;
                                            //  $defData = Yii::$app->general->getRepairByDefectType($defect);
                                            //  echo $defData;
                                            //  $defDataRepair = $totalRepair > 0 ? ($defData / $totalRepair) * 100 : 0;
                                            //  array_push($defectGraphArray,$defDataRepair);
                                        ?>
                                    </td>
                                    <td><?php echo !empty($totalRepair)?number_format(($r/$totalRepair)*100,2).'%':"0%";?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 col-print-6">
                    <div class="ntd-summary-table">
                        <table class="table table-selectiondetail performed">
                            <thead>
                                <tr>
                                    <th><?= Yii::$app->trans->getTrans('Defect'); ?></th>
                                    <th><?= Yii::$app->trans->getTrans('Qty'); ?></th>
                                    <th><?= Yii::$app->trans->getTrans('% of Repair Rate'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($defectRight as $defect){ ?>
                                <tr>
                                    <th><?php echo $defect; ?></th>
                                    <td>
                                        <?php
                                               $r = isset($def[$defect])?$def[$defect]:0;
                                               echo $r;
                                        ?>
                                    </td>
                                    <td><?php echo !empty($totalRepair)?number_format(($r/$totalRepair)*100,2).'%':"0%";?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- <?php if(!empty($defectArray)){ ?>
            <div class="clearfix">
                <div class="col-md-12">
                    <div class="ntd-summary-table">
                        <?php foreach($defectArray as $sDef){ ?>
                        <span><?php echo $sDef; ?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?> -->
            
            <div class="title-blueBG">
                <?php
                
                    echo Highcharts::widget([
                        'options' => [
                            'title' => ['text' => Yii::$app->trans->getTrans('Repair Report by Defect')],
                            'chart' => ['type' => 'column'],
                            'xAxis' => [
                                'categories' => $defctName
                            ],
                            'yAxis' => [
                                'title' => ['text' => '% Repair'],
                                'labels' => [
                                    'format' => '{value} %',
                                ],
                            ],
                            'series' => [
                                [
                                    'name' => 'Defects',
                                    'data' => $defRepairRate,
                                    'tooltip' => [
                                        'valueSuffix' => ' %'
                                    ]
                                ],
                            ],
                            'credits' => ['enabled' => false],
                        ]
                    ]);
                ?>
            </div>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
<?php
use yii\widgets\Pjax;
use miloschuman\highcharts\Highcharts;
$rData = '';
$dateLabel = '';
$data = array();
$dateArray = array();

$mWeldArray = array();
$mNdtArray = array();
$mRepairArray = array();

$tWeldArray = array();
$tNdtArray = array();
$tRepairArray = array();

if($filterType == "daily") {
    $rData = !empty($dateRange) ? date('Y-m-d',strtotime($dateRange)) : date('Y-m-d');
    $dateLabel = date('d-M-y',strtotime($rData));

    $weekAgoDate = date('Y-m-d',strtotime('-7 days',strtotime($rData)));

    $period = new DatePeriod(
        new DateTime($weekAgoDate),
        new DateInterval('P1D'),
        new DateTime($rData." 00:01")
    );

    foreach ($period as $key => $value) {
        $dateArray[$key] = $value->format('Y-m-d');
    }

    if(!empty($dateArray)){
        foreach($dateArray as $date){
            //welding
            $weldData = Yii::$app->general->getWeldDaily($date);
            // $MianLineWeldAll = (float)$weldAllData['Main_Line']['total_weld'];
            // $TieLineWeldAll = (float)$weldAllData['Tie_Line']['total_weld'];;
            array_push($mWeldArray,(float)(float)$weldAllData['Main_Line']['total_weld']);
            array_push($tWeldArray,(float)$weldAllData['Tie_Line']['total_weld']);

            //ndt
            $ndtData = Yii::$app->general->getWeldNdtToday($date);
            array_push($mNdtArray,(float)$ndtData['MianLineNdt']);
            array_push($tNdtArray,(float)$ndtData['TieLineNdt']);

            //repair
            $weldRepairData = Yii::$app->general->getWeldRepairDaily($date);
            // array_push($mRepairArray,(float)$weldRepairData['MianLineWeldRepair']);
            // array_push($tRepairArray,(float)$weldRepairData['TieLineWeldRepair']);
            array_push($mRepairArray,($ndtData['MianLineNdt'] > 0 ? (float)$weldRepairData['MianLineWeldRepair'] * 100 / (float)$ndtData['MianLineNdt'] : 0));
            array_push($tRepairArray,($ndtData['TieLineNdt'] > 0 ? (float)$weldRepairData['TieLineWeldRepair'] * 100 / (float)$ndtData['TieLineNdt'] : 0));
        }
    }
    /*-----------------Weld--------------------*/
    //Today
    $weldTodayData = Yii::$app->general->getWeldDaily($rData);
    $mainLineWeld = $weldTodayData['MianLineWeld'];
    $tieLineWeld = $weldTodayData['TieLineWeld'];

    //All
    $weldAllData = Yii::$app->general->getWeldAll();
    $mainLineWeldAll = $weldAllData['MianLineWeld'];
    $tieLineWeldAll = $weldAllData['TieLineWeld'];
    /*----------------------------------------*/

    /*-----------------Repair------------------*/
    //Today
    $weldRepairTodayData = Yii::$app->general->getWeldRepairDaily($rData);
    $mainLineRepair = $weldRepairTodayData['MianLineWeldRepair'];
    $tieLineRepair = $weldRepairTodayData['TieLineWeldRepair'];
    $totalRepairToday = $mainLineRepair + $tieLineRepair;

    //All
    $weldRepairAllData = Yii::$app->general->getWeldRepairAll();
    $mainLineRepairAll = $weldRepairAllData['MianLineWeldRepair'];
    $tieLineRepairAll = $weldRepairAllData['TieLineWeldRepair'];
    $totalRepairAll = $mainLineRepairAll + $tieLineRepairAll;
    /*----------------------------------------*/

    /*-----------------NDT------------------*/
    //Today
    $weldNdtData = Yii::$app->general->getWeldNdtToday($rData);
    $mainLineNdt = $weldNdtData['MianLineNdt'];
    $tieLineNdt = $weldNdtData['TieLineNdt'];
    $totalNdtToday = $mainLineNdt + $tieLineNdt;

    //All
    $weldNdtAllData = Yii::$app->general->getWeldNdtAll();
    $mainLineNdtAll = $weldNdtAllData['MianLineNdt'];
    $tieLineNdtAll = $weldNdtAllData['TieLineNdt'];
    $totalNdtAll = $mainLineNdtAll + $tieLineNdtAll;
    /*----------------------------------------*/

    // Weld Repair Rate Today
    $mainLineRate =  number_format($mainLineNdt > 0 ? $mainLineRepair * 100 / $mainLineNdt : 0,2);
    $tieLineRate  =  number_format($tieLineNdt > 0 ? $tieLineRepair * 100 / $tieLineNdt : 0,2);
    $totalTodayRate = $totalNdtToday > 0 ? $totalRepairToday * 100 / $totalNdtToday : 0;

    // Weld Repair Rate All
    $mainLineRateAll =  number_format($mainLineNdtAll > 0 ? $mainLineRepairAll * 100 / $mainLineNdtAll : 0,2);
    $tieLineRateAll  =  number_format($tieLineNdtAll > 0 ? $tieLineRepairAll * 100 / $tieLineNdtAll : 0,2);
    $totalAllRate = $totalNdtAll > 0 ? $totalRepairAll * 100 / $totalNdtAll : 0;

    /*-----------------Cut------------------*/
    //Today
    $weldCutData = Yii::$app->general->getWeldCutToday($rData);
    $mainLineCut = $weldCutData['MianLineCut'];
    $tieLineCut = $weldCutData['TieLineCut'];
    $totalCutToday = $mainLineCut + $tieLineCut;

    //All
    $weldCutAllData = Yii::$app->general->getWeldCutAll();
    $mainLineCutAll = $weldCutAllData['MianLineCut'];
    $tieLineCutAll = $weldCutAllData['TieLineCut'];
    $totalCutAll = $mainLineCutAll + $tieLineCutAll;
    /*----------------------------------------*/

    /*-----------------Coated------------------*/
    //Today
    $weldCoatedData = Yii::$app->general->getWeldCoatedToday($rData);
    $mainLineCoated = $weldCoatedData['MianLineCoated'];
    $tieLineCoated = $weldCoatedData['TieLineCoated'];
    $totalCoatedToday = $mainLineCoated + $tieLineCoated;

    //All
    $weldCoatedAllData = Yii::$app->general->getWeldCoatedAll();
    $mainLineCoatedAll = $weldCoatedAllData['MianLineCoated'];
    $tieLineCoatedAll = $weldCoatedAllData['TieLineCoated'];
    $totalCoatedAll = $mainLineCoatedAll + $tieLineCoatedAll;
    /*----------------------------------------*/
}
?>
<?php Pjax::begin(['id'=>'idofpjaxcontainer']); ?>
<div class="table-Approval-main" id="print-body">
    <div class="table-summary">
        <div class="clearfix">
            <div class="logo-appro">
                <?= \yii\helpers\Html::img(Yii::$app->general->logo(), ['alt'=>'project-logo', 'class'=>'appro-logo']);?>
                </div>
                <div class="appro-title">
                    <h1>PROJECT DAILY WELDING & COATING REPORT</h1>
                </div>
            </div>
            <div class="title-blueBG"></div>
            <div class="ntd-summary-main clearfix">
                <div class="col-md-12">
                    <div class="ntd-table-title clearfix">
                        <span class="pull-left ml-1">REPORT DATE : <?= $dateLabel; ?></span>
                    </div>
                    <div class="ntd-summary-table">
                        <table class="table table-selectiondetail performed">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>Mainline</th>
                                    <th>Tie-In</th>
                                    <th>Total</th>
                                    <th colspan="3">Welding and NDT Information</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($filterType == "daily"){ ?>
                                <!-- Weld Today -->
                                <tr>
                                    <td>Welds Today: </td>
                                    <td><?= $mainLineWeld; ?></td>
                                    <td><?= $tieLineWeld; ?></td>
                                    <td><?= $mainLineWeld + $tieLineWeld;?></td>
                                    <td colspan="3"></td>
                                </tr>
                                <!-- Weld Overall -->
                                <tr>
                                    <td>Overall Welds: </td>
                                    <td><?= $mainLineWeldAll; ?></td>
                                    <td><?= $tieLineWeldAll; ?></td>
                                    <td><?= $mainLineWeldAll + $tieLineWeldAll;?></td>
                                    <td colspan="3"></td>
                                </tr>
                                
                                <!-- Weld Repair Today -->
                                <tr>
                                    <td>Repairs Today: </td>
                                    <td><?= $mainLineRepair; ?></td>
                                    <td><?= $tieLineRepair; ?></td>
                                    <td><?= $totalRepairToday; ?></td>
                                    <td colspan="3"></td>
                                </tr>
                                <!-- Overall Weld Repair -->
                                <tr>
                                    <td>Overall Repairs: </td>
                                    <td><?= $mainLineRepairAll; ?></td>
                                    <td><?= $tieLineRepairAll; ?></td>
                                    <td><?= $totalRepairAll; ?></td>
                                    <td colspan="3"></td>
                                </tr>

                                <!-- Weld Repair Rate Today -->
                                <tr>
                                    <td>% Repair Rate Today: </td>
                                    <td><?= $mainLineRate; ?> %</td>
                                    <td><?= $tieLineRate; ?> %</td>
                                    <td><?= $totalTodayRate; ?> %</td>
                                    <td colspan="3"></td>
                                </tr>
                                <!-- Overall Weld Repair Rate -->
                                <tr>
                                    <td>% Repair Rate Overall: </td>
                                    <td><?= $mainLineRateAll; ?> %</td>
                                    <td><?= $tieLineRateAll; ?> %</td>
                                    <td><?= $totalAllRate; ?> %</td>
                                    <td colspan="3" class="font-bold">Coating Information</td>
                                </tr>

                                <!-- Weld Cut Out Today -->
                                <tr>
                                    <td>Cut Outs Today: </td>
                                    <td><?= $mainLineCut; ?></td>
                                    <td><?= $tieLineCut; ?></td>
                                    <td><?= $totalCutToday; ?></td>
                                    <td colspan="3"></td>
                                </tr>
                                <!-- Weld Cut Out Overall -->
                                <tr>
                                    <td>Cut Outs Overall: </td>
                                    <td><?= $mainLineCutAll; ?></td>
                                    <td><?= $tieLineCutAll; ?></td>
                                    <td><?= $totalCutAll; ?></td>
                                    <td colspan="3"></td>
                                </tr>

                                <!-- Weld NDT Today -->
                                <tr>
                                    <td>Welds NDT Today: </td>
                                    <td><?= $mainLineNdt; ?></td>
                                    <td><?= $tieLineNdt; ?></td>
                                    <td><?= $totalNdtToday; ?></td>
                                    <th colspan="3"></th>
                                </tr>
                                <!-- Weld NDT Overall -->
                                <tr>
                                    <td>Welds NDT Overall: </td>
                                    <td><?= $mainLineNdtAll; ?></td>
                                    <td><?= $tieLineNdtAll; ?></td>
                                    <td><?= $totalNdtAll; ?></td>
                                    <th colspan="3"></th>
                                </tr>

                                <!-- Weld Coated Today -->
                                <tr>
                                    <td>Welds Coated Today: </td>
                                    <td><?= $mainLineCoated; ?></td>
                                    <td><?= $tieLineCoated; ?></td>
                                    <td><?= $totalCoatedToday; ?></td>
                                    <th colspan="3"></th>
                                </tr>
                                <!-- Weld Coated Overall -->
                                <tr>
                                    <td>Welds Coated Overall: </td>
                                    <td><?= $mainLineCoatedAll; ?></td>
                                    <td><?= $tieLineCoatedAll; ?></td>
                                    <td><?= $totalCoatedAll; ?></td>
                                    <th colspan="3"></th>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="title-blueBG">
                <?php
                    if(!empty($dateArray)){
                        //graph for main line
                        echo Highcharts::widget([
                            'options' => [
                                'chart' => [
                                    'zoomType' => 'xy'
                                ],
                                'title' => [
                                    'text' => 'Mainline Weekly Welding Info'
                                ],
                                'xAxis' => [
                                    [
                                        'categories' => $dateArray,
                                        'crosshair' => true
                                    ]
                                ],
                                'yAxis' => [
                                    [
                                        'title' => [
                                            'text' => 'Welding'
                                        ],
                                        'opposite' => true
                                    ],
                                    [
                                        'gridLineWidth' => 0,
                                        'title' => [
                                            'text' => 'NDT',
                                        ],
                                    ],
                                    [
                                        'gridLineWidth' => 0,
                                        'title' => [
                                            'text' => 'Repair',
                                        ],
                                        'opposite' => true
                                    ]
                                ],
                                'tooltip' => [
                                    'shared' => true
                                ],
                                'legend' => [
                                    'layout' => 'vertical',
                                    'align' => 'left',
                                    'x' => 80,
                                    'verticalAlign' => 'top',
                                    'y' => 40,
                                    'floating' => true,
                                    'backgroundColor' => '#FFFFFF'
                                ],
                                'series' => [
                                    [
                                        'name' => 'Welding',
                                        'type' => 'column',
                                        
                                        'data' => $mWeldArray,                                        
                                    ],
                                    [
                                        'name' => 'NDT',
                                        'type' => 'column',
                                        'yAxis' => 1,
                                        'data' => $mNdtArray,
                                        'marker' => [
                                            'enabled' => false
                                        ],                                    
                                    ],
                                    [
                                        'name' => '% Repair',
                                        'type' => 'spline',
                                        'yAxis' => 2,
                                        'data' => $mRepairArray,                                        
                                    ]
                                ],
                                'credits' => ['enabled' => false],
                            ]
                        ]);
                        
                        //graph for tie line
                        echo Highcharts::widget([
                            'options' => [
                                'chart' => [
                                    'zoomType' => 'xy'
                                ],
                                'title' => [
                                    'text' => 'Tie In Weekly Welding Info'
                                ],
                                'xAxis' => [
                                    [
                                        'categories' => $dateArray,
                                        'crosshair' => true
                                    ]
                                ],
                                'yAxis' => [
                                    [
                                        'title' => [
                                            'text' => 'Welding'
                                        ],
                                        'opposite' => true
                                    ],
                                    [
                                        'gridLineWidth' => 0,
                                        'title' => [
                                            'text' => 'NDT',
                                        ],
                                    ],
                                    [
                                        'gridLineWidth' => 0,
                                        'title' => [
                                            'text' => 'Repair',
                                        ],
                                        'opposite' => true
                                    ]
                                ],
                                'tooltip' => [
                                    'shared' => true
                                ],
                                'legend' => [
                                    'layout' => 'vertical',
                                    'align' => 'left',
                                    'x' => 80,
                                    'verticalAlign' => 'top',
                                    'y' => 40,
                                    'floating' => true,
                                    'backgroundColor' => '#FFFFFF'
                                ],
                                'series' => [
                                    [
                                        'name' => 'Welding',
                                        'type' => 'column',
                                        
                                        'data' => $tWeldArray,                                        
                                    ],
                                    [
                                        'name' => 'NDT',
                                        'type' => 'column',
                                        'yAxis' => 1,
                                        'data' => $tNdtArray,
                                        'marker' => [
                                            'enabled' => false
                                        ],                                    
                                    ],
                                    [
                                        'name' => 'Repair',
                                        'type' => 'spline',
                                        'yAxis' => 2,
                                        'data' => $tRepairArray,
                                    ]
                                ],
                                'credits' => ['enabled' => false],
                            ]
                        ]);
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
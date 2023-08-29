<?php
use yii\widgets\Pjax;
use miloschuman\highcharts\Highcharts;

$defects = Yii::$app->general->TaxonomyDrop(9);
unset($defects['None']);
$defLeg = $defects;

$rData = '';
$dateArray = array();

$mWeldArray = array();
$mNdtArray = array();
$mRepairArray = array();

$tWeldArray = array();
$tNdtArray = array();
$tRepairArray = array();

$data = array();
$mWeld = 0; $tWeld = 0; $totalWeld = 0;
$mRepair = 0; $tRepair = 0; $totalRepair = 0;
$mRepairRate = 0; $tRepairRate = 0; $totalRepairRate = 0;
$mCutOut = 0; $tCutOut = 0; $totalCutOut = 0;
$mNdt = 0; $tNdt = 0; $totalNdt = 0;
$mCoating = 0; $tCoating = 0; $totalCoating = 0;

$mWeldArray = array();
$mNdtArray = array();
$mRepairArray = array();

$tWeldArray = array();
$tNdtArray = array();
$tRepairArray = array();

$defectGraphArray = array();
$defectArray = $defects;
$pieces = !empty($defectArray) ? array_chunk($defectArray, ceil(count($defectArray) / 2)) : array();
$defectLeft = array();
$defectRight = array();

if(!empty($pieces) && !empty($pieces[0]) && !empty($pieces[1])){
    $defectLeft = $pieces[0];
    $defectRight = $pieces[1];
}

$defectArrayLegend = array_values($defLeg);

$startEndDate = array();
if($filterType == "weekly") {
    $rData = !empty($dateRange) ? $dateRange : '-';
    if($rData != ""){
        $expDate = explode('-', $rData);
        $start = $expDate[0];
        $end = $expDate[1];
        $startEndDate = [$start, $end];
        $period = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            new DateTime($end." 00:01")
        );

        foreach ($period as $key => $value) {
            $date = $value->format('Y-m-d');
            $dateArray[$key] = $value->format('Y-m-d');

            /*-------------------*/
            //welding
            $weldData = Yii::$app->general->getWeldDaily($date);
            //for graph
            array_push($mWeldArray,(float)$weldData['MianLineWeld']);
            array_push($tWeldArray,(float)$weldData['TieLineWeld']);

            //for table
            $mWeld += $weldData['MianLineWeld'];
            $tWeld += $weldData['TieLineWeld'];
            /*-------------------*/

            //ndt
            $ndtData = Yii::$app->general->getWeldNdtToday($date);
            //for graph
            array_push($mNdtArray,(float)$ndtData['MianLineNdt']);
            array_push($tNdtArray,(float)$ndtData['TieLineNdt']);

            //for table
            $mNdt += $ndtData['MianLineNdt'];
            $tNdt += $ndtData['TieLineNdt'];
            /*-------------------*/

            //repair
            $repairData = Yii::$app->general->getWeldRepairDaily($date);
            
            //for table
            $mRepair += $repairData['MianLineWeldRepair'];
            $tRepair += $repairData['TieLineWeldRepair'];

            //for graph
            array_push($mRepairArray,($mNdt > 0 ? (float)$mRepair * 100 / (float)$mNdt : 0));
            array_push($tRepairArray,($tNdt > 0 ? (float)$tRepair * 100 / (float)$tNdt : 0));
            /*-------------------*/


            //cut out
            $cutOutData = Yii::$app->general->getWeldCutToday($date);
            $mCutOut += $cutOutData['MianLineCut'];
            $tCutOut += $cutOutData['TieLineCut'];

            //coating
            $coatedData = Yii::$app->general->getWeldCoatedToday($date);
            $mCoating += $coatedData['MianLineCoated'];
            $tCoating += $coatedData['TieLineCoated'];
        }

        //repair rate
        $mRepairRate = number_format(($mNdt > 0 ? $mRepair * 100 / $mNdt : 0), 2);
        $tRepairRate = number_format(($tNdt > 0 ? $tRepair * 100 / $tNdt : 0), 2);


        $totalWeld = $mWeld + $tWeld;
        $totalRepair = $mRepair + $tRepair;
        $totalCutOut = $mCutOut + $tCutOut;
        $totalNdt = $mNdt + $tNdt;
        $totalCoating = $mCoating + $tCoating;
        $totalRepairRate = number_format(($totalNdt > 0 ? $totalRepair * 100 / $totalNdt : 0), 2);
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
                    <h1>PROJECT WEEKLY WELDING & COATING REPORT</h1>
                </div>
            </div>
            <div class="title-blueBG"></div>
            <div class="clearfix">
                <div class="col-md-6 col-print-6">
                    <div class="ntd-table-title clearfix">
                        <span class="text-center">WELD PRODUCTION / REPAIR / NDT SUMMARY</span>
                    </div>
                    <div class="ntd-summary-table">
                        <table class="table table-selectiondetail performed">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>Mainline</th>
                                    <th>Tie-In</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Total Welds This Week</th>
                                    <td><?php echo $mWeld; ?></td>
                                    <td><?php echo $tWeld; ?></td>
                                    <td><?php echo $totalWeld; ?></td>
                                </tr>
                                <tr>
                                    <th>Total Repairs identified This Week</th>
                                    <td><?php echo $mRepair; ?></td>
                                    <td><?php echo $tRepair; ?></td>
                                    <td><?php echo $totalRepair; ?></td>
                                </tr>
                                <tr>
                                    <th>% Repair Rate This Week</th>
                                    <td><?php echo $mRepairRate.' %'; ?></td>
                                    <td><?php echo $tRepairRate.' %'; ?></td>
                                    <td><?php echo $totalRepairRate.' %'; ?></td>
                                </tr>
                                <tr>
                                    <th>Welds Cut Out This Week</th>
                                    <td><?php echo $mCutOut; ?></td>
                                    <td><?php echo $tCutOut; ?></td>
                                    <td><?php echo $totalCutOut; ?></td>
                                </tr>
                                <tr>
                                    <th>Welds NDT This Week</th>
                                    <td><?php echo $mNdt; ?></td>
                                    <td><?php echo $tNdt; ?></td>
                                    <td><?php echo $totalNdt; ?></td>
                                </tr>
                                <tr>
                                    <th>Total Coating This Week</th>
                                    <td><?php echo $mCoating; ?></td>
                                    <td><?php echo $tCoating; ?></td>
                                    <td><?php echo $totalCoating; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 col-print-6">
                    <div class="ntd-table-title clearfix">
                        <span class="text-center"><?= date('d-M-y',strtotime($start)).'<span class="ml-2 mr-2"> to </span>'.date('d-M-y',strtotime($end)); ?></span>
                    </div>
                    <div class="ntd-summary-table">
                        <table class="table table-selectiondetail performed">
                            <thead>
                                <tr>
                                    <th colspan="4">GENERAL INFORMATION</th>
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
                        <span class="text-center">REPAIR BY DEFECT TYPE THIS WEEK</span>
                    </div>
                </div>
            </div>

            <div class="clearfix">
                <div class="col-md-6 col-print-6">
                    <div class="ntd-summary-table">
                        <table class="table table-selectiondetail performed">
                            <thead>
                                <tr>
                                    <th>Defect</th>
                                    <th>Qty</th>
                                    <th>% of Repair Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($defectLeft as $defect){ ?>
                                <tr>
                                    <th><?php echo $defect; ?></th>
                                    <td>
                                        <?php
                                            $defData = Yii::$app->general->getRepairByDefectType($defect, $startEndDate);
                                            echo $defData;
                                            $defDataRepair = $totalRepair > 0 ? ($defData / $totalRepair) * 100 : 0;
                                            array_push($defectGraphArray,$defDataRepair);
                                        ?>
                                    </td>
                                    <td><?php echo $defDataRepair.' %'; ?></td>
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
                                    <th>Defect</th>
                                    <th>Qty</th>
                                    <th>% of Repair Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($defectRight as $defect){ ?>
                                <tr>
                                    <th><?php echo $defect; ?></th>
                                    <td>
                                        <?php
                                            $defData = Yii::$app->general->getRepairByDefectType($defect, $startEndDate);
                                            echo $defData;
                                            $defDataRepair = $totalRepair > 0 ? ($defData / $totalRepair) * 100 : 0;
                                            array_push($defectGraphArray,$defDataRepair);
                                        ?>
                                    </td>
                                    <td><?php echo $defDataRepair.' %'; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php if(!empty($defectArray)){ ?>
            <div class="clearfix">
                <div class="col-md-12">
                    <div class="ntd-summary-table">
                        <?php foreach($defectArray as $sDef){ ?>
                        <span><?php echo $sDef; ?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
            
            <div class="title-blueBG">
                <?php
                    if($filterType == "weekly" && !empty($dateArray)){
                        echo Highcharts::widget([
                            'options' => [
                                'title' => ['text' => 'Repairs by Type This Week'],
                                'chart' => ['type' => 'column'],
                                'xAxis' => [
                                    'categories' => $defectArrayLegend
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
                                        'data' => $defectGraphArray,
                                        'tooltip' => [
                                            'valueSuffix' => ' %'
                                        ]
                                    ],
                                ],
                                'credits' => ['enabled' => false],
                            ]
                        ]);

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
                                            'text' => '% Repair',
                                        ],
                                        'labels' => [
                                            'format' => '{value} %',
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
                                        'tooltip' => [
                                            'valueSuffix' => ' %'
                                        ]
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
                                            'text' => '% Repair',
                                        ],
                                        'labels' => [
                                            'format' => '{value} %',
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
                                        'name' => '% Repair',
                                        'type' => 'spline',
                                        'yAxis' => 2,
                                        'data' => $tRepairArray,
                                        'tooltip' => [
                                            'valueSuffix' => ' %'
                                        ]
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
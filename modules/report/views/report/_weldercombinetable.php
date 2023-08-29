<?php
use miloschuman\highcharts\Highcharts;

$currentDate = !empty($date) ? $date : date('Y-m-d');
$lastYearDate = date('Y-m-d',strtotime("-1 year",strtotime($currentDate)));

//get first and last day of week
// $date = new DateTime();
// $date->modify('2018W10');
// echo $date->format('Y-m-d');
// echo "<br/>";
// $date->modify('2018W10 +6 days');
// echo $date->format('Y-m-d');

//get total week between two dates
function datediffInWeeks($date1, $date2){
    if($date1 > $date2) return datediffInWeeks($date2, $date1);
    $first = DateTime::createFromFormat('Y-m-d', $date1);
    $second = DateTime::createFromFormat('Y-m-d', $date2);
    return floor($first->diff($second)->days/7);
}

$startTime = strtotime($lastYearDate);
$endTime = strtotime($currentDate);

$weeks = array();
$i = 0;
while ($startTime < $endTime) {  
    $weeks[$i]['week'] = date('W', $startTime);
    $weeks[$i]['year'] = date('Y', $startTime);
    $startTime += strtotime('+1 week', 0);
    $i++;
}

$weekArray = array();
$weldArray = array();
$repairArray = array();

$samplDate = array();
$pastWeek = 1;
foreach($weeks as $key => $val){

    $dateList = getStartAndEndDate($val['week'], $val['year']);
    $samplDate[] = $dateList;

    $allWeld = getWeldingData($dateList['first'], $dateList['last']);

    $weekArray[] = $val['week'].' - '.$val['year'];
    $weldArray[] = $allWeld['TotalWeldLength'];
    $repairArray[] = $allWeld['rapairRate'];
    $repairOverAllArray[] = $allWeld['overAllRate'];

    $pastWeek++;
}

function getStartAndEndDate($week, $year){
    if(strlen($week) == 1) $week = "0".$week;
    $date = new DateTime();
    $date->modify($year.'W'.$week);
    $firstData = $date->format('Y-m-d');

    $date->modify($year.'W'.$week.' +6 days');
    $lastData = $date->format('Y-m-d');
    return array('first'=>$firstData,'last'=>$lastData);
}

function getWeldingData($start,$end){
    $TotalWeldCount = 0; $TotalWeldLength = 0; $overAllWeld = 0;
    $Welding = \app\models\Welding::find()->select(['welding.root_os','welding.root_ts','welding.hot_os','welding.hot_ts','welding.fill_os','welding.fill_ts','welding.cap_os','welding.cap_ts','pipe.od'])
            ->leftJoin('pipe','welding.pipe_number = pipe.pipe_number AND welding.project_id = pipe.project_id AND welding.is_active = pipe.is_active')
            ->andWhere(['between', 'welding.date', $start, $end])
            ->active()->asArray()->all();
    
    $overAllWeld = count($Welding);
    
    if(!empty($Welding)){
        foreach($Welding as $Weld){
            $WelderCount = 0;
            foreach($Weld as $key => $v){
                if($v != "" && $v != "Please Select" && $key != "od"){
                    $WelderCount = $WelderCount + 1;
                    $TotalWeldCount = $TotalWeldCount + 1;
                }
            }            
            
            $TotalWeldLength = $TotalWeldLength + $WelderCount * 3.14 * $Weld['od'];
        }
    }

    $TotalWeldRepair = 0; $RepairLength = 0; $overAllRepair = 0;
    $WeldRepairData = \app\models\Weldingrepair::find()->select(['welding_repair.welder','pipe.od'])
            ->leftJoin('welding','welding_repair.weld_number=welding.weld_number AND welding.project_id = welding_repair.project_id AND welding.is_active = welding_repair.is_active')
            ->leftJoin('pipe','welding.pipe_number = pipe.pipe_number AND welding.project_id = pipe.project_id AND welding.is_active = pipe.is_active')
            ->andWhere(['between', 'welding_repair.date', $start, $end])
            ->active()
            ->asArray()
            ->all();
    
    $overAllRepair = count($WeldRepairData);

    if(!empty($WeldRepairData)){ 
        foreach($WeldRepairData as $repair){
            $TotalWeldRepair = $TotalWeldRepair + 1;
            $RepairLength = $RepairLength + (3.14 * $repair['od']);
        }
    }

    $rapairRate = !empty($TotalWeldLength) ? ($RepairLength/$TotalWeldLength)*100 : 0;
    $overall = $overAllWeld > 0 ? ($overAllRepair / $overAllWeld) : 0;
    return ['TotalWeldLength' => $TotalWeldLength, 'rapairRate' => $rapairRate, 'overAllRate' => $overall];
}

?>
<div class="table-responsive table-welder">
    <table class="table table-bordered">
        <thead>                                                
            <tr class="tr-second">
                <th>Name</th>
                <th>Number of welds done</th>
                <th>Number of Repairs</th>
                <th>Total Weld Length Done (mm)</th>
                <th>Total Length of Repair (mm)</th>
                <th>% of Repairs</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!empty($data)){
                $totalWeldLength = $totalRepairLength = 0;
                foreach($data as $key => $val){
                    $totalWeldLength += !empty($val['WeldingLength']) ? $val['WeldingLength'] : 0;
                    $totalRepairLength += !empty($val['RepairLength']) ? $val['RepairLength'] : 0;
            ?>
            <tr>
                <td><?= !empty($val['WelderName']) ? $val['WelderName'] : '-'; ?></td>
                <td><?= !empty($val['TotalWeldCount']) ? $val['TotalWeldCount'] : 0; ?></td>
                <td><?= !empty($val['TotalWeldRepair']) ? $val['TotalWeldRepair'] : 0; ?></td>
                <td><?= !empty($val['WeldingLength']) ? $val['WeldingLength'] : 0; ?></td>
                <td><?= !empty($val['RepairLength']) ? $val['RepairLength'] : 0; ?></td>
                <td><?= !empty($val['RepairRate']) ? number_format($val['RepairRate'],2, '.','').' %' : '0 %'; ?></td>
            </tr>
            <?php } ?>
            <tr class="tr-last">
                <td></td>
                <td colspan="2" align="center">TOTAL</td>
                <td><?= $totalWeldLength; ?></td>
                <td><?= $totalRepairLength; ?></td>
                <td><?= !empty($totalWeldLength) ? number_format((float)($totalRepairLength / $totalWeldLength) * 100, 2, '.', '')." %" : "0 %"; ?></td>
            </tr>
            <?php
            } else {
            ?>
            <tr>
                <td colspan="6" align="center">No Data Found</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<div class="combine-graph">
    <?php
    //graph for welder analysis
    echo Highcharts::widget([
        'options' => [
            'chart' => [
                'zoomType' => 'xy'
            ],
            'title' => [
                'text' => 'Welder Analysis'
            ],
            'xAxis' => [
                [
                    'categories' => $weekArray,
                    'crosshair' => true
                ]
            ],
            'yAxis' => [
                [
                    'gridLineWidth' => 0,
                    'title' => [
                        'text' => '% Repair Weekly',
                    ],
                    'labels' => [
                        'format' => '{value} %',
                    ],
                    'opposite' => true
                ],
                [
                    'gridLineWidth' => 0,
                    'title' => [
                        'text' => '% Repair Overall',
                    ],
                    'labels' => [
                        'format' => '{value} %',
                    ],
                    'opposite' => true
                ],
                [
                    'title' => [
                        'text' => 'Length'
                    ],
                    'labels' => [
                        'format' => '{value} mm',
                    ],
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
                    'name' => 'Weekly Repair Rate',
                    'type' => 'spline',                    
                    'data' => $repairArray,
                    'tooltip' => [
                        'valueSuffix' => ' %'
                    ]
                ],
                [
                    'name' => 'Overall Repair Rate',
                    'type' => 'spline',
                    'yAxis' => 1,
                    'data' => $repairOverAllArray,
                    'tooltip' => [
                        'valueSuffix' => ' %'
                    ]
                ],
                [
                    'name' => 'Commulative Length Welded',
                    'type' => 'spline',
                    'yAxis' => 2,
                    'data' => $weldArray,
                    'tooltip' => [
                        'valueSuffix' => ' mm'
                    ]                                       
                ],
            ],
            'credits' => ['enabled' => false],
        ]
    ]);
    ?>
</div>
<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use miloschuman\highcharts\Highcharts;
?>
<section id="basic-form-layouts">
		<div class="row">
			<div class="col-sm-12">
				<div class="content-header"></div>
			</div>
		</div>
		<div class="row match-height">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title" id="basic-layout-form">
                            PRODUCTION REPORT
                            <button class="btn btn-raised btn-white btn-min-width mr-1 mb-1 black pull-right" onclick="printDiv();">Print Report</button>
                        </h4>
					</div>
					<div class="card-body">	
                        <div class="production-data" id="print-body">
                            <div class="table-summary">
                                <div class="clearfix">
                                    <div class="logo-appro">
                                    <?= \yii\helpers\Html::img(Yii::$app->general->logo(), ['alt'=>'some', 'class'=>'appro-logo']);?>             </div>
                                        <div class="appro-title">
                                            <h1>Welder Repair Report</h1>
                                        </div>
                                    </div>
                                    <div class="title-blueBG"></div>
                                    <div class="ntd-summary-main">
                                        <div class="">
                                            <div class="ntd-table-title clearfix">
                                                <span class="pull-left ml-1">REPORT DATE :<?=date('Y-m-d');?></span>
                                                <span class="pull-right ml-1">Welder: 
                                                <?= Html::dropDownList('welder',!empty($_GET['welder_name'])?$_GET['welder_name']:"",Yii::$app->general->TaxonomyDrop(7),['prompt'=>'','id'=>'change-welder']); ?>
                                                </span>
                                            </div>
                                            <div class="ntd-summary-table">                                           
                                                <table class="table table-selectiondetail performed">                                                   
                                                    <tbody>
                                                        <tr>
                                                            <td style="padding: 87px;">
                                                                <p style=" text-align: center; font-size: 29px;font-weight: 700;">Welder Repair Rate</p> 
                                                                <p style="font-size: 60px;text-align: center;font-weight: 500;"><?= !empty($data['RepairRate'])?number_format($data['RepairRate'],2):0;;?>%</p>
                                                            </td>
                                                            <td style="padding: 87px;">
                                                                <p style="font-size: 34px;text-align: center;font-weight: 500;"> Weld : <?= !empty($data['TotalWeldCount'])?$data['TotalWeldCount']:0;;?></p>
                                                                <p style=" font-size: 35px;text-align: center;font-weight: 500;">Repair : <?= !empty($data['TotalWeldRepair'])?$data['TotalWeldRepair']:0;;?></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">
                                                            <?php
                                                          // print_r($data['mainline']);die;     
                                                                  echo Highcharts::widget([
                                                                    'options' => [
                                                                        'chart'=> [
                                                                            'type'=> 'bar'
                                                                        ],
                                                                        'title'=> [
                                                                            'text'=> 'Weld Bead Defect Positions'
                                                                        ],
                                                                        'xAxis'=> [
                                                                            'categories'=> $defectPosition
                                                                        ],
                                                                        'yAxis'=> [
                                                                            'min'=> 0,
                                                                            'title'=> [
                                                                                'text'=> ''
                                                                            ]
                                                                        ],
                                                                        'legend'=> [
                                                                            'reversed'=> true
                                                                        ],
                                                                        'plotOptions'=> [
                                                                            'series'=> [
                                                                                'stacking'=> 'normal'
                                                                            ]
                                                                        ],
                                                                        'credits'=> [
                                                                            'enabled'=> false
                                                                        ],
                                                                        'series'=> $defectPosArray,
                                                                    ]
                                                                    
                                                                ]);
                                                            
                                                            ?>     
                                                            </td>
                                                            <!-- <td> -->
                                                            <?php
                                                                
                                                                // echo Highcharts::widget([
                                                                //     'options' => [
                                                                //         'title' =>  ['text' => 'Welder Progression'],
                                                                //         'chart' => ['type' => 'line'],
                                                                //         'xAxis' => [
                                                                //             'categories' => [
                                                                //                         'Jan',
                                                                //                         'Feb',
                                                                //                         'Hot OS',
                                                                //                         'Hot TS',
                                                                //                         'Fill OS',
                                                                //                         'Fill TS',
                                                                //                         'Cap OS',
                                                                //                         'Cap TS',
                                                                //             ]
                                                                //         ],
                                                                //         'yAxis' => [
                                                                //             'title' => ['text' => 'Number Of Defects Position Count']
                                                                //         ],
                                                                //         'legend'=>[
                                                                //             'layout'=>'vertical',
                                                                //             'align'=>'right',
                                                                //             'verticalAlign'=>'middle'
                                                                //         ],
                                                                //         'series'=>[
                                                                //             ['name'=>'Number of welds',
                                                                //             'data'=>[43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
                                                                //             ],
                                                                //             ['name'=>'Number of Welds Repair',
                                                                //             'data'=> [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
                                                                //             ]
                                                                //         ],
                                                                //         'credits' => ['enabled' => false],
                                                                //         'responsive'=>[
                                                                //             'rules'=>[
                                                                //                 'condition'=>['maxWidth'=>500],
                                                                //                 'chartOptions'=>[
                                                                //                     'legend'=>[
                                                                //                         'layout'=>'horizontal',
                                                                //                         'align'=>'center',
                                                                //                         'verticalAlign'=>'bottom'
                                                                //                     ]
                                                                //                 ]
                                                                //             ]
                                                                //         ]
                                                                //     ]
                                                                // ]);
                                                            
                                                        ?>
                                                            <!-- </td> -->
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <p>Remark</p>
                                                                <p style="padding: 48px;"></p>
                                                            </td>
                                                            <td>
                                                                <p align="left">Way Forward</p>
                                                                <p style="padding: 48px;"></p>
                                                            </td>
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
				            </div>
		            	</div>
                    </div>
		</section>